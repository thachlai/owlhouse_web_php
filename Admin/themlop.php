<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Thêm lớp</title>
<div class="content">
    <h2>Thêm Lớp</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="id_nganh">Ngành:</label>
        <select id="id_nganh" name="id_nganh" required>
            <?php
            // Fetch all "Ngành" options
            $sql_nganh = "SELECT * FROM nganh";
            $result_nganh = $conn->query($sql_nganh);
            while ($row_nganh = $result_nganh->fetch_assoc()) {
                echo "<option value='" . $row_nganh['id_nganh'] . "'>" . $row_nganh['tennganh'] . "</option>";
            }
            ?>
        </select>

        <label for="tenlop">Tên Lớp:</label>
        <input type="text" id="tenlop" name="tenlop" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota"></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">

        <button type="submit" name="submit">Thêm Lớp</button>
    </form>
</div>

<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_nganh = $_POST["id_nganh"];
    $tenlop = $_POST["tenlop"];
    $mota = $_POST["mota"];
    $img = $_FILES["img"]["name"];

    // Upload image
    if ($img) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    } else {
        $target_file = null;
    }

    // Insert data into the database
    $sql = "INSERT INTO lop (id_nganh, tenlop, mota, img) VALUES ('$id_nganh', '$tenlop', '$mota', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm Lớp thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>