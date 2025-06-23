<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Kiểm tra nếu có ID Sinh Vật được gửi đến
if (isset($_GET['id_sinhvat'])) {
    $id_sinhvat = $_GET['id_sinhvat'];

    // Truy vấn thông tin Sinh Vật từ cơ sở dữ liệu
    $sql = "SELECT * FROM sinhvat WHERE id_sinhvat='$id_sinhvat'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<div class='error-message'>Sinh Vật không tồn tại.</div>";
        exit;
    }
} else {
    echo "<div class='error-message'>ID Sinh Vật không được cung cấp.</div>";
    exit;
}

// Xử lý yêu cầu cập nhật khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tensinhvat = $_POST['tensinhvat'];
    $id_loai = $_POST['id_loai'];
    $id_chi = $_POST['id_chi'];
    $id_ho = $_POST['id_ho'];
    $id_bo = $_POST['id_bo'];
    $id_lop = $_POST['id_lop'];
    $id_nganh = $_POST['id_nganh'];
    $id_gioi = $_POST['id_gioi'];
    $mota = $_POST['mota'];
    $img = $_FILES['img']['name'];

   // Cập nhật ảnh chính nếu có
   if (!empty($img)) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["img"]["name"]);
    move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    $sql_update = "UPDATE sinhvat SET tensinhvat='$tensinhvat', id_loai='$id_loai', mota='$mota', img='$img' WHERE id_sinhvat='$id_sinhvat'";
} else {
    $sql_update = "UPDATE sinhvat SET tensinhvat='$tensinhvat', id_loai='$id_loai', mota='$mota' WHERE id_sinhvat='$id_sinhvat'";
}

if ($conn->query($sql_update) === TRUE) {
    // Xử lý ảnh phụ
    if (isset($_FILES['imgs'])) {
        $totalFiles = count($_FILES['imgs']['name']);
        for ($i = 0; $i < $totalFiles; $i++) {
            if (!empty($_FILES['imgs']['name'][$i])) {
                $img_name = $_FILES['imgs']['name'][$i];
                $target_file = $target_dir . basename($img_name);
                move_uploaded_file($_FILES['imgs']['tmp_name'][$i], $target_file);
                $sql_img = "INSERT INTO anh_sinhvat (id_sinhvat, anh) VALUES ('$id_sinhvat', '$img_name')";
                $conn->query($sql_img);
            }
        }
    }

    // Xóa ảnh phụ nếu có
    if (isset($_POST['delete_imgs'])) {
        $delete_ids = $_POST['delete_imgs'];
        foreach ($delete_ids as $id_anhsv) {
            $query = "SELECT anh FROM anh_sinhvat WHERE id_anhsv='$id_anhsv'";
            $result = $conn->query($query);
            $row = $result->fetch_assoc();
            $img_path = "../uploads/" . $row['anh'];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
            $sql_delete = "DELETE FROM anh_sinhvat WHERE id_anhsv='$id_anhsv'";
            $conn->query($sql_delete);
        }
    }

    echo "<div class='success-message'>Cập nhật Sinh Vật thành công.</div>";
} else {
    echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
}
}

// Lấy danh sách Giới, Ngành, Lớp, Bộ, Họ, Chi, Loài để hiển thị trong dropdown
$sql_gioi = "SELECT * FROM gioi";
$result_gioi = $conn->query($sql_gioi);

$sql_nganh = "SELECT * FROM nganh";
$result_nganh = $conn->query($sql_nganh);

$sql_lop = "SELECT * FROM lop";
$result_lop = $conn->query($sql_lop);

$sql_bo = "SELECT * FROM bo";
$result_bo = $conn->query($sql_bo);

$sql_ho = "SELECT * FROM ho";
$result_ho = $conn->query($sql_ho);

$sql_chi = "SELECT * FROM chi";
$result_chi = $conn->query($sql_chi);

$sql_loai = "SELECT * FROM loai";
$result_loai = $conn->query($sql_loai);
?>
<title>Sửa Sinh Vật</title>
<!-- Biểu mẫu sửa Sinh Vật -->
<div class="form-container">
    <h2>Sửa Sinh Vật</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="tensinhvat">Tên Sinh Vật:</label>
        <input type="text" id="tensinhvat" name="tensinhvat" value="<?php echo htmlspecialchars($row['tensinhvat']); ?>" required>

        <label for="id_gioi">Giới:</label>
        <select id="id_gioi" name="id_gioi" required>
            <?php
            while ($row_gioi = $result_gioi->fetch_assoc()) {
                $selected = ($row['id_gioi'] == $row_gioi['id_gioi']) ? 'selected' : '';
                echo "<option value='" . $row_gioi['id_gioi'] . "' $selected>" . $row_gioi['tengioi'] . "</option>";
            }
            ?>
        </select>

        <label for="id_nganh">Ngành:</label>
        <select id="id_nganh" name="id_nganh" required>
            <?php
            while ($row_nganh = $result_nganh->fetch_assoc()) {
                $selected = ($row['id_nganh'] == $row_nganh['id_nganh']) ? 'selected' : '';
                echo "<option value='" . $row_nganh['id_nganh'] . "' $selected>" . $row_nganh['tennganh'] . "</option>";
            }
            ?>
        </select>

        <label for="id_lop">Lớp:</label>
        <select id="id_lop" name="id_lop" required>
            <?php
            while ($row_lop = $result_lop->fetch_assoc()) {
                $selected = ($row['id_lop'] == $row_lop['id_lop']) ? 'selected' : '';
                echo "<option value='" . $row_lop['id_lop'] . "' $selected>" . $row_lop['tenlop'] . "</option>";
            }
            ?>
        </select>

        <label for="id_bo">Bộ:</label>
        <select id="id_bo" name="id_bo" required>
            <?php
            while ($row_bo = $result_bo->fetch_assoc()) {
                $selected = ($row['id_bo'] == $row_bo['id_bo']) ? 'selected' : '';
                echo "<option value='" . $row_bo['id_bo'] . "' $selected>" . $row_bo['tenbo'] . "</option>";
            }
            ?>
        </select>

        <label for="id_ho">Họ:</label>
        <select id="id_ho" name="id_ho" required>
            <?php
            while ($row_ho = $result_ho->fetch_assoc()) {
                $selected = ($row['id_ho'] == $row_ho['id_ho']) ? 'selected' : '';
                echo "<option value='" . $row_ho['id_ho'] . "' $selected>" . $row_ho['tenho'] . "</option>";
            }
            ?>
        </select>

        <label for="id_chi">Chi:</label>
        <select id="id_chi" name="id_chi" required>
            <?php
            while ($row_chi = $result_chi->fetch_assoc()) {
                $selected = ($row['id_chi'] == $row_chi['id_chi']) ? 'selected' : '';
                echo "<option value='" . $row_chi['id_chi'] . "' $selected>" . $row_chi['tenchi'] . "</option>";
            }
            ?>
        </select>

        <label for="id_loai">Loài:</label>
        <select id="id_loai" name="id_loai" required>
            <?php
            while ($row_loai = $result_loai->fetch_assoc()) {
                $selected = ($row['id_loai'] == $row_loai['id_loai']) ? 'selected' : '';
                echo "<option value='" . $row_loai['id_loai'] . "' $selected>" . $row_loai['tenloai'] . "</option>";
            }
            ?>
        </select>

        <label for="mota">Mô Tả:</label>
        <textarea id="mota" name="mota" rows="4" required><?php echo htmlspecialchars($row['mota']); ?></textarea>

        <label for="img">Hình Ảnh Chính (chọn ảnh mới nếu cần):</label>
        <input type="file" id="img" name="img">
        <?php if (!empty($row['img'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="Hình ảnh Sinh Vật" style="width: 80px; height: 80px;">
        <?php endif; ?>

        <!-- Ảnh phụ -->
        <label>Ảnh Phụ:</label>
        <div id="image_fields">
            <?php
            $query = "SELECT * FROM anh_sinhvat WHERE id_sinhvat = '$id_sinhvat'";
            $result = mysqli_query($conn, $query);
            while ($row_image = mysqli_fetch_assoc($result)) {
                echo '<div class="image_field">';
                echo '<input type="file" name="imgs[]" accept="image/*" onchange="previewImages(this)" />';
                echo '<button type="button" onclick="removeImageField(this)">Xóa</button>';
                echo '<br><div class="image_preview"><img src="../uploads/' . htmlspecialchars($row_image['anh']) . '" style="width: 100px; margin-right: 10px;"></div>';
                echo '<input type="checkbox" name="delete_imgs[]" value="' . htmlspecialchars($row_image['id_anhsv']) . '"> Xóa ảnh này';
                echo '</div>';
            }
            ?>
        </div>
        <button type="button" onclick="addImageField()">Thêm Ảnh Phụ</button><br><br>

        <button type="submit">Cập Nhật</button>

    </form>
</div>

<?php
$conn->close();
?>
<script>
 function addImageField() {
        const container = document.getElementById('image_fields');
        const newField = document.createElement('div');
        newField.className = 'image_field';
        newField.innerHTML = `
            <input type="file" name="imgs[]" accept="image/*" onchange="previewImages(this)" />
            <button type="button" onclick="removeImageField(this)">Xóa</button>
            <br><div class="image_preview"></div>
        `;
        container.appendChild(newField);
    }

    function removeImageField(button) {
        const field = button.parentNode;
        field.remove();
    }

    function previewImages(input) {
        const files = input.files;
        const previewDiv = input.parentNode.querySelector('.image_preview');
        previewDiv.innerHTML = '';
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.marginRight = '10px';
                previewDiv.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }

    CKEDITOR.replace('mota');</script>
</script>