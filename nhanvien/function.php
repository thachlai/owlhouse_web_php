<?php

function check_login() {
    if (!isset($_SESSION['id_nguoidung'])) {
        header("Location: ../user/dangnhap.php");
        exit();
    }
}

function check_admin() {
    if (!isset($_SESSION['id_nguoidung']) || $_SESSION['quyen'] != 1) {
        header("Location: ../user/trangchu.php");
        exit();
    }
}
function check_nhanvien() {
    if (!isset($_SESSION['id_nguoidung']) || $_SESSION['quyen'] != 2) {
        header("Location: ../user/trangchu.php");
        exit();
    }
}
?>