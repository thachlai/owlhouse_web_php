<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']); // Chuyển đổi id thành số nguyên để tránh SQL injection

    // Tạo tên bảng và điều kiện dựa trên loại
    $table = '';
    $id_column = '';

    switch ($type) {
        case 'gioi':
            $table = 'gioi';
            $id_column = 'id_gioi';
            break;
        case 'nganh':
            $table = 'nganh';
            $id_column = 'id_nganh';
            break;
        case 'lop':
            $table = 'lop';
            $id_column = 'id_lop';
            break;
        case 'bo':
            $table = 'bo';
            $id_column = 'id_bo';
            break;
        case 'ho':
            $table = 'ho';
            $id_column = 'id_ho';
            break;
        case 'chi':
            $table = 'chi';
            $id_column = 'id_chi';
            break;
        case 'loai':
            $table = 'loai';
            $id_column = 'id_loai';
            break;
        case 'sinhvat':
            $table = 'sinhvat';
            $id_column = 'id_sinhvat';
            break;
        default:
            echo "Loại không hợp lệ.";
            exit();
    }

    // Kiểm tra nếu bảng và cột id đã được xác định
    if (!empty($table) && !empty($id_column)) {
        // Thực hiện truy vấn để xóa
        $sql = "DELETE FROM $table WHERE $id_column = $id";

        if ($conn->query($sql) === TRUE) {
            // Nếu xóa thành công, chuyển hướng về trang danh sách tương ứng
            header("Location: ${type}.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Bảng hoặc cột không hợp lệ.";
    }
} else {
    echo "Thiếu tham số.";
}

$conn->close();
?>
