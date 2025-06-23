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
    <!-- <title>Header</title> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="../ckeditor/ckeditor.js"></script>
    <script src="../ckfinder/ckfinder.js"></script>
    
</head>
<style>
    dropdown-menu:hover{
        background-color:#97eab4;
    }
</style>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="trangchu.php"><img src="../img/LOGO2.png" alt="Logo"></a>
            </div>
            <nav>
                <ul class="menu">
                    <li class="dropdown">
                        <a href="diendan.php">Diễn Đàn</a>
                    </li>
                    <li class="dropdown">
                        <a href="trangchu.php">Trang chủ</a>
                    </li>
                    <!-- <li class="dropdown">
                        <a href="gioithieu.php">Giới thiệu</a>
                    </li> -->
                    <li class="dropdown"><a href="dictionary.php">Từ điển</a></li>
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
                    <li class="search">
                    <form action="dictionary.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Tìm kiếm sinh vật..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
                    </li>
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
                                
                                echo '<li class="xanh"><img src="../uploads/' . $user_img . '" alt="User Image" style="width: 80px; height: 80px; border-radius: 50%; margin-right: 10px;"></li>';
                                echo '<li class="xanh"><a href="hoso.php">Hồ Sơ</a></li>';
                                echo '<li class="xanh"><a href="taobaidang.php">Tạo bài đăng</a></li>';
                                if ($user_role == 1) {
                                    echo '<li class="xanh"><a href="../Admin/admin.php">Quản trị</a></li>';
                                } elseif ($user_role == 2) {
                                    echo '<li class="xanh"><a href="../nhanvien/nhanvien.php">Sinh vật</a></li>';
                                }
                                echo '<li class="xanh"><a href="logout.php">Đăng xuất</a></li>';
                            } else {
                                echo '<li class="xanh"><a href="dangnhap.php">Đăng nhập</a></li>';
                                echo '<li class="xanh"><a href="dangky.php">Đăng ký</a></li>';
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
