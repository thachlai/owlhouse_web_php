<?php
$sv = "sql303.infinityfree.com";
$user = "if0_37084794";
$pw = "OWLhouse1910";
$db = "if0_37084794_animal_web";

$conn = new mysqli ($sv,$user,$pw,$db);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>