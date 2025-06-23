<?php
include('conn.php'); // Kết nối tới cơ sở dữ liệu

$error_message = '';
$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gioitinh = $_POST['gioitinh'];
    $ngaysinh = $_POST['ngaysinh'];
    $sdt = $_POST['sdt'];
    $diachi = $_POST['diachi'];

    // Kiểm tra mật khẩu
    if ($password !== $confirm_password) {
        $error_message = 'Mật khẩu và xác nhận mật khẩu không khớp.';
    } else {
        // Kiểm tra email đã tồn tại
        $stmt = $conn->prepare("SELECT * FROM nguoidung WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = 'Email đã tồn tại.';
        } else {
            // Mã hóa mật khẩu
            $hashed_password = md5($password);

            // Xử lý ảnh đại diện
            $img_path = '';
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $img_name = basename($_FILES['img']['name']);
                $img_tmp_name = $_FILES['img']['tmp_name'];
                $img_path = '../uploads/' . $img_name;
                move_uploaded_file($img_tmp_name, $img_path);
            }

            // Thêm người dùng mới vào cơ sở dữ liệu
            $stmt = $conn->prepare("INSERT INTO nguoidung (fullname, email, password, img, gioitinh, ngaysinh, sdt, diachi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fullname, $email, $hashed_password, $img_path, $gioitinh, $ngaysinh, $sdt, $diachi);

            if ($stmt->execute()) {
                $registrationSuccess = true;
            } else {
                $error_message = 'Đã xảy ra lỗi trong quá trình đăng ký.';
            }

            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
    <title>Đăng Ký</title>

  
    <style>
        body {
            background: url(../img/natural2.webp);
            background-size: 25% 25%;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70px;
        }
        .login-box {
            width: 700px;
            background-color: #ffffffb8;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 190px auto 60px auto;
        }
        .form-group-left {
            float: left;
            width: 50%;
        }
        .form-group-right {
            float: right;
            width: 50%;
        }
        .form-group {
            margin: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        /* .form-group select {
            width: 70%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group option {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        } */
        .password-toggle {
            position: relative;
        }
        .password-toggle input[type="password"] {
            padding-right: 30px;
        }
        .password-toggle .toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .forgot-password {
            margin-top: 10px;
        }
        .form-group select[name="gioitinh"] {
            height: 35px;
            width: 299px;
            font-size: 13px;
            padding-left: 5px;
        }
        .form-group input[name="img"] {
            padding-left: 0;
            margin-top: 5px;
        }
        .form-group input[name="img"]:hover {
            cursor: pointer;
        }
        
        .form-group input[name="ngaysinh"] {
            height: 35px;
        }
        .register-link {
            margin-top: 10px;
        }
        .register-button {
            width: 100%;
            padding: 8.5px;
            box-sizing: border-box;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .register-button:hover {
            background-color: #0DB7EA;
            color: #000;
        }
        .p-center {
            text-align: center;
            margin-top: 5px;
        }
        .color-turquoise {
            color: #1b534d;
            text-decoration: none;
        }
        .color-turquoise:hover {
            color: #0DB7EA;
        }
        .error-message {
            color: red;
            text-align: center;
            margin: 10px 0 -5px 0;
            margin-bottom: 10px;
            padding-left: 10px;
        }
        .success-message {
            color: red;
            text-align: center;
            margin: 10px 0 -5px 0;
            padding-left: 10px;
            margin-bottom: 10px;
        }
    </style>

</head>
<body>
    <?php include('header.php'); ?>
    <div class="login-container">
        <div class="login-box">
            <h2 class="p-center">Đăng ký tài khoản</h2>
            <?php if (!empty($error_message)) : ?>
                <div class='error-message'><?php echo $error_message; ?></div>
            <?php elseif ($registrationSuccess) : ?>
                <div class='success-message'>Đăng ký thành công!</div>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group-left">
                    <div class="form-group">
                        <input type="text" name="fullname" required class="form-input" title="Nhập họ tên" placeholder="Nhập họ tên *">
                    </div> 
                    <div class="form-group">
                        <input type="email" name="email" required class="form-input" title="Nhập email" placeholder="Nhập email *">
                    </div>
                    <div class="form-group password-toggle">
                        <input type="password" id="password" name="password" required class="form-input" 
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                        title="Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một số."
                        placeholder="Nhập mật khẩu *">
                        <span class="toggle-btn" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
                    </div>
                    <div class="form-group password-toggle">
                        <input type="password" id="confirm_password" name="confirm_password" required class="form-input" placeholder="Xác nhận lại mật khẩu *"
                        title="Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một số.">
                    </div>
                    <div class="form-group flex">
                        <p>Chọn ảnh đại diện:</p>
                        <input type="file" name="img" accept="image/*" class="form-input" title="Chọn ảnh đại diện">
                    </div>
                </div>
                <div class="form-group-right">
                    <div class="form-group">
                        <select name="gioitinh" class="form-input" title="Chọn giới tính">
                            <option value="nam">Nam</option>
                            <option value="nu">Nữ</option>
                            <option value="khac">Giới tính khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="date" name="ngaysinh" class="form-input" title="Chọn ngày, tháng, năm sinh" max="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <input type="tel" name="sdt" class="form-input" title="Nhập số điện thoại"  pattern="\d{10,}" 
       title="Số điện thoại phải là ít nhất 11 chữ số và chỉ chứa số" 
       placeholder="Nhập số điện thoại" oninput="validatePhoneNumber(this)">
                    </div>
                    <div class="form-group">
                        <input type="text" name="diachi" class="form-input" title="Nhập địa chỉ" placeholder="Nhập địa chỉ">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="register-button">Tạo tài khoản</button>
                    </div>
                    <div class="form-group">
                        <p class="p-center">Bạn đã có tài khoản? <a class="color-turquoise" href="dangnhap.php">Đăng nhập ngay</a></p>
                    </div>
                    <div class="form-group forgot-password">
                        <p class="p-center">Bạn quên mật khẩu? <a class="color-turquoise" href="quenmatkhau.php">Quên mật khẩu</a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- <?php include('footer.php'); ?> -->
    
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var confirmPasswordInput = document.getElementById("confirm_password");
            var toggleBtns = document.querySelectorAll(".toggle-btn");

            var isPasswordVisible = passwordInput.type === "text";
            passwordInput.type = isPasswordVisible ? "password" : "text";
            confirmPasswordInput.type = isPasswordVisible ? "password" : "text";

            toggleBtns.forEach(function(btn) {
                btn.innerHTML = isPasswordVisible ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
        }

        function validatePhoneNumber(input) {
            // Loại bỏ các ký tự không phải số
            input.value = input.value.replace(/[^\d]/g, '');

            // Đảm bảo độ dài không vượt quá 11 chữ số
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }
    </script>
</body>
</html>
