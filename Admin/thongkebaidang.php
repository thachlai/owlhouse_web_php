
<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');


// Xử lý lọc ngày tháng năm
$filter = "1=1"; // Mặc định là không lọc

if (isset($_POST['filter'])) {
    $from_date = $_POST['from_date'] ?? null;
    $to_date = $_POST['to_date'] ?? null;

    if ($from_date && $to_date) {
        $filter = "DATE(thoigiantao) BETWEEN '$from_date' AND '$to_date'";
    } elseif ($from_date) {
        $filter = "DATE(thoigiantao) >= '$from_date'";
    } elseif ($to_date) {
        $filter = "DATE(thoigiantao) <= '$to_date'";
    }
}

// Truy vấn SQL cho số lượng bài đăng
$sql_posts = [
    "ngay" => "SELECT COUNT(*) AS so_luong FROM baidang WHERE $filter AND DATE(thoigiantao) = CURDATE();",
    "tuan" => "SELECT COUNT(*) AS so_luong FROM baidang WHERE $filter AND YEARWEEK(thoigiantao, 1) = YEARWEEK(CURDATE(), 1);",
    "thang" => "SELECT COUNT(*) AS so_luong FROM baidang WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE()) AND MONTH(thoigiantao) = MONTH(CURDATE());",
    "nam" => "SELECT COUNT(*) AS so_luong FROM baidang WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE());"
];

// Truy vấn SQL cho số lượng bình luận
$sql_comments = [
    "ngay" => "SELECT COUNT(*) AS so_luong FROM binhluan WHERE $filter AND DATE(thoigiantao) = CURDATE();",
    "tuan" => "SELECT COUNT(*) AS so_luong FROM binhluan WHERE $filter AND YEARWEEK(thoigiantao, 1) = YEARWEEK(CURDATE(), 1);",
    "thang" => "SELECT COUNT(*) AS so_luong FROM binhluan WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE()) AND MONTH(thoigiantao) = MONTH(CURDATE());",
    "nam" => "SELECT COUNT(*) AS so_luong FROM binhluan WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE());"
];

$results_posts = [];
$results_comments = [];

foreach ($sql_posts as $key => $query) {
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $results_posts[$key] = $row['so_luong'];
    } else {
        $results_posts[$key] = 0;
    }
}

foreach ($sql_comments as $key => $query) {
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $results_comments[$key] = $row['so_luong'];
    } else {
        $results_comments[$key] = 0;
    }
}

$conn->close();

// Chuyển dữ liệu sang định dạng JSON cho JavaScript
$results_posts_json = json_encode($results_posts);
$results_comments_json = json_encode($results_comments);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Bài Đăng và Bình Luận</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        canvas {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }
        .container {
            text-align: center;
        }
        .filter-form {
            text-align: center;
            margin: 20px;
        }
        .filter-form input, .filter-form button {
            padding: 10px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thống Kê Bài Đăng và Bình Luận</h1>

        <!-- Bộ lọc ngày tháng năm -->
        <div class="filter-form">
            <form method="post" action="">
                <label for="from_date">Từ ngày:</label>
                <input type="date" id="from_date" name="from_date">
                <label for="to_date">Đến ngày:</label>
                <input type="date" id="to_date" name="to_date">
                <button type="submit" name="filter">Lọc</button>
            </form>
        </div>

        <!-- Biểu đồ số lượng bài đăng -->
        <h2>Số Lượng Bài Đăng</h2>
        <canvas id="chartPosts"></canvas>
        
        <!-- Biểu đồ số lượng bình luận -->
        <h2>Số Lượng Bình Luận</h2>
        <canvas id="chartComments"></canvas>
    </div>
    <script>
        // Dữ liệu JSON từ PHP
        const dataPosts = <?php echo $results_posts_json; ?>;
        const dataComments = <?php echo $results_comments_json; ?>;

        // Vẽ biểu đồ số lượng bài đăng
        const ctxPosts = document.getElementById('chartPosts').getContext('2d');
        const chartPosts = new Chart(ctxPosts, {
            type: 'bar',
            data: {
                labels: ['Ngày', 'Tuần', 'Tháng', 'Năm'],
                datasets: [{
                    label: 'Số Lượng Bài Đăng',
                    data: [dataPosts.ngay || 0, dataPosts.tuan || 0, dataPosts.thang || 0, dataPosts.nam || 0],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Thời Gian'
                        }
                    }
                }
            }
        });

        // Vẽ biểu đồ số lượng bình luận
        const ctxComments = document.getElementById('chartComments').getContext('2d');
        const chartComments = new Chart(ctxComments, {
            type: 'bar',
            data: {
                labels: ['Ngày', 'Tuần', 'Tháng', 'Năm'],
                datasets: [{
                    label: 'Số Lượng Bình Luận',
                    data: [dataComments.ngay || 0, dataComments.tuan || 0, dataComments.thang || 0, dataComments.nam || 0],
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Thời Gian'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
