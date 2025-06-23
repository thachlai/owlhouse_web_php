<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Thêm Chi</title>
<div class="content">
    <h2>Thêm Chi</h2>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="id_ho">Họ:</label>
        <select id="id_ho" name="id_ho" required>
            <?php
            $sql_ho = "SELECT * FROM ho";
            $result_ho = $conn->query($sql_ho);
            while ($row_ho = $result_ho->fetch_assoc()) {
                echo "<option value='" . $row_ho['id_ho'] . "'>" . $row_ho['tenho'] . "</option>";
            }
            ?>
        </select>

        <label for="tenchi">Tên Chi:</label>
        <input type="text" id="tenchi" name="tenchi" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota"></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">

        <button type="submit" name="submit">Thêm Chi</button>
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_ho = $_POST["id_ho"];
    $tenchi = $_POST["tenchi"];
    $mota = $_POST["mota"];
    $img = $_FILES["img"]["name"];

    if ($img) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    } else {
        $target_file = null;
    }

    $sql = "INSERT INTO chi (id_ho, tenchi, mota, img) VALUES ('$id_ho', '$tenchi', '$mota', '$target_file')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Thêm Chi thành công!</div>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<script>
            CKEDITOR.replace('mota');
</script>