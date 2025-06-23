<?php
session_start();
include('conn.php');
include('function.php');
include 'header.php';
check_login();

// Lấy ID người dùng từ URL hoặc sử dụng ID của người đăng nhập nếu không có
$id_nguoidung = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_nguoidung'];

// Truy vấn để lấy thông tin người dùng
$query_user = "SELECT * FROM nguoidung WHERE id_nguoidung = ?";
$stmt_user = $conn->prepare($query_user);

if (!$stmt_user) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}

$stmt_user->bind_param("i", $id_nguoidung);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

// Kiểm tra nếu có kết quả trả về
if ($result_user->num_rows == 0) {
    echo "Người dùng không tồn tại.";
    exit;
}

$user = $result_user->fetch_assoc();

// Truy vấn để lấy 5 bài đăng mới nhất của người dùng
$query_latest_posts = "SELECT * FROM baidang WHERE id_nguoidung = ? ORDER BY thoigiantao DESC LIMIT 5";
$stmt_latest_posts = $conn->prepare($query_latest_posts);

if (!$stmt_latest_posts) {
    die("Lỗi chuẩn bị truy vấn mới nhất: " . $conn->error);
}

$stmt_latest_posts->bind_param("i", $id_nguoidung);
$stmt_latest_posts->execute();
$result_latest_posts = $stmt_latest_posts->get_result();

// Truy vấn để lấy 5 bài đăng có nhiều bình luận nhất
$query_most_commented_posts = "
    SELECT baidang.*, COUNT(binhluan.id_binhluan) AS num_comments
    FROM baidang
    LEFT JOIN binhluan ON baidang.id_baidang = binhluan.id_baidang
    WHERE baidang.id_nguoidung = ?
    GROUP BY baidang.id_baidang
    ORDER BY num_comments DESC
    LIMIT 5
";
$stmt_most_commented_posts = $conn->prepare($query_most_commented_posts);

if (!$stmt_most_commented_posts) {
    die("Lỗi chuẩn bị truy vấn bình luận nhiều nhất: " . $conn->error);
}

$stmt_most_commented_posts->bind_param("i", $id_nguoidung);
$stmt_most_commented_posts->execute();
$result_most_commented_posts = $stmt_most_commented_posts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Người Dùng</title>
    <style>
body {
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

.user-profile {
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.user-profile .avatar {
    text-align: center;
    margin-bottom: 20px;
}

.user-profile .avatar img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #007bff;
}

.user-profile h1 {
    margin-top: 0;
    text-align: center;
    font-size: 2em;
    color: #007bff;
}

.user-profile p {
    font-size: 1em;
    line-height: 1.5;
    margin: 10px 0;
}

.update-profile-btn {
    display: inline-block;
    padding: 12px 24px;
    margin-top: 20px;
    background-color: #007bff;
    color: #ffffff;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
    font-size: 1em;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.update-profile-btn:hover {
    background-color: #0056b3;
}

.posts {
    margin-top: 30px;
}

.posts h2 {
    font-size: 1.5em;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.post {
    margin-bottom: 20px;
    padding: 15px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease; /* Thêm hiệu ứng chuyển tiếp */
}

.post:hover {
    background: #f0f8ff; /* Thay đổi màu nền khi hover */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Tăng bóng đổ khi hover */
    transform: translateY(-5px); /* Di chuyển lên một chút khi hover */
}

.post h3 {
    margin: 0;
    font-size: 1.2em;
    color: #007bff;
}

.post p {
    margin: 10px 0;
    color: #555;
    font-size: 0.9em;
    line-height: 1.4;
}

.post a {
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.post a:hover {
    color: #0056b3;
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="user-profile">
        <?php if ($user): ?>
            <div class="avatar">
                <!-- Hiển thị ảnh đại diện -->
                <?php if (!empty($user['img']) && file_exists('../uploads/' . $user['img'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($user['img']); ?>" alt="Ảnh Đại Diện">
                <?php else: ?>
                    <img src="../uploads/default_avatar.png" alt="Ảnh Đại Diện Mặc Định">
                <?php endif; ?>
            </div>
            <h1><?php echo htmlspecialchars($user['fullname']); ?></h1>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Ngày Sinh: <?php echo htmlspecialchars($user['ngaysinh']); ?></p>
            <p>Địa Chỉ: <?php echo htmlspecialchars($user['diachi']); ?></p>
            <p>Giới Tính: <?php echo htmlspecialchars($user['gioitinh']); ?></p>

            <?php if ($id_nguoidung == $_SESSION['id_nguoidung']): ?>
                <a href="update_profile.php" class="update-profile-btn">Cập Nhật Hồ Sơ</a>
            <?php endif; ?>

        <?php else: ?>
            <p>Người dùng không tồn tại.</p>
        <?php endif; ?>

        <!-- Hiển thị các bài đăng mới nhất -->
        <div class="posts">
            <h2>Bài Đăng Mới Nhất</h2>
            <?php if ($result_latest_posts->num_rows == 0): ?>
                <p>Người dùng này chưa có bài đăng nào.</p>
            <?php else: ?>
                <?php while ($post = $result_latest_posts->fetch_assoc()): ?>
                    <div class="post">
                        <h3><a href="chitietbaidang.php?id=<?php echo htmlspecialchars($post['id_baidang']); ?>"><?php echo htmlspecialchars($post['tieude']); ?></a></h3>
                        <p><?php echo nl2br((substr($post['mota'], 0, 100)) . (strlen($post['mota']) > 100 ? '...' : '')); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Hiển thị các bài đăng nhiều bình luận nhất -->
        <div class="posts">
            <h2>Bài Đăng Nhiều Bình Luận Nhất</h2>
            <?php if ($result_most_commented_posts->num_rows == 0): ?>
                <p>Người dùng này chưa có bài đăng nào có nhiều bình luận.</p>
            <?php else: ?>
                <?php while ($post = $result_most_commented_posts->fetch_assoc()): ?>
                    <div class="post">
                        <h3><a href="chitietbaidang.php?id=<?php echo htmlspecialchars($post['id_baidang']); ?>"><?php echo htmlspecialchars($post['tieude']); ?></a></h3>
                        <p><?php echo nl2br((substr($post['mota'], 0, 100)) . (strlen($post['mota']) > 100 ? '...' : '')); ?></p>
                        <p>Số Bình Luận: <?php echo htmlspecialchars($post['num_comments']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
