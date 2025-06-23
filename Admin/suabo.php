<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Kiểm tra nếu có ID Bộ được gửi đến
if (isset($_GET['id_bo'])) {
    $id_bo = $_GET['id_bo'];

    // Truy vấn thông tin Bộ từ cơ sở dữ liệu
    $sql = "SELECT * FROM bo WHERE id_bo='$id_bo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Bộ không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Bộ không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenbo = $_POST['tenbo'];
    $id_lop = $_POST['id_lop'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

    // Cập nhật ảnh nếu có
    if (!empty($img)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        $sql_update = "UPDATE bo SET tenbo='$tenbo', id_lop='$id_lop', mota='$mota', img='$img' WHERE id_bo='$id_bo'";
    } else {
        $sql_update = "UPDATE bo SET tenbo='$tenbo', id_lop='$id_lop', mota='$mota' WHERE id_bo='$id_bo'";
    }

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật Bộ thành công.</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy danh sách Lớp để hiển thị trong dropdown
$sql_lop = "SELECT * FROM lop";
$result_lop = $conn->query($sql_lop);
?>
<title>Sửa cấp bộ</title>
<!-- Biểu mẫu sửa Bộ -->
<div class="form-container">
    <h2>Sửa Bộ</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tenbo">Tên Bộ:</label>
        <input type="text" id="tenbo" name="tenbo" value="<?php echo htmlspecialchars($row['tenbo']); ?>" required>

        <label for="id_lop">Lớp:</label>
        <select id="id_lop" name="id_lop" required>
            <?php
            while ($row_lop = $result_lop->fetch_assoc()) {
                $selected = ($row['id_lop'] == $row_lop['id_lop']) ? 'selected' : '';
                echo "<option value='" . $row_lop['id_lop'] . "' $selected>" . $row_lop['tenlop'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Bộ" style="width: 80px; height: 80px;">
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
