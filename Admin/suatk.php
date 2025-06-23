<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin(); // Kiểm tra quyền admin
include('header.php');

// Kiểm tra và lấy ID người dùng từ URL
if (isset($_GET['id_nguoidung']) && !empty($_GET['id_nguoidung'])) {
    $id_nguoidung = $_GET['id_nguoidung'];
} else {
    echo "<div class='error-message'>ID tài khoản không được cung cấp.</div>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? md5($_POST['password']) : null; // Mã hóa mật khẩu nếu có thay đổi
    $gioitinh = $_POST['gioitinh'];
    $diachi = $_POST['diachi'];
    $ngaysinh = $_POST['ngaysinh'];
    $sdt = $_POST['sdt'];
    $trangthai = $_POST['trangthai'];
    $quyen = $_POST['quyen'];

    // Xử lý ảnh
    $img_name = $_POST['current_img']; // Giữ tên ảnh hiện tại nếu không có ảnh mới
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $img = $_FILES['img'];
        $img_name = $img['name'];
        $img_tmp = $img['tmp_name'];
        $img_size = $img['size'];

        // Kiểm tra kích thước ảnh và điều chỉnh nếu cần
        if ($img_size > 5000000) { // Ví dụ: 5MB
            $image_info = getimagesize($img_tmp);
            $image_width = $image_info[0];
            $image_height = $image_info[1];

            // Thay đổi kích thước nếu quá lớn
            $new_width = 1000; // Đặt kích thước mới
            $new_height = ($image_height / $image_width) * $new_width;

            $src = imagecreatefromstring(file_get_contents($img_tmp));
            $dst = imagecreatetruecolor($new_width, $new_height);

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
            $upload_path = '../uploads/' . $img_name;
            imagejpeg($dst, $upload_path);
            imagedestroy($src);
            imagedestroy($dst);
        } else {
            move_uploaded_file($img_tmp, '../uploads/' . $img_name);
        }
    }

    // Cập nhật dữ liệu vào cơ sở dữ liệu
    $sql = "UPDATE nguoidung SET fullname='$fullname', email='$email', gioitinh='$gioitinh', diachi='$diachi', ngaysinh='$ngaysinh', sdt='$sdt', trangthai='$trangthai', quyen='$quyen', img='$img_name'" .
           ($password ? ", password='$password'" : "") .
           " WHERE id_nguoidung='$id_nguoidung'";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Cập nhật tài khoản thành công</div>";
    } else {
        echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
    }
}

// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT * FROM nguoidung WHERE id_nguoidung='$id_nguoidung'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$conn->close();
?>
<title>Sửa tài khoản</title>
<div class="form-container">
    <form action="" method="POST" enctype="multipart/form-data">
        <h2>Sửa Tài Khoản</h2>
        <label for="fullname">Họ và Tên:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="password">Mật Khẩu (để trống nếu không thay đổi):</label>
        <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
        title="Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một số.">

        <label for="gioitinh">Giới Tính:</label>
        <select id="gioitinh" name="gioitinh">
            <option value="Nam" <?php if ($user['gioitinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
            <option value="Nữ" <?php if ($user['gioitinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
            <option value="Khác" <?php if ($user['gioitinh'] == 'Khác') echo 'selected'; ?>>Khác</option>
        </select>

        <label for="diachi">Địa Chỉ:</label>
        <textarea id="diachi" name="diachi" required><?php echo htmlspecialchars($user['diachi']); ?></textarea>

        <label for="ngaysinh">Ngày Sinh:</label>
        <input type="date" id="ngaysinh" name="ngaysinh" value="<?php echo htmlspecialchars($user['ngaysinh']); ?>" required>

        <label for="sdt">Số Điện Thoại:</label>
        <input type="text" id="sdt" name="sdt" value="<?php echo htmlspecialchars($user['sdt']); ?>" required>

        <label for="trangthai">Trạng Thái:</label>
        <select id="trangthai" name="trangthai">
            <option value="0" <?php if ($user['trangthai'] == 0) echo 'selected'; ?>>Mở</option>
            <option value="1" <?php if ($user['trangthai'] == 1) echo 'selected'; ?>>Khóa</option>
        </select>

        <label for="quyen">Quyền:</label>
        <select id="quyen" name="quyen">
            <option value="0" <?php if ($user['quyen'] == 0) echo 'selected'; ?>>Người dùng</option>
            <option value="1" <?php if ($user['quyen'] == 1) echo 'selected'; ?>>Admin</option>
            <option value="2" <?php if ($user['quyen'] == 2) echo 'selected'; ?>>Nhà sinh vật học</option>
        </select>

        <label for="img">Ảnh (để trống nếu không thay đổi):</label>
        <input type="file" id="img" name="img" accept="image/*" onchange="previewImage()">
        <input type="hidden" name="current_img" value="<?php echo htmlspecialchars($user['img']); ?>">
        <img id="preview" src="<?php echo '../uploads/' . htmlspecialchars($user['img']); ?>" alt="Ảnh Hiện Tại" style="display: block; width: 150px; height: 150px; margin-bottom: 10px;">

        <button type="submit">Cập Nhật Tài Khoản</button>
    </form>
</div>

<script>
function previewImage() {
    var file = document.getElementById('img').files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById('preview');
        img.src = e.target.result;
    }
    reader.readAsDataURL(file);
}
</script>

<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #f9f9f9;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .form-container label {
        display: block;
        margin: 10px 0 5px;
    }

    .form-container input,
    .form-container textarea,
    .form-container select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-container button {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-container button:hover {
        background-color: #0056b3;
    }
</style>
