<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();

// Email của tài khoản không thể xóa
$protected_email = 'tranvokimthach@gmail.com';

// Kiểm tra nếu tham số 'id' được cung cấp qua URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Chuyển đổi ID thành số nguyên để tránh SQL injection

    // Lấy email của tài khoản cần xóa để kiểm tra
    $sql_check_email = "SELECT email FROM nguoidung WHERE id_nguoidung = $id";
    $result_check_email = $conn->query($sql_check_email);

    if ($result_check_email->num_rows > 0) {
        $row = $result_check_email->fetch_assoc();
        $email = $row['email'];

        // Nếu email là của tài khoản không thể xóa
        if ($email === $protected_email) {
            echo "<div class='error-message'>Đây là tài khoản quản trị không thể xóa được.</div>";
            $conn->close();
            exit();
        }
    }

    // Xóa tài khoản
    $sql = "DELETE FROM nguoidung WHERE id_nguoidung = $id";

    if ($conn->query($sql) === TRUE) {
        // Nếu xóa thành công, chuyển hướng về trang taikhoan.php
        header("Location: taikhoan.php");
        exit();
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Thiếu tham số.";
}

$conn->close();
?>
