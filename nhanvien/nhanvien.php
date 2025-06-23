<?php
session_start();

include('conn.php'); 
include('function.php');
check_login() ;
check_nhanvien();

include('header.php');

// // Lấy tổng số tài khoản
// $sql_user_count = "SELECT COUNT(*) as total FROM nguoidung";
// $result_user_count = $conn->query($sql_user_count);
// $row_user_count = $result_user_count->fetch_assoc();
// $total_users = $row_user_count['total'];

// Lấy tổng số giới
$sql_kingdom_count = "SELECT COUNT(*) as total FROM gioi";
$result_kingdom_count = $conn->query($sql_kingdom_count);
$row_kingdom_count = $result_kingdom_count->fetch_assoc();
$total_kingdoms = $row_kingdom_count['total'];

// Lấy tổng số ngành
$sql_phylum_count = "SELECT COUNT(*) as total FROM nganh";
$result_phylum_count = $conn->query($sql_phylum_count);
$row_phylum_count = $result_phylum_count->fetch_assoc();
$total_phylums = $row_phylum_count['total'];

// Lấy tổng số lớp
$sql_class_count = "SELECT COUNT(*) as total FROM lop";
$result_class_count = $conn->query($sql_class_count);
$row_class_count = $result_class_count->fetch_assoc();
$total_classes = $row_class_count['total'];

// Lấy tổng số bộ
$sql_order_count = "SELECT COUNT(*) as total FROM bo";
$result_order_count = $conn->query($sql_order_count);
$row_order_count = $result_order_count->fetch_assoc();
$total_orders = $row_order_count['total'];

// Lấy tổng số họ
$sql_family_count = "SELECT COUNT(*) as total FROM ho";
$result_family_count = $conn->query($sql_family_count);
$row_family_count = $result_family_count->fetch_assoc();
$total_families = $row_family_count['total'];

// Lấy tổng số chi
$sql_genus_count = "SELECT COUNT(*) as total FROM chi";
$result_genus_count = $conn->query($sql_genus_count);
$row_genus_count = $result_genus_count->fetch_assoc();
$total_genera = $row_genus_count['total'];

// Lấy tổng số loài
$sql_species_count = "SELECT COUNT(*) as total FROM loai";
$result_species_count = $conn->query($sql_species_count);
$row_species_count = $result_species_count->fetch_assoc();
$total_species = $row_species_count['total'];

// Lấy tổng số sinh vật
$sql_animal_count = "SELECT COUNT(*) as total FROM sinhvat";
$result_animal_count = $conn->query($sql_animal_count);
$row_animal_count = $result_animal_count->fetch_assoc();
$total_animals = $row_animal_count['total'];

// // Lấy tổng số bài đăng
// $sql_post_count = "SELECT COUNT(*) as total FROM baidang";
// $result_post_count = $conn->query($sql_post_count);
// $row_post_count = $result_post_count->fetch_assoc();
// $total_posts = $row_post_count['total'];

// // Lấy tổng số hỏi đáp
// $sql_faq_count = "SELECT COUNT(*) as total FROM hoidap";
// $result_faq_count = $conn->query($sql_faq_count);
// $row_faq_count = $result_faq_count->fetch_assoc();
// $total_faqs = $row_faq_count['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>
    <style>
        /* style.css */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 25px auto 100px auto;
        }

        .header {
            background-color: #333;
            color: #fff;
            padding: 20px;
        }

        .dashboard {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 20px 0;
        }

        .dashboard-item {
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            margin: 10px;
            width: 200px;
        }

        .dashboard-item:hover {
            background-color: #e7e1e1;
        }

        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard">
            <!-- <div class="dashboard-item" onclick="navigateTo('taikhoan.php')">
                <h2>Tổng số tài khoản</h2>
                <p><?php echo $total_users; ?></p>
            </div> -->

            <div class="dashboard-item" onclick="navigateTo('gioi.php')">
                <h2>Tổng số giới</h2>
                <p><?php echo $total_kingdoms; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('nganh.php')">
                <h2>Tổng số ngành</h2>
                <p><?php echo $total_phylums; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('lop.php')">
                <h2>Tổng số lớp</h2>
                <p><?php echo $total_classes; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('bo.php')">
                <h2>Tổng số bộ</h2>
                <p><?php echo $total_orders; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('ho.php')">
                <h2>Tổng số họ</h2>
                <p><?php echo $total_families; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('chi.php')">
                <h2>Tổng số chi</h2>
                <p><?php echo $total_genera; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('loai.php')">
                <h2>Tổng số loài</h2>
                <p><?php echo $total_species; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('sinhvat.php')">
                <h2>Tổng số sinh vật</h2>
                <p><?php echo $total_animals; ?></p>
            </div>

            <!-- <div class="dashboard-item" onclick="navigateTo('baidang.php')">
                <h2>Tổng số bài đăng</h2>
                <p><?php echo $total_posts; ?></p>
            </div>

            <div class="dashboard-item" onclick="navigateTo('hoidap.php')">
                <h2>Tổng số hỏi đáp</h2>
                <p><?php echo $total_faqs; ?></p>
            </div> -->
        </div>
    </div>

    <div class="footer">
        <p>&copy; TT All rights reserved @ <?= date('Y'); ?></p>
    </div>

    <script>
        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</body>

</html>
