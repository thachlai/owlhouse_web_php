<?php
session_start();
include('conn.php');
include('function.php');
// check_login();
include('header.php');

// Xác định số lượng bài đăng hiển thị trên mỗi trang
$posts_per_page = 10; // Thay đổi số lượng bài đăng trên mỗi trang nếu cần

// Lấy số trang hiện tại từ URL, mặc định là trang 1 nếu không có
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $posts_per_page;

// Khởi tạo biến để lưu trữ điều kiện tìm kiếm và lọc
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$role_filter = isset($_GET['role']) ? intval($_GET['role']) : -1;

// Tạo điều kiện tìm kiếm và lọc
$search_condition = $search_term ? " AND b.tieude LIKE '%$search_term%'" : '';
$role_condition = $role_filter >= 0 ? " AND u.quyen = $role_filter" : '';

// Truy vấn để lấy các bài đăng theo điều kiện tìm kiếm và lọc
$query = "SELECT b.id_baidang, b.tieude, b.mota, b.thoigiantao, u.id_nguoidung, u.fullname, u.email, u.quyen, u.img as avatar
          FROM baidang b
          JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung
          WHERE b.trangthai = 0 $search_condition $role_condition
          ORDER BY b.thoigiantao DESC
          LIMIT $start_from, $posts_per_page";
$result = mysqli_query($conn, $query);

// Tính tổng số bài đăng để phân trang
$query_total = "SELECT COUNT(*) as total_posts FROM baidang b
                JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung
                WHERE b.trangthai = 0 $search_condition $role_condition";
$result_total = mysqli_query($conn, $query_total);
$total_posts = mysqli_fetch_assoc($result_total)['total_posts'];
$total_pages = ceil($total_posts / $posts_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diễn Đàn</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .post-list {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .post-list h1 {
        font-size: 2em;
        margin-bottom: 20px;
        color: #333;
        text-align: center;
    }

    .search-filter {
        margin-bottom: 20px;
        text-align: center;
    }

    .search-filter form {
        display: inline-block;
    }

    .search-filter input[type="text"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
        margin-right: 10px;
    }

    .search-filter select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-right: 10px;
    }

    .search-filter button {
        padding: 10px 15px;
        border: none;
        background-color: #007bff;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    .search-filter button:hover {
        background-color: #0056b3;
    }

    .post-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
        padding: 15px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s, box-shadow 0.3s;
        border: 1px;
    }

    .post-item:hover {
        background-color:#97eab4;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        transform: scale(1.05);
    }

    .post-item .avatar {
        margin-right: 15px;
    }

    .post-item .avatar img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }

    .post-item .content {
        flex: 1;
    }

    .post-item h2 {
        margin-top: 0;
        font-size: 1.5em;
        color: #333;
    }

    .post-item h2 a {
        text-decoration: none;
        color: #007bff;
        transition: color 0.3s;
    }

    .post-item h2 a:hover {
        color: #0056b3;
    }

    .post-item p {
        margin: 10px 0;
        color: #555;
    }

    .post-item .post-meta {
        font-size: 0.9em;
        color: #777;
    }

    .pagination {
        text-align: center;
        margin: 20px 0;
    }

    .pagination a {
        display: inline-block;
        margin: 0 5px;
        text-decoration: none;
        color: #007bff;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 1em;
        transition: background-color 0.3s, color 0.3s;
    }

    .pagination a:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    .pagination a.active {
        background-color: #007bff;
        color: #ffffff;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <div class="post-list">
        <h1>Diễn Đàn</h1>
        <div class="search-filter">
            <form method="GET" action="diendan.php">
                <input type="text" name="search" placeholder="Tìm kiếm theo tiêu đề" value="<?php echo htmlspecialchars($search_term); ?>">
                <select name="role">
                    <option value="-1" <?php echo $role_filter == -1 ? 'selected' : ''; ?>>Tất cả</option>
                    <option value="0" <?php echo $role_filter == 0 ? 'selected' : ''; ?>>Người dùng</option>
                    <option value="2" <?php echo $role_filter == 2 ? 'selected' : ''; ?>>Nhà sinh vật học</option>
                    <option value="1" <?php echo $role_filter == 1 ? 'selected' : ''; ?>>Admin</option>
                </select>
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>
        
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="post-item">
                <div class="avatar">
                    <?php
                    // Đường dẫn đến ảnh đại diện
                    $avatar_path = '../uploads/' . htmlspecialchars($row['avatar']);
                    ?>
                    <a href="hoso.php?id=<?php echo $row['id_nguoidung']; ?>">
                        <img src="<?php echo $avatar_path; ?>" alt="Avatar">
                    </a>
                </div>
                <div class="content">
                    <h2><a href="chitietbaidang.php?id=<?php echo $row['id_baidang']; ?>"><?php echo htmlspecialchars($row['tieude']); ?></a></h2>
                    <p class="post-meta">
                        Tạo bởi: <a href="hoso.php?id=<?php echo $row['id_nguoidung']; ?>"><?php echo htmlspecialchars($row['fullname']); ?></a> (<?php echo htmlspecialchars($row['email']); ?>) <br>+ 
                        <?php
                        switch ($row['quyen']) {
                            case 1:
                                echo 'Admin';
                                break;
                            case 2:
                                echo 'Nhà Sinh vật học';
                                break;
                            default:
                                echo 'Người dùng';
                        }
                        ?> <br>- Ngày tạo: <?php echo $row['thoigiantao']; ?>
                    </p>
                    <p><?php echo substr(($row['mota']), 0, 150) . '...'; ?></p> <!-- Hiển thị mô tả ngắn -->
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Phân trang -->
        <div class="pagination">
            <?php
            // Hiển thị các liên kết phân trang
            for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="diendan.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>&role=<?php echo $role_filter; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
