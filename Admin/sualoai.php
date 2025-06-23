<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Kiểm tra nếu có ID Loài được gửi đến
if (isset($_GET['id_loai'])) {
    $id_loai = $_GET['id_loai'];

    // Truy vấn thông tin Loài từ cơ sở dữ liệu
    $sql = "SELECT * FROM loai WHERE id_loai='$id_loai'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Loài không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Loài không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenloai = $_POST['tenloai'];
    $id_chi = $_POST['id_chi'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

    // Cập nhật ảnh nếu có
    if (!empty($img)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        $sql_update = "UPDATE loai SET tenloai='$tenloai', id_chi='$id_chi', mota='$mota', img='$img' WHERE id_loai='$id_loai'";
    } else {
        $sql_update = "UPDATE loai SET tenloai='$tenloai', id_chi='$id_chi', mota='$mota' WHERE id_loai='$id_loai'";
    }

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật Loài thành công.</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy danh sách Chi để hiển thị trong dropdown
$sql_chi = "SELECT * FROM chi";
$result_chi = $conn->query($sql_chi);
?>

<!-- Biểu mẫu sửa Loài -->
<div class="form-container">
    <h2>Sửa Loài</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tenloai">Tên Loài:</label>
        <input type="text" id="tenloai" name="tenloai" value="<?php echo htmlspecialchars($row['tenloai']); ?>" required>

        <label for="id_chi">Chi:</label>
        <select id="id_chi" name="id_chi" required>
            <?php
            while ($row_chi = $result_chi->fetch_assoc()) {
                $selected = ($row['id_chi'] == $row_chi['id_chi']) ? 'selected' : '';
                echo "<option value='" . $row_chi['id_chi'] . "' $selected>" . $row_chi['tenchi'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Loài" style="width: 80px; height: 80px;">
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
