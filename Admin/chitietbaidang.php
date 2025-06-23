<?php
session_start();
include('conn.php');
include('function.php');
include 'header.php';
check_login();
check_admin();

// Lấy ID bài đăng từ URL
$id_baidang = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Cập nhật trạng thái bài đăng
        $new_status = isset($_POST['post_status']) ? intval($_POST['post_status']) : 0;
        $query = "UPDATE baidang SET trangthai = '$new_status' WHERE id_baidang = '$id_baidang'";
        mysqli_query($conn, $query);

        // Cập nhật trạng thái bình luận
        if (isset($_POST['comment_status'])) {
            foreach ($_POST['comment_status'] as $comment_id => $status) {
                $status = intval($status);
                $query = "UPDATE binhluan SET trangthai = '$status' WHERE id_binhluan = '$comment_id'";
                mysqli_query($conn, $query);
            }
        }
        
        // Thay thế header() bằng việc đặt biến session hoặc một tham số query
        $_SESSION['message'] = 'Cập nhật thành công!';
        echo '<script>
                window.onload = function() {
                    window.location.href = "chitietbaidang.php?id=' . $id_baidang . '";
                };
              </script>';
        exit();
    }


    // Xử lý thêm bình luận
    if (isset($_POST['comment'])) {
        if (isset($_SESSION['id_nguoidung'])) {
            $comment_content = $_POST['comment'];
            $user_id = $_SESSION['id_nguoidung'];

            // Làm sạch đầu vào
            $comment_content = mysqli_real_escape_string($conn, $comment_content);

            // Thêm bình luận vào cơ sở dữ liệu
            $query_comment = "INSERT INTO binhluan (id_baidang, id_nguoidung, binhluan) VALUES ('$id_baidang', '$user_id', '$comment_content')";
            mysqli_query($conn, $query_comment);
        } else {
            echo "<script>alert('Bạn cần đăng nhập để bình luận.');</script>";
        }
    }
}

// Lấy thông tin bài đăng
$query = "SELECT b.*, u.fullname, u.email, u.img as avatar, u.quyen 
          FROM baidang b 
          JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung 
          WHERE b.id_baidang = '$id_baidang'";
$result = mysqli_query($conn, $query);
$baidang = mysqli_fetch_assoc($result);

// Lấy các ảnh phụ của bài đăng
$query_imgs = "SELECT * FROM anh_baidang WHERE id_baidang = '$id_baidang'";
$imgs_result = mysqli_query($conn, $query_imgs);

// Lấy các bình luận
$query_comments = "SELECT b.*, u.fullname, u.email, u.img as avatar FROM binhluan b JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung WHERE b.id_baidang = '$id_baidang' ORDER BY b.thoigiantao DESC";
$comments_result = mysqli_query($conn, $query_comments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Bài Đăng</title>
    <style>
        .post-content, .comments-section {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .post-header .avatar {
            margin-right: 15px;
        }
        .post-header .avatar img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .post-header .user-info {
            flex: 1;
        }
        .post-header .user-info p {
            margin: 0;
        }
        .post-header .user-info .role {
            color: #777;
        }
        .post-title {
            margin: 10px 0;
        }
        .post-content img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
            display: block;
        }
        .comment {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
        }
        .comment .avatar {
            margin-right: 15px;
        }
        .comment .avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .comment .comment-details {
            flex: 1;
        }
        .comment-author {
            font-weight: bold;
        }
        .comment-date {
            color: #777;
            font-size: 0.9em;
        }
        .comment-content {
            margin-top: 10px;
        }
        .comment-form {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .comment-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .comment-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .comment-form button:hover {
            background-color: #45a049;
        }
        .comment-status {
            display: flex;
            align-items: center;
        }
        .comment-status select {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="post-content">
        <div class="post-header">
            <a href="../user/hoso.php?id=<?php echo htmlspecialchars($baidang['id_nguoidung']); ?>" class="avatar">
                <?php
                // Đường dẫn đến ảnh đại diện của người tạo bài đăng
                $avatar_path = '../uploads/' . htmlspecialchars($baidang['avatar']);
                ?>
                <img src="<?php echo $avatar_path; ?>" alt="Avatar">
            </a>
            <div class="user-info">
                <p>
                    <a href="../user/hoso.php?id=<?php echo htmlspecialchars($baidang['id_nguoidung']); ?>" class="username">
                        <?php echo htmlspecialchars($baidang['fullname']); ?>
                    </a>
                    (<?php echo htmlspecialchars($baidang['email']); ?>)
                </p>
                <p class="role"><?php echo ($baidang['quyen'] == 1) ? 'Nhà sinh vật học' : 'Người dùng'; ?></p>
            </div>
        </div>

        <h1 class="post-title"><?php echo htmlspecialchars($baidang['tieude']); ?></h1> <!-- Hiển thị tiêu đề bài đăng -->
        
        <p><?php echo nl2br(($baidang['mota'])); ?></p> <!-- Hiển thị mô tả bài đăng -->

        <?php while ($img = mysqli_fetch_assoc($imgs_result)): ?>
            <img src="<?php echo htmlspecialchars($img['anh']); ?>" alt="Image">
        <?php endwhile; ?>

        <?php if (isset($_SESSION['quyen']) && $_SESSION['quyen'] == 1): ?>
            <!-- Form cập nhật trạng thái bài đăng và bình luận -->
            <form method="POST" action="">
                <div>
                    <label for="post_status">Trạng thái bài đăng:</label>
                    <select name="post_status" id="post_status">
                        <option value="0" <?php echo $baidang['trangthai'] == 0 ? 'selected' : ''; ?>>Công khai</option>
                        <option value="1" <?php echo $baidang['trangthai'] == 1 ? 'selected' : ''; ?>>Ẩn</option>
                    </select>
                </div>

                <div class="comments-section">
                    <h2>Bình luận</h2>
                    <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
                        <div class="comment">
                            <a href="../user/hoso.php?id=<?php echo htmlspecialchars($comment['id_nguoidung']); ?>" class="avatar">
                                <?php
                                // Đường dẫn đến ảnh đại diện của người bình luận
                                $comment_avatar_path = '../uploads/' . htmlspecialchars($comment['avatar']);
                                ?>
                                <img src="<?php echo $comment_avatar_path; ?>" alt="Avatar">
                            </a>
                            <div class="comment-details">
                                <p class="comment-author">
                                    <a href="../user/hoso.php?id=<?php echo htmlspecialchars($comment['id_nguoidung']); ?>">
                                        <?php echo htmlspecialchars($comment['fullname']); ?>
                                    </a>
                                </p>
                                <p class="comment-date"><?php echo htmlspecialchars($comment['thoigiantao']); ?></p>
                                <p class="comment-content"><?php echo nl2br(($comment['binhluan'])); ?></p>
                                <div class="comment-status">
                                    <label for="status_<?php echo $comment['id_binhluan']; ?>">Trạng thái:</label>
                                    <select name="comment_status[<?php echo $comment['id_binhluan']; ?>]" id="status_<?php echo $comment['id_binhluan']; ?>">
                                        <option value="0" <?php echo $comment['trangthai'] == 0 ? 'selected' : ''; ?>>Mở</option>
                                        <option value="1" <?php echo $comment['trangthai'] == 1 ? 'selected' : ''; ?>>Khóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <button type="submit" name="update">Cập Nhật</button>
            </form>
        <?php endif; ?>

        <div class="comment-form">
            <h2>Thêm bình luận</h2>
            <form method="POST" action="">
                <textarea id="editor"name="comment" rows="4" placeholder="Viết bình luận của bạn..."></textarea>
                <button type="submit">Gửi</button>
            </form>
        </div>
    </div>
</body>
</html>
<script>
    CKEDITOR.replace('editor');
</script>