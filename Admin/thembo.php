<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Thêm Cấp Bộ</title>
<div class="content">
    <h2>Thêm Bộ</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="id_lop">Lớp:</label>
        <select id="id_lop" name="id_lop" required>
            <?php
            // Fetch all "Lớp" options
            $sql_lop = "SELECT * FROM lop";
            $result_lop = $conn->query($sql_lop);
            while ($row_lop = $result_lop->fetch_assoc()) {
                echo "<option value='" . $row_lop['id_lop'] . "'>" . $row_lop['tenlop'] . "</option>";
            }
            ?>
        </select>

        <label for="tenbo">Tên Bộ:</label>
        <input type="text" id="tenbo" name="tenbo" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota"></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">

        <button type="submit" name="submit">Thêm Bộ</button>
    </form>
</div>

<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_lop = $_POST["id_lop"];
    $tenbo = $_POST["tenbo"];
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
    $sql = "INSERT INTO bo (id_lop, tenbo, mota, img) VALUES ('$id_lop', '$tenbo', '$mota', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm Bộ thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>