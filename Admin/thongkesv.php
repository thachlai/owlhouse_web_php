<?php
// Kết nối cơ sở dữ liệu
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

// Truy vấn SQL cho số lượng sinh vật mới
$sql_new_species = [
    "ngay" => "SELECT COUNT(*) AS so_luong FROM sinhvat WHERE $filter AND DATE(thoigiantao) = CURDATE();",
    "tuan" => "SELECT COUNT(*) AS so_luong FROM sinhvat WHERE $filter AND YEARWEEK(thoigiantao, 1) = YEARWEEK(CURDATE(), 1);",
    "thang" => "SELECT COUNT(*) AS so_luong FROM sinhvat WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE()) AND MONTH(thoigiantao) = MONTH(CURDATE());",
    "nam" => "SELECT COUNT(*) AS so_luong FROM sinhvat WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE());"
];

// Truy vấn SQL cho số lượng sinh vật được hỏi đáp
$sql_qna = [
    "ngay" => "SELECT COUNT(*) AS so_luong FROM hoidap WHERE $filter AND DATE(thoigiantao) = CURDATE();",
    "tuan" => "SELECT COUNT(*) AS so_luong FROM hoidap WHERE $filter AND YEARWEEK(thoigiantao, 1) = YEARWEEK(CURDATE(), 1);",
    "thang" => "SELECT COUNT(*) AS so_luong FROM hoidap WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE()) AND MONTH(thoigiantao) = MONTH(CURDATE());",
    "nam" => "SELECT COUNT(*) AS so_luong FROM hoidap WHERE $filter AND YEAR(thoigiantao) = YEAR(CURDATE());"
];

$results_new_species = [];
$results_qna = [];

foreach ($sql_new_species as $key => $query) {
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $results_new_species[$key] = $row['so_luong'];
    } else {
        $results_new_species[$key] = 0;
    }
}

foreach ($sql_qna as $key => $query) {
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $results_qna[$key] = $row['so_luong'];
    } else {
        $results_qna[$key] = 0;
    }
}

$conn->close();

// Chuyển dữ liệu sang định dạng JSON cho JavaScript
$results_new_species_json = json_encode($results_new_species);
$results_qna_json = json_encode($results_qna);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Sinh Vật</title>
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
        <h1>Thống Kê Sinh Vật</h1>

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

        <!-- Biểu đồ số lượng sinh vật mới -->
        <h2>Số Lượng Sinh Vật Mới</h2>
        <canvas id="chartNewSpecies"></canvas>
        
        <!-- Biểu đồ số lượng sinh vật được hỏi đáp -->
        <h2>Số Lượng Sinh Vật Được Hỏi Đáp</h2>
        <canvas id="chartQnA"></canvas>
    </div>
    <script>
        // Dữ liệu JSON từ PHP
        const dataNewSpecies = <?php echo $results_new_species_json; ?>;
        const dataQnA = <?php echo $results_qna_json; ?>;

        // Vẽ biểu đồ số lượng sinh vật mới
        const ctxNewSpecies = document.getElementById('chartNewSpecies').getContext('2d');
        const chartNewSpecies = new Chart(ctxNewSpecies, {
            type: 'bar',
            data: {
                labels: ['Ngày', 'Tuần', 'Tháng', 'Năm'],
                datasets: [{
                    label: 'Số Lượng Sinh Vật Mới',
                    data: [dataNewSpecies.ngay || 0, dataNewSpecies.tuan || 0, dataNewSpecies.thang || 0, dataNewSpecies.nam || 0],
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

        // Vẽ biểu đồ số lượng sinh vật được hỏi đáp
        const ctxQnA = document.getElementById('chartQnA').getContext('2d');
        const chartQnA = new Chart(ctxQnA, {
            type: 'bar',
            data: {
                labels: ['Ngày', 'Tuần', 'Tháng', 'Năm'],
                datasets: [{
                    label: 'Số Lượng Sinh Vật Được Hỏi Đáp',
                    data: [dataQnA.ngay || 0, dataQnA.tuan || 0, dataQnA.thang || 0, dataQnA.nam || 0],
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
        