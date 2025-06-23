<?php
session_start();
include('conn.php');
include('function.php');
// check_login();

include('header.php');

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? $_GET['search'] : '';
$loai = isset($_GET['loai']) ? intval($_GET['loai']) : 0;

// Xử lý phân trang
$results_per_page = 35;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($page - 1) * $results_per_page;

// Hàm lấy các tùy chọn động từ cơ sở dữ liệu
function get_options($table, $id_column, $name_column, $selected_id = 0, $parent_column = null, $parent_id = 0) {
    global $conn;
    $options = "<option value='0'>Chọn</option>";
    $sql = "SELECT $id_column, $name_column FROM $table";
    if ($parent_column) {
        $sql .= " WHERE $parent_column = $parent_id";
    }
    $result = $conn->query($sql);
    if (!$result) {
        echo "Lỗi truy vấn: " . $conn->error;
        return $options;
    }
    while ($row = $result->fetch_assoc()) {
        $selected = $row[$id_column] == $selected_id ? ' selected' : '';
        $options .= "<option value='" . $row[$id_column] . "'$selected>" . htmlspecialchars($row[$name_column]) . "</option>";
    }
    return $options;
}

// Xử lý tìm kiếm toàn cục
function search_species($search, $start_from, $results_per_page) {
    global $conn;
    $sql = "SELECT * FROM sinhvat WHERE tensinhvat LIKE '%" . $conn->real_escape_string($search) . "%' LIMIT $start_from, $results_per_page";
    return $conn->query($sql);
}

// Xử lý lọc theo cấp Loài
function filter_species_by_loai($loai, $start_from, $results_per_page) {
    global $conn;
    $sql_filter = "SELECT * FROM sinhvat WHERE 1";

    if ($loai > 0) $sql_filter .= " AND id_loai='$loai'";

    $sql_filter .= " LIMIT $start_from, $results_per_page";
    return $conn->query($sql_filter);
}

// Tính tổng số trang
function get_total_pages($search, $loai) {
    global $conn;
    $sql_count = "SELECT COUNT(*) as total FROM sinhvat WHERE 1";

    if (!empty($search)) {
        $sql_count .= " AND tensinhvat LIKE '%" . $conn->real_escape_string($search) . "%'";
    } else {
        if ($loai > 0) $sql_count .= " AND id_loai='$loai'";
    }

    $result_count = $conn->query($sql_count);
    if ($result_count) {
        $row = $result_count->fetch_assoc();
        return ceil($row['total'] / 35); // 35 là số sinh vật mỗi trang
    } else {
        echo "Lỗi truy vấn: " . $conn->error;
        return 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Từ Điển Sinh Vật</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
  <style>
body {
    background-color: #f4f4f4;
}

.content {
    max-width: 1300px;
    margin: 0 auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.sinhvat-list {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px;
}

.sinhvat-item {
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    width: 220px;
    text-align: center;
    background: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.sinhvat-item:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    background-color:#97eab4;
}

.sinhvat-img img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.sinhvat-info {
    padding: 12px;
}

.sinhvat-info h3 {
    font-size: 16px;
    margin: 0;
    color: #333;
    line-height: 1.4;
}

.sinhvat-info a {
    text-decoration: none;
    color: inherit;
    transition: color 0.3s ease;
}

.sinhvat-info a:hover {
    color: #007bff;
}

.timkiem, .loc {
    padding: 16px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 16px 0;
}

.timkiem label, .loc label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input[type="text"], select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: calc(100% - 18px);
    box-sizing: border-box;
}

button {
    margin-top:10px;
    padding: 8px 16px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

.pagination {
    text-align: center;
    margin: 20px 0;
}

.pagination a {
    margin: 0 5px;
    text-decoration: none;
    color: #007bff;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.pagination a.active, .pagination a:hover {
    background-color: #007bff;
    color: #ffffff;
}
</style>

    </style>
</head>
<body>
    <div class="content">
        
    <!-- Form tìm kiếm toàn cục -->
    <div class="timkiem">
        <form method="GET" action="dictionary.php">
            <label for="search">Tìm kiếm theo tên Sinh Vật:</label>
            <input type="text" id="search" name="search" placeholder="Nhập tên Sinh Vật" value="<?php echo htmlspecialchars($search); ?>">
            <br>
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>

    <!-- Form lọc theo cấp Loài -->
    <div class="loc">
        <form method="GET" action="dictionary.php">
            <label for="loai">Loài:</label>
            <select id="loai" name="loai">
                <?php echo get_options('loai', 'id_loai', 'tenloai', $loai); ?>
            </select>
            <button type="submit">Lọc</button>
        </form>
    </div>

    <!-- Hiển thị danh sách sinh vật -->
    <?php
    $species_result = [];

    if (!empty($search)) {
        $species_result = search_species($search, $start_from, $results_per_page);
    } else {
        $species_result = filter_species_by_loai($loai, $start_from, $results_per_page);
    }

    if ($species_result === false) {
        echo "<div class='error-message'>Lỗi truy vấn: " . $conn->error . "</div>";
    } else {
        if ($species_result->num_rows > 0) {
            echo "<div class='sinhvat-list'>";
            while ($row = $species_result->fetch_assoc()) {
                $id_sinhvat = $row["id_sinhvat"];
                $ten_sinhvat = htmlspecialchars($row["tensinhvat"]);
                $img_src = '../uploads/' . htmlspecialchars($row["img"]);
                
                // Hiển thị khung sinh vật
                echo "<div class='sinhvat-item'>
                    <div class='sinhvat-img'><img src='$img_src' alt='$ten_sinhvat'></div>
                    <div class='sinhvat-info'>
                        <h3><a href='chitietsinhvat.php?id=$id_sinhvat'>$ten_sinhvat</a></h3>
                    </div>
                </div>";
            }
            echo "</div>";
        } else {
            echo "<p>Không tìm thấy sinh vật nào.</p>";
        }
    }

    // Hiển thị phân trang
    $total_pages = get_total_pages($search, $loai);
    echo "<div class='pagination'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo "<a href='dictionary.php?page=$i&search=" . urlencode($search) . "&loai=$loai' class='$active'>$i</a>";
    }
    echo "</div>";
    ?>
    </div>
</body>
</html>
