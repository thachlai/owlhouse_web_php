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

// Truy vấn SQL với bộ lọc
$sql = [
    "ngay" => "SELECT COUNT(*) AS so_luong FROM nguoidung WHERE $filter AND DATE(thoigiantao) = CURDATE();",
    "tuan" => "SELECT COUNT(*) AS so_luong FROM nguoidung WHERE $filter AND YEARWEEK(thoigiantao, 1) = YEARWEEK(CURDATE(), 1);",
    "thang" => "SELECT COUNT(*) AS so_luong FROM nguoidung WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE()) AND MONTH(thoigiantao) = MONTH(CURDATE());",
    "nam" => "SELECT COUNT(*) AS so_luong FROM nguoidung WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE());"
];

$results = [];
foreach ($sql as $key => $query) {
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $results[$key] = $row['so_luong'];
    } else {
        $results[$key] = 0;
    }
}

$conn->close();

// Chuyển dữ liệu sang định dạng JSON cho JavaScript
$results_json = json_encode($results);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Tài Khoản</title>
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
        <h1>Thống Kê Số Lượng Tài Khoản Tạo Mới</h1>

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

        <canvas id="myChart"></canvas>
    </div>
    <script>
        // Dữ liệu JSON từ PHP
        const data = <?php echo $results_json; ?>;

        // Vẽ biểu đồ
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Ngày', 'Tuần', 'Tháng', 'Năm'],
                datasets: [{
                    label: 'Số Lượng Tài Khoản Tạo Mới',
                    data: [data.ngay || 0, data.tuan || 0, data.thang || 0, data.nam || 0],
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
    </script>
</body>
</html>
