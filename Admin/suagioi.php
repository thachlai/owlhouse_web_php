<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Lấy id_gioi từ tham số URL
$id_gioi = isset($_GET['id_gioi']) ? $_GET['id_gioi'] : '';

// Kiểm tra xem có phải là yêu cầu POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $new_tengioi = $_POST['tengioi'];
    $new_mota = $_POST['mota'];

    // Xử lý hình ảnh
    $new_img = $_FILES['img']['name'];
    if (!empty($new_img)) {
        $target = "../uploads/" . basename($new_img);
        move_uploaded_file($_FILES['img']['tmp_name'], $target);
        $img_sql = ", img = '$new_img'";
    } else {
        $img_sql = "";
    }

    // Thực hiện truy vấn để cập nhật dữ liệu
    $sql_update = "UPDATE gioi SET 
                    tengioi='$new_tengioi',
                    mota='$new_mota' $img_sql
                    WHERE id_gioi='$id_gioi'";

    if ($conn->query($sql_update) === TRUE) {
        echo "<div class='success-message'>Cập nhật giới thành công!</div>";

        // Chuyển hướng đến trang loai.php sau một khoảng thời gian ngắn
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'gioi.php';
                }, 2000); // 2000 mili giây = 2 giây
              </script>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $sql_update . "<br>" . $conn->error . "</div>";
    }
} else if (!empty($id_gioi)) {
    // Lấy dữ liệu của giới từ cơ sở dữ liệu để hiển thị
    $sql_gioi = "SELECT * FROM gioi WHERE id_gioi='$id_gioi'";
    $result_gioi = $conn->query($sql_gioi);

    if ($result_gioi->num_rows > 0) {
        $row_gioi = $result_gioi->fetch_assoc();
    } else {
        echo "<div class='error-message'>Không tìm thấy giới.</div>";
    }
}
?>

<div class="content">
    <h2>Sửa Cấp Giới</h2>

    <form action="suagioi.php?id_gioi=<?php echo $id_gioi; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_gioi" value="<?php echo htmlspecialchars($row_gioi['id_gioi'] ?? ''); ?>">

        <label for="tengioi">Tên Giới:</label>
        <input type="text" id="tengioi" name="tengioi" value="<?php echo htmlspecialchars($row_gioi['tengioi'] ?? ''); ?>" required>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row_gioi['mota'] ?? ''); ?></textarea>

        <label for="img">Hình Ảnh:</label>
        <input type="file" id="img" name="img">
        <?php if (isset($row_gioi['img']) && !empty($row_gioi['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row_gioi['img']); ?>" alt="Hình ảnh Giới" style="width: 80px; height: 80px;">
        <?php endif; ?>

        <button type="submit">Cập Nhật Giới</button>
    </form>
</div>

<script>
// Bạn có thể giữ nguyên phần script nếu cần
</script>

<?php
$conn->close();
// include('footer.php');
?>
<script>
            CKEDITOR.replace('mota');
</script>