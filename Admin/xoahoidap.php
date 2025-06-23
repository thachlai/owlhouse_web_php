<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();

// Kết quả mặc định là thất bại
$response = array('success' => false, 'message' => 'Có lỗi xảy ra.');

// Kiểm tra yêu cầu xóa
if (isset($_GET['type']) && $_GET['type'] == 'hoidap' && isset($_GET['id'])) {
    $id_hoidap = intval($_GET['id']);
    
    // Xóa hỏi đáp khỏi cơ sở dữ liệu
    $sql_delete = "DELETE FROM hoidap WHERE id_hoidap='$id_hoidap'";
    
    if ($conn->query($sql_delete) === TRUE) {
        $response['success'] = true;
        $response['message'] = 'Xóa thành công.';
    } else {
        $response['message'] = 'Có lỗi xảy ra khi xóa.';
    }
} else {
    $response['message'] = 'Yêu cầu không hợp lệ.';
}

echo json_encode($response);
$conn->close();
?>
