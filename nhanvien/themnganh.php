<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_nhanvien();
include('header.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_gioi = $_POST["id_gioi"];
    $tennganh = $_POST["tennganh"];
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
    $sql = "INSERT INTO nganh (id_gioi, tennganh, mota, img) VALUES ('$id_gioi', '$tennganh', '$mota', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm Ngành thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>

<div class="content">
    <h2>Thêm Ngành</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="id_gioi">Giới:</label>
        <select id="id_gioi" name="id_gioi" required>
            <?php
            // Fetch all "Giới" options
            $sql_gioi = "SELECT * FROM gioi";
            $result_gioi = $conn->query($sql_gioi);
            while ($row_gioi = $result_gioi->fetch_assoc()) {
                echo "<option value='" . $row_gioi['id_gioi'] . "'>" . $row_gioi['tengioi'] . "</option>";
            }
            ?>
        </select>

        <label for="tennganh">Tên Ngành:</label>
        <input type="text" id="tennganh" name="tennganh" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota"></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">

        <button type="submit" name="submit">Thêm Ngành</button>
    </form>
</div>

<?php
$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>