<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];

    // Xác thực loại đối tượng
    if ($type === 'baidang') {
        // Xóa bài đăng
        $sql = "DELETE FROM baidang WHERE id_baidang=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        // Thực hiện câu lệnh xóa
        if ($stmt->execute()) {
            // Xóa ảnh liên quan (nếu có)
            $sql_anh = "DELETE FROM anh_baidang WHERE id_baidang=?";
            $stmt_anh = $conn->prepare($sql_anh);
            $stmt_anh->bind_param("i", $id);
            $stmt_anh->execute();

            $_SESSION['message'] = 'Xóa bài đăng thành công!';
        } else {
            $_SESSION['message'] = 'Lỗi khi xóa bài đăng.';
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = 'Loại đối tượng không hợp lệ.';
    }
} else {
    $_SESSION['message'] = 'Thiếu thông tin để thực hiện xóa.';
}

$conn->close();

// Chuyển hướng về trang danh sách hoặc trang quản lý
header('Location: baidang.php'); // Thay đổi tên trang theo yêu cầu
exit();
?>
