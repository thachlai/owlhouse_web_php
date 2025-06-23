<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Thêm Họ</title>
<div class="content">
    <h2>Thêm Họ</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="id_bo">Bộ:</label>
        <select id="id_bo" name="id_bo" required>
            <?php
            $sql_bo = "SELECT * FROM bo";
            $result_bo = $conn->query($sql_bo);
            while ($row_bo = $result_bo->fetch_assoc()) {
                echo "<option value='" . $row_bo['id_bo'] . "'>" . $row_bo['tenbo'] . "</option>";
            }
            ?>
        </select>

        <label for="tenho">Tên Họ:</label>
        <input type="text" id="tenho" name="tenho" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota"></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">

        <button type="submit" name="submit">Thêm Họ</button>
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_bo = $_POST["id_bo"];
    $tenho = $_POST["tenho"];
    $mota = $_POST["mota"];
    $img = $_FILES["img"]["name"];

    if ($img) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    } else {
        $target_file = null;
    }

    $sql = "INSERT INTO ho (id_bo, tenho, mota, img) VALUES ('$id_bo', '$tenho', '$mota', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm Họ thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>