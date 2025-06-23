<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_nhanvien();
include('header.php');

// Kiểm tra nếu có ID Họ được gửi đến
if (isset($_GET['id_ho'])) {
    $id_ho = $_GET['id_ho'];

    // Truy vấn thông tin Họ từ cơ sở dữ liệu
    $sql = "SELECT * FROM ho WHERE id_ho='$id_ho'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Họ không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Họ không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenho = $_POST['tenho'];
    $id_bo = $_POST['id_bo'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

    // Cập nhật ảnh nếu có
    if (!empty($img)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        $sql_update = "UPDATE ho SET tenho='$tenho', id_bo='$id_bo', mota='$mota', img='$img' WHERE id_ho='$id_ho'";
    } else {
        $sql_update = "UPDATE ho SET tenho='$tenho', id_bo='$id_bo', mota='$mota' WHERE id_ho='$id_ho'";
    }

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật Họ thành công.</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy danh sách Bộ để hiển thị trong dropdown
$sql_bo = "SELECT * FROM bo";
$result_bo = $conn->query($sql_bo);
?>

<!-- Biểu mẫu sửa Họ -->
<div class="form-container">
    <h2>Sửa Họ</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tenho">Tên Họ:</label>
        <input type="text" id="tenho" name="tenho" value="<?php echo htmlspecialchars($row['tenho']); ?>" required>

        <label for="id_bo">Bộ:</label>
        <select id="id_bo" name="id_bo" required>
            <?php
            while ($row_bo = $result_bo->fetch_assoc()) {
                $selected = ($row['id_bo'] == $row_bo['id_bo']) ? 'selected' : '';
                echo "<option value='" . $row_bo['id_bo'] . "' $selected>" . $row_bo['tenbo'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Họ" style="width: 80px; height: 80px;">
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
