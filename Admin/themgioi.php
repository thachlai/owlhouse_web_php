<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $tengioi = $_POST["tengioi"];
    $mota = $_POST["mota"];
    $img = $_FILES["img"]["name"]; // File upload

    // Handle file upload
    if (isset($_FILES["img"]) && $_FILES["img"]["error"] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    }

    // Thực hiện truy vấn để thêm giới vào cơ sở dữ liệu
    $sql = "INSERT INTO gioi (tengioi, mota, img) VALUES ('$tengioi', '$mota', '$img')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm giới thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<title>Thêm Giới</title>
<div class="content">
    <h2>Thêm Giới</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tengioi">Tên Giới:</label>
        <input type="text" id="tengioi" name="tengioi" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img" accept="image/*" required>

        <button type="submit" name="submit">Thêm Giới</button>
    </form>
</div>

<?php
$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>