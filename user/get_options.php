<?php
include('conn.php');

$level = $_GET['level'];
$parent_value = $_GET['parent_value'];
$parent_id = $_GET['parent_id'];

function get_options($table, $id_column, $name_column, $parent_column = null, $parent_id = null) {
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
        $options .= "<option value='" . $row[$id_column] . "'>" . htmlspecialchars($row[$name_column]) . "</option>";
    }
    return $options;
}

switch ($level) {
    case 'ngành':
        echo get_options('nganh', 'id_nganh', 'tennganh', 'id_gioi', $parent_value);
        break;
    case 'lớp':
        echo get_options('lop', 'id_lop', 'tenlop', 'id_nganh', $parent_value);
        break;
    case 'bộ':
        echo get_options('bo', 'id_bo', 'tenbo', 'id_lop', $parent_value);
        break;
    case 'họ':
        echo get_options('ho', 'id_ho', 'tenho', 'id_bo', $parent_value);
        break;
    case 'chi':
        echo get_options('chi', 'id_chi', 'tenchi', 'id_ho', $parent_value);
        break;
    case 'loại':
        echo get_options('loai', 'id_loai', 'tenloai', 'id_chi', $parent_value);
        break;
}
?>
