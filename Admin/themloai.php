<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Thêm Loài</title>
<div class="content">
    <h2>Thêm Loài</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="id_chi">Chi:</label>
        <select id="id_chi" name="id_chi" required>
            <?php
            $sql_chi = "SELECT * FROM chi";
            $result_chi = $conn->query($sql_chi);
            while ($row_chi = $result_chi->fetch_assoc()) {
                echo "<option value='" . $row_chi['id_chi'] . "'>" . $row_chi['tenchi'] . "</option>";
            }
            ?>
        </select>

        <label for="tenloai">Tên Loài:</label>
        <input type="text" id="tenloai" name="tenloai" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota"></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">

        <button type="submit" name="submit">Thêm Loài</button>
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_chi = $_POST["id_chi"];
    $tenloai = $_POST["tenloai"];
    $mota = $_POST["mota"];
    $img = $_FILES["img"]["name"];

    if ($img) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    } else {
        $target_file = null;
    }

    $sql = "INSERT INTO loai (id_chi, tenloai, mota, img) VALUES ('$id_chi', '$tenloai', '$mota', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm Loài thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>