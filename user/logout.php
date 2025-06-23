<?php
// Bắt đầu phiên nếu chưa được bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa tất cả các biến phiên
$_SESSION = array();

// Xóa cookie phiên nếu có
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Hủy phiên
session_destroy();

// Chuyển hướng về trang chủ hoặc trang đăng nhập
header("Location: dangnhap.php"); // Hoặc điều chỉnh đến trang bạn muốn
exit();
?>
