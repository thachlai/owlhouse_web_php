<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_nhanvien();
include('header.php');

// Kiểm tra nếu có ID Chi được gửi đến
if (isset($_GET['id_chi'])) {
    $id_chi = $_GET['id_chi'];

    // Truy vấn thông tin Chi từ cơ sở dữ liệu
    $sql = "SELECT * FROM chi WHERE id_chi='$id_chi'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Chi không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Chi không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenchi = $_POST['tenchi'];
    $id_ho = $_POST['id_ho'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

    // Cập nhật ảnh nếu có
    if (!empty($img)) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["img"]["name"]);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        $sql_update = "UPDATE chi SET tenchi='$tenchi', id_ho='$id_ho', mota='$mota', img='$img' WHERE id_chi='$id_chi'";
    } else {
        $sql_update = "UPDATE chi SET tenchi='$tenchi', id_ho='$id_ho', mota='$mota' WHERE id_chi='$id_chi'";
    }

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật Chi thành công.</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy danh sách Họ để hiển thị trong dropdown
$sql_ho = "SELECT * FROM ho";
$result_ho = $conn->query($sql_ho);
?>

<!-- Biểu mẫu sửa Chi -->
<div class="form-container">
    <h2>Sửa Chi</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tenchi">Tên Chi:</label>
        <input type="text" id="tenchi" name="tenchi" value="<?php echo htmlspecialchars($row['tenchi']); ?>" required>

        <label for="id_ho">Họ:</label>
        <select id="id_ho" name="id_ho" required>
            <?php
            while ($row_ho = $result_ho->fetch_assoc()) {
                $selected = ($row['id_ho'] == $row_ho['id_ho']) ? 'selected' : '';
                echo "<option value='" . $row_ho['id_ho'] . "' $selected>" . $row_ho['tenho'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Chi" style="width: 80px; height: 80px;">
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
