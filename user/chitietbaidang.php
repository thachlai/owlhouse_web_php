<?php
session_start();
include('conn.php');
include('function.php');
include 'header.php';


// Lấy ID bài đăng từ URL
$id_baidang = isset($_GET['id']) ? intval($_GET['id']) : 0;

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

// Xử lý bình luận
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    check_login();
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

// Lấy các bình luận với trangthai = 0
$query_comments = "SELECT b.*, u.fullname, u.email, u.img as avatar 
                   FROM binhluan b 
                   JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung 
                   WHERE b.id_baidang = '$id_baidang' AND b.trangthai = 0 
                   ORDER BY b.thoigiantao DESC";
$comments_result = mysqli_query($conn, $query_comments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Bài Đăng</title>
    <style>
        /* Styles are unchanged */
        body{
            background-color: #f4f4f4;
        }
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
    </style>
</head>
<body>
    <div class="post-content">
        <div class="post-header">
            <a href="hoso.php?id=<?php echo htmlspecialchars($baidang['id_nguoidung']); ?>" class="avatar">
                <?php
                // Đường dẫn đến ảnh đại diện của người tạo bài đăng
                $avatar_path = '../uploads/' . htmlspecialchars($baidang['avatar']);
                ?>
                <img src="<?php echo $avatar_path; ?>" alt="Avatar">
            </a>
            <div class="user-info">
                <p>
                    <a href="hoso.php?id=<?php echo htmlspecialchars($baidang['id_nguoidung']); ?>" class="username">
                        <?php echo htmlspecialchars($baidang['fullname']); ?>
                    </a>
                    (<?php echo htmlspecialchars($baidang['email']); ?>)
                </p>
                <p class="role">
    <?php 
    switch ($baidang['quyen']) {
        case 1:
            echo 'Admin';
            break;
        case 2:
            echo 'Nhà sinh vật học';
            break;
        default:
            echo 'Người dùng';
    }
    ?>
</p>    
            </div>
        </div>

        <h1 class="post-title"><?php echo htmlspecialchars($baidang['tieude']); ?></h1> <!-- Hiển thị tiêu đề bài đăng -->

        <!-- Hiển thị mô tả bài đăng -->
        <div class="post-description">
            <div><?php echo nl2br(($baidang['mota'])); ?></div>
        </div>

        <?php while ($img = mysqli_fetch_assoc($imgs_result)): ?>
            <img src="<?php echo htmlspecialchars($img['anh']); ?>" alt="Image">
        <?php endwhile; ?>

    </div>

    <div class="comments-section">
        <h2>Bình Luận</h2>
        <?php while ($comment = mysqli_fetch_assoc($comments_result)): ?>
            <div class="comment">
                <a href="hoso.php?id=<?php echo htmlspecialchars($comment['id_nguoidung']); ?>" class="avatar">
                    <?php
                    // Đường dẫn đến ảnh đại diện của người bình luận
                    $comment_avatar_path = '../uploads/' . htmlspecialchars($comment['avatar']);
                    ?>
                    <img src="<?php echo $comment_avatar_path; ?>" alt="Avatar">
                </a>
                <div class="comment-details">
                    <p class="comment-author">
                        <a href="hoso.php?id=<?php echo htmlspecialchars($comment['id_nguoidung']); ?>" class="username">
                            <?php echo htmlspecialchars($comment['fullname']); ?>
                        </a>
                        (<?php echo htmlspecialchars($comment['email']); ?>)
                    </p>
                    <p class="comment-date"><?php echo $comment['thoigiantao']; ?></p>
                    <p class="comment-content"><?php echo ($comment['binhluan']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="comment-form">
            <h3>Thêm Bình Luận</h3>
            <form method="POST" action="">
                <textarea id="binhluan" name="comment" rows="4" placeholder="Viết bình luận của bạn ở đây..."></textarea><br>
                <button type="submit" >Gửi Bình Luận</button>
            </form>
        </div>
    </div>
</body>
</html>
<script>
    CKEDITOR.replace('binhluan');
</script>
