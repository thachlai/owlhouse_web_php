<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Kiểm tra nếu có ID Ngành được gửi đến
if (isset($_GET['id_nganh'])) {
    $id_nganh = $_GET['id_nganh'];

    // Truy vấn thông tin Ngành từ cơ sở dữ liệu
    $sql = "SELECT * FROM nganh WHERE id_nganh='$id_nganh'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Ngành không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Ngành không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tennganh = $_POST['tennganh'];
    $id_gioi = $_POST['id_gioi'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

    // Cập nhật ảnh nếu có
    if (!empty($img)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        $sql_update = "UPDATE nganh SET tennganh='$tennganh', id_gioi='$id_gioi', mota='$mota', img='$img' WHERE id_nganh='$id_nganh'";
    } else {
        $sql_update = "UPDATE nganh SET tennganh='$tennganh', id_gioi='$id_gioi', mota='$mota' WHERE id_nganh='$id_nganh'";
    }

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật Ngành thành công.</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy danh sách Giới để hiển thị trong dropdown
$sql_gioi = "SELECT * FROM gioi";
$result_gioi = $conn->query($sql_gioi);
?>
<title>Sửa Cấp Ngành</title>
<!-- Biểu mẫu sửa Ngành -->
<div class="form-container">
    <h2>Sửa Ngành</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tennganh">Tên Ngành:</label>
        <input type="text" id="tennganh" name="tennganh" value="<?php echo htmlspecialchars($row['tennganh']); ?>" required>

        <label for="id_gioi">Giới:</label>
        <select id="id_gioi" name="id_gioi" required>
            <?php
            while ($row_gioi = $result_gioi->fetch_assoc()) {
                $selected = ($row['id_gioi'] == $row_gioi['id_gioi']) ? 'selected' : '';
                echo "<option value='" . $row_gioi['id_gioi'] . "' $selected>" . $row_gioi['tengioi'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Ngành" style="width: 80px; height: 80px;">
        <?php endif; ?>

        <button type="submit">Cập Nhật</button>
    </form>
</div>

<?php
$conn->close();
// include('footer.php');
?>
<script>
            CKEDITOR.replace('mota');
</script>