<?php
session_start();
include('conn.php');
include('function.php');
include('header.php');
check_login();

// Lấy ID người dùng từ session
$id_nguoidung = $_SESSION['id_nguoidung'];

// Xử lý cập nhật hồ sơ
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $ngaysinh = $_POST['ngaysinh'];
    $diachi = $_POST['diachi'];
    $gioitinh = $_POST['gioitinh'];

    // Xử lý ảnh
    $imgPath = $user['img']; // Giữ lại đường dẫn cũ nếu không có ảnh mới

    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $imgTmpName = $_FILES['img']['tmp_name'];
        $imgName = basename($_FILES['img']['name']);
        $imgPath = '../uploads/' . $imgName;

        // Tạo thư mục uploads nếu chưa tồn tại
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        move_uploaded_file($imgTmpName, $imgPath);
    }

    $query = "UPDATE nguoidung SET fullname = ?, email = ?, ngaysinh = ?, diachi = ?, gioitinh = ?, img = ? WHERE id_nguoidung = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $fullname, $email, $ngaysinh, $diachi, $gioitinh, $imgPath, $id_nguoidung);
    $stmt->execute();
    
    echo '<script>
            alert("Cập nhật hồ sơ thành công!");
            window.location.href = "hoso.php";
          </script>';
    exit();
}

// Truy vấn để lấy thông tin người dùng
$query_user = "SELECT * FROM nguoidung WHERE id_nguoidung = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $id_nguoidung);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Hồ Sơ</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
        }

        .update-profile-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .update-profile-form h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .update-profile-form label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .update-profile-form input, 
        .update-profile-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .update-profile-form button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .update-profile-form button:hover {
            background-color: #0056b3;
        }

        .password-update-btn {
            display: block;
            width: calc(100% - 22px);
            padding: 12px;
            margin: 20px auto 0;
            background-color: #28a745;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .password-update-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="update-profile-form">
        <h1>Cập Nhật Hồ Sơ</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="fullname">Họ Tên</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="ngaysinh">Ngày Sinh</label>
            <input type="date" id="ngaysinh" name="ngaysinh" value="<?php echo htmlspecialchars($user['ngaysinh']); ?>" >

            <label for="diachi">Địa Chỉ</label>
            <input type="text" id="diachi" name="diachi" value="<?php echo htmlspecialchars($user['diachi']); ?>" >

            <label for="gioitinh">Giới Tính</label>
            <select id="gioitinh" name="gioitinh">
                <option value="Nam" <?php echo ($user['gioitinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                <option value="Nữ" <?php echo ($user['gioitinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                <option value="Khác" <?php echo ($user['gioitinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
            </select>

            <label for="img">Ảnh Đại Diện</label>
            <input type="file" id="img" name="img" accept="image/*" onchange="previewImage()">
            <br>
            <img  id="preview" src="" alt="Ảnh Xem Trước" style="display: none; width: 150px; height: 150px;">
        <br>
            <button type="submit">Cập Nhật</button>
        </form>

        <!-- Thêm nút cập nhật mật khẩu -->
        <a href="capnhatmatkhau.php" class="password-update-btn">Cập Nhật Mật Khẩu</a>
    </div>
</body>
</html>
<script>
    function previewImage() {
    var file = document.getElementById('img').files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById('preview');
        img.src = e.target.result;
        img.style.display = 'block';
    }
    reader.readAsDataURL(file);
}
</script>
