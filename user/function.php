<?php


function check_login() {
    if (!isset($_SESSION['id_nguoidung'])) {
        header("Location: ./dangnhap.php");
        exit();
    }
}

function check_admin() {
    if (!isset($_SESSION['id_nguoidung']) || $_SESSION['quyen'] != 1) {
        header("Location: ./trangchu.php.php");
        exit();
    }
}
?>