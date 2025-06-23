<?php
// Kết nối đến cơ sở dữ liệu
include 'conn.php';


// Kiểm tra trạng thái đăng nhập và quyền người dùng
$is_logged_in = isset($_SESSION['id_nguoidung']); // Kiểm tra nếu người dùng đã đăng nhập
$user_role = isset($_SESSION['quyen']) ? $_SESSION['quyen'] : 0; // Quyền người dùng
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="../ckeditor/ckeditor.js"></script>
    <script src="../ckfinder/ckfinder.js"></script>
    <style>
        body{
    background-color: #5fdf3b;
}
/* Style cho header */
header {
    background: #333;
    color: #fff;
    padding: 10px 20px;
    /* width: 50p[] 250px; */
}

header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo img {
    width: 150px;
}

nav {
    display: flex;
    align-items: center;
}

nav ul.menu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
}

nav ul.menu > li {
    position: relative;
    margin-left: 20px;
}

nav ul.menu > li > a {
    color: #fff;
    padding: 10px;
    text-decoration: none;
    display: block;
}

nav ul.menu .dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    color: #000;
    padding: 0;
    list-style: none;
    margin: 0;
    z-index: 1000;
}

nav ul.menu .dropdown-menu > li {
    position: relative;
}

nav ul.menu .dropdown-menu > li > a {
    color: #000;
    padding: 10px;
    text-decoration: none;
    display: block;
}

nav ul.menu .dropdown-menu .dropdown-menu {
    top: 0;
    left: 100%;
    display: none;
}

nav ul.menu > li:hover > .dropdown-menu {
    display: block;
}

nav ul.menu .dropdown-menu li:hover > .dropdown-menu {
    display: block;
}

nav .search {
    position: relative;
    margin-left: 20px;
}

nav .search form {
    display: flex;
    align-items: center;
}

nav .search input[type="text"] {
    padding: 5px;
    margin-left: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

nav .user-icon {
    position: relative;
    margin-left: 20px;
}

nav .user-icon > a {
    color: #fff;
    padding: 10px;
    display: flex;
    align-items: center;
    text-decoration: none;
}

nav .user-icon i {
    font-size: 20px;
}



nav .user-icon:hover .dropdown-menu {
    display: flex;
    padding: 25px; /* Tăng padding để khung lớn hơn */
    right: 0; /* Đảm bảo menu xuất hiện dưới icon */
    left: auto;
    justify-content: center;
    align-items: center;
    text-align: center;
}

nav .user-icon .dropdown-menu > li > a {

}
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Container for content */
.content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Table Styles */
.table-danhmuc {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table-danhmuc table {
    width: 100%;
    border-collapse: collapse;
}

.table-danhmuc th, .table-danhmuc td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.table-danhmuc th {
    background-color: #4CAF50;
    color: white;
}

.table-danhmuc tr:nth-child(even) {
    background-color: #f2f2f2;
}

.table-danhmuc tr:hover {
    background-color: #ddd;
}

.table-danhmuc img {
    max-width: 100px;
    height: auto;
    border-radius: 5px;
}
/* CSS cho ô mô tả */
/* CSS cho textarea trong bảng */
.mota-textarea {
    width: 500px; /* Độ rộng của textarea bằng với độ rộng của ô bảng */
    height: 200px; /* Chiều cao cố định */
    overflow: auto; /* Hiển thị thanh cuộn khi nội dung vượt quá kích thước */
    border: none; /* Loại bỏ viền của textarea */
    background-color: #f9f9f9; /* Màu nền nhẹ */
    resize: none; /* Ngăn người dùng thay đổi kích thước của textarea */
    padding: 5px; /* Padding để không bị sát viền */
}
.image_field {
    margin-bottom: 10px; /* Khoảng cách giữa các ảnh phụ */
    display: flex;
    align-items: center;
}

.image_field input[type="file"] {
    margin-right: 10px; /* Khoảng cách giữa ảnh và nút xóa */
}

.image_field button {
    margin-left: 10px; /* Khoảng cách giữa nút xóa và ảnh */
}

.image_preview img {
    display: inline-block;
    margin-right: 10px; /* Khoảng cách giữa các ảnh phụ */
}
/* Thêm vào tệp CSS của bạn hoặc trong <style> */
.anh-phu {
    max-width: 150px; /* Kích thước tối đa của khung ảnh phụ */
    max-height: 100px; /* Chiều cao tối đa của khung ảnh phụ */
    overflow-y: auto; /* Hiển thị thanh cuộn dọc nếu cần */
    overflow-x: hidden; /* Ẩn thanh cuộn ngang */
}

.anh-phu-container {
    display: flex; /* Hiển thị ảnh phụ theo hàng ngang */
    flex-wrap: wrap; /* Cho phép các ảnh phụ xuống dòng khi vượt quá chiều rộng khung */
}

.anh-phu-img {
    width: 50px; /* Kích thước của ảnh phụ */
    height: 50px;
    margin-right: 5px; /* Khoảng cách giữa các ảnh */
    margin-bottom: 5px; /* Khoảng cách giữa các hàng ảnh */
}
/* Form Styles */
form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

form label {
    display: block;
    margin-bottom: 8px;margin-top: 8px;
    font-weight: bold;
}

form input[type="text"],
form input[type="file"],
form textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

form textarea {
    resize: vertical;
}

form button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    cursor: pointer;
}

form button:hover {
    background-color: #45a049;
}

/* Success Message */
.success-message {
    color: green;
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 20px;
}

/* Search Form Styles */
.timkiem {
    margin-bottom: 20px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.timkiem form {
    display: flex;
    flex-direction: column;
}

.timkiem label {
    margin-bottom: 8px;
}

.timkiem input[type="text"] {
    padding: 8px;
    margin-bottom: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.timkiem button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 4px;
    cursor: pointer;
}

.timkiem button:hover {
    background-color: #45a049;
}

    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="admin.php"><img src="../img/LOGO2.png" alt="Logo"></a>
            </div>
            <nav>
                <ul class="menu">
                <li class="dropdown"><a href="#">Thống kê</a>
                        <ul class="dropdown-menu">
                            <li><a href="thongketaikhoan.php">Thống kê tài khoản</a></li>
                            <li><a href="thongkebaidang.php">Thống kê bài đăng</a></li>
                            <li><a href="thongkesv.php">Thống kê sinh vật</a></li>
                        </ul>
                    <li class="dropdown"><a href="#">Tài Khoản</a>
                        <ul class="dropdown-menu">
                            <li><a href="themtaikhoan.php">Them Tài Khoản</a></li>
                            <li><a href="taikhoan.php">Danh sách tài khoản</a></li>
                        </ul>
                    <li class="dropdown"><a href="#">Cấp Giới</a>
                        <ul class="dropdown-menu">
                            <li><a href="themgioi.php">Thêm Giới</a></li>
                            <li><a href="gioi.php">Danh sách giới</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#">Cấp Ngành</a>
                        <ul class="dropdown-menu">
                            <li><a href="themnganh.php">Thêm Ngành</a></li>
                            <li><a href="nganh.php">Danh sách Ngành</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#">Cấp Lớp</a>
                        <ul class="dropdown-menu">
                            <li><a href="themlop.php">Thêm lớp</a></li>
                            <li><a href="lop.php">Danh sách lớp</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#">Cấp Bộ</a>
                        <ul class="dropdown-menu">
                            <li><a href="thembo.php">Thêm Bộ</a></li>
                            <li><a href="bo.php">Danh Sách Bộ</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#">Cấp Họ</a>
                        <ul class="dropdown-menu">
                            <li><a href="themho.php">Thêm Họ</a></li>
                            <li><a href="ho.php">Danh Sách Họ</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#">Cấp Chi</a>
                        <ul class="dropdown-menu">
                            <li><a href="themchi.php">Thêm Chi</a></li>
                            <li><a href="chi.php">Danh Sách Chi</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#">Cấp Loài</a>
                        <ul class="dropdown-menu">
                            <li><a href="themloai.php">Thêm Loài</a></li>
                            <li><a href="loai.php">Danh Sách Loài</a></li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="#">Sinh vật</a>
                        <ul class="dropdown-menu">
                            <li><a href="themsinhvat.php">Thêm Sinh Vật</a></li>
                            <li><a href="sinhvat.php">Danh Sách Sinh Vật</a></li>
                        </ul>
                    
                    </li>
                    <li class="dropdown"><a href="baidang.php">Bài Đăng</a>
                        <!-- <ul class="dropdown-menu">
                            <li><a href="themsinhvat.php">Thêm Sinh Vật</a></li>
                            <li><a href="sinhvat.php">Danh Sách Sinh Vật</a></li>
                        </ul> -->
                    
                    </li>
                    <!-- <li class="dropdown">
                        <a href="#">Doanh Mục</a>
                        <ul class="dropdown-menu">
                            <?php
                            // Lấy dữ liệu từ cơ sở dữ liệu
                            $query = "SELECT * FROM gioi";
                            $result = $conn->query($query);

                            while ($row = $result->fetch_assoc()) {
                                echo '<li><a href="#">' . htmlspecialchars($row['tengioi']) . '</a>';
                                echo '<ul class="dropdown-menu">';
                                
                                $query_nganh = "SELECT * FROM nganh WHERE id_gioi = " . $row['id_gioi'];
                                $result_nganh = $conn->query($query_nganh);
                                
                                while ($row_nganh = $result_nganh->fetch_assoc()) {
                                    echo '<li><a href="#">' . htmlspecialchars($row_nganh['tennganh']) . '</a>';
                                    echo '<ul class="dropdown-menu">';
                                    
                                    $query_lop = "SELECT * FROM lop WHERE id_nganh = " . $row_nganh['id_nganh'];
                                    $result_lop = $conn->query($query_lop);
                                    
                                    while ($row_lop = $result_lop->fetch_assoc()) {
                                        echo '<li><a href="#">' . htmlspecialchars($row_lop['tenlop']) . '</a>';
                                        echo '<ul class="dropdown-menu">';
                                        
                                        $query_bo = "SELECT * FROM bo WHERE id_lop = " . $row_lop['id_lop'];
                                        $result_bo = $conn->query($query_bo);
                                        
                                        while ($row_bo = $result_bo->fetch_assoc()) {
                                            echo '<li><a href="#">' . htmlspecialchars($row_bo['tenbo']) . '</a>';
                                            echo '<ul class="dropdown-menu">';
                                            
                                            $query_ho = "SELECT * FROM ho WHERE id_bo = " . $row_bo['id_bo'];
                                            $result_ho = $conn->query($query_ho);
                                            
                                            while ($row_ho = $result_ho->fetch_assoc()) {
                                                echo '<li><a href="#">' . htmlspecialchars($row_ho['tenho']) . '</a>';
                                                echo '<ul class="dropdown-menu">';
                                                
                                                $query_chi = "SELECT * FROM chi WHERE id_ho = " . $row_ho['id_ho'];
                                                $result_chi = $conn->query($query_chi);
                                                
                                                while ($row_chi = $result_chi->fetch_assoc()) {
                                                    echo '<li><a href="#">' . htmlspecialchars($row_chi['tenchi']) . '</a>';
                                                    echo '<ul class="dropdown-menu">';
                                                    
                                                    $query_loai = "SELECT * FROM loai WHERE id_chi = " . $row_chi['id_chi'];
                                                    $result_loai = $conn->query($query_loai);
                                                    
                                                    while ($row_loai = $result_loai->fetch_assoc()) {
                                                        echo '<li><a href="#">' . htmlspecialchars($row_loai['tenloai']) . '</a>';
                                                        echo '<ul class="dropdown-menu">';
                                                        
                                                        $query_sinhvat = "SELECT * FROM sinhvat WHERE id_loai = " . $row_loai['id_loai'];
                                                        $result_sinhvat = $conn->query($query_sinhvat);
                                                        
                                                        while ($row_sinhvat = $result_sinhvat->fetch_assoc()) {
                                                            echo '<li><a href="#">' . htmlspecialchars($row_sinhvat['tensinhvat']) . '</a></li>';
                                                        }
                                                        echo '</ul></li>';
                                                    }
                                                    echo '</ul></li>';
                                                }
                                                echo '</ul></li>';
                                            }
                                            echo '</ul></li>';
                                        }
                                        echo '</ul></li>';
                                    }
                                    echo '</ul></li>';
                                }
                                echo '</ul></li>';
                            }
                            ?>
                        </ul>
                    </li> -->
                    <!-- <li class="search">
                        <form action="search.php" method="get">
                            <input type="text" name="query" placeholder="Tìm kiếm sinh vật...">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </li> -->
                    <li class="user-icon">
                        <a href="#"><i class="fas fa-user"></i></a>
                        <ul class="dropdown-menu">
                            <?php
                            if ($is_logged_in) {
                                // Lấy thông tin người dùng từ cơ sở dữ liệu
                                $user_id = $_SESSION['id_nguoidung'];
                                $query_user = "SELECT img FROM nguoidung WHERE id_nguoidung = $user_id";
                                $result_user = $conn->query($query_user);
                                $user = $result_user->fetch_assoc();
                                $user_img = $user['img'];
                                
                                echo '<li><img src="../uploads/' . $user_img . '" alt="User Image" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 10px;"></li>';
                                echo '<li><a href="../user/hoso.php">Hồ Sơ</a></li>';
                                if ($user_role == 1) {
                                    echo '<li><a href="../user/trangchu.php">Trang chủ</a></li>';
                                } elseif ($user_role == 2) {
                                    echo '<li><a href="my_species.php">Sinh vật</a></li>';
                                }
                                echo '<li><a href="../user/logout.php">Đăng xuất</a></li>';
                            } else {
                                echo '<li><a href="../dangnhap.php">Đăng nhập</a></li>';
                                echo '<li><a href="../dangky.php">Đăng ký</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
</body>
</html>
