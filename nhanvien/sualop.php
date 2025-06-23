<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_nhanvien();
include('header.php');

// Kiểm tra nếu có ID Lớp được gửi đến
if (isset($_GET['id_lop'])) {
    $id_lop = $_GET['id_lop'];

    // Truy vấn thông tin Lớp từ cơ sở dữ liệu
    $sql = "SELECT * FROM lop WHERE id_lop='$id_lop'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Lớp không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Lớp không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenlop = $_POST['tenlop'];
    $id_nganh = $_POST['id_nganh'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

    // Cập nhật ảnh nếu có
    if (!empty($img)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        $sql_update = "UPDATE lop SET tenlop='$tenlop', id_nganh='$id_nganh', mota='$mota', img='$img' WHERE id_lop='$id_lop'";
    } else {
        $sql_update = "UPDATE lop SET tenlop='$tenlop', id_nganh='$id_nganh', mota='$mota' WHERE id_lop='$id_lop'";
    }

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật Lớp thành công.</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy danh sách Ngành để hiển thị trong dropdown
$sql_nganh = "SELECT * FROM nganh";
$result_nganh = $conn->query($sql_nganh);
?>

<!-- Biểu mẫu sửa Lớp -->
<div class="form-container">
    <h2>Sửa Lớp</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tenlop">Tên Lớp:</label>
        <input type="text" id="tenlop" name="tenlop" value="<?php echo htmlspecialchars($row['tenlop']); ?>" required>

        <label for="id_nganh">Ngành:</label>
        <select id="id_nganh" name="id_nganh" required>
            <?php
            while ($row_nganh = $result_nganh->fetch_assoc()) {
                $selected = ($row['id_nganh'] == $row_nganh['id_nganh']) ? 'selected' : '';
                echo "<option value='" . $row_nganh['id_nganh'] . "' $selected>" . $row_nganh['tennganh'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Lớp" style="width: 80px; height: 80px;">
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