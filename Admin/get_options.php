<?php
include('conn.php');

// Xác định các tham số truyền qua
$level = isset($_GET['level']) ? $_GET['level'] : null;
$parent_id = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;

// Xác định bảng và cột dựa trên cấp bậc
switch ($level) {
    case 'nganh':
        $table = 'nganh';
        $parent_id_field = 'id_gioi';
        $id_field = 'id_nganh';
        $name_field = 'tennganh';
        break;
    case 'lop':
        $table = 'lop';
        $parent_id_field = 'id_nganh';
        $id_field = 'id_lop';
        $name_field = 'tenlop';
        break;
    case 'bo':
        $table = 'bo';
        $parent_id_field = 'id_lop';
        $id_field = 'id_bo';
        $name_field = 'tenbo';
        break;
    case 'ho':
        $table = 'ho';
        $parent_id_field = 'id_bo';
        $id_field = 'id_ho';
        $name_field = 'tenho';
        break;
    case 'chi':
        $table = 'chi';
        $parent_id_field = 'id_ho';
        $id_field = 'id_chi';
        $name_field = 'tenchi';
        break;
    case 'loai':
        $table = 'loai';
        $parent_id_field = 'id_chi';
        $id_field = 'id_loai';
        $name_field = 'tenloai';
        break;
    default:
        echo "<option value=''>--Chọn--</option>";
        exit;
}

// Truy vấn để lấy các tùy chọn dựa trên cấp bậc và ID của cấp bậc cha
$sql = "SELECT $id_field, $name_field FROM $table WHERE $parent_id_field = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $parent_id);
$stmt->execute();
$result = $stmt->get_result();

// Tạo danh sách tùy chọn
$options = "<option value=''>--Chọn--</option>";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row[$id_field] . "'>" . $row[$name_field] . "</option>";
}

// Trả về danh sách tùy chọn dưới dạng phản hồi Ajax
echo $options;

// Đóng kết nối
$stmt->close();
$conn->close();
?>
