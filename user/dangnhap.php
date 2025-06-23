<?php
session_start();
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? md5($_POST['password']) : '';

    $sql = "SELECT * FROM nguoidung WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['trangthai'] == 1) {
            $error = "Tài khoản của bạn đã bị khóa.";
        } else {
            // Lưu thông tin người dùng vào session
            $_SESSION['id_nguoidung'] = $user['id_nguoidung'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['quyen'] = $user['quyen'];
            $_SESSION['user_img'] = $user['img']; // Thêm dòng này để lưu ảnh người dùng vào session
            echo "Đăng nhập thành công!";
            // Chuyển hướng người dùng đến trang chủ hoặc trang khác
            header("Location: trangchu.php");
            exit();
        }
    } else {
        $error = "Email hoặc mật khẩu không đúng.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <style>
        body {
            background: url(../img/natural2.webp);
            background-size: 25% 25%;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
        }
        .login-box {
            width: 350px;
            margin: 235px auto 100px auto;
            background-color: #ffffffb8;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
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
        input#email {
            margin-top: 20px;
        }
        .forgot-password {
            margin-top: 10px;
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
        }
        .color-turquoise {
            color: #1b534d;
        }
        .color-turquoise:hover {
            color: #0DB7EA;
        }
        .error-message {
            color: red;
            margin: 10px 0 -5px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
    include 'header.php';
    ?>
    <div class="login-container">
        <div class="login-box">
            <h2 class="p-center">Đăng nhập</h2>
                
            <?php if (isset($error)) : ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
                
            <form action="" method="POST">
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Nhập tài khoản" required>
                </div>
                
                <div class="form-group password-toggle">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <span class="toggle-btn" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="register-button">Đăng nhập</button>
                </div>
                <div class="form-group forgot-password">
                    <p class="p-center">Bạn quên mật khẩu? <a class="color-turquoise" href="quenmatkhau.php">Quên mật khẩu</a></p>
                </div>
                <div class="form-group register-link">
                    <p class="p-center">Bạn chưa có tài khoản? <a class="color-turquoise" href="dangky.php">Đăng ký ngay</a></p>
                </div>
            </form>
        </div>
    </div>
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var toggleBtn = document.querySelector('.toggle-btn i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.classList.remove('fa-eye');
                toggleBtn.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleBtn.classList.remove('fa-eye-slash');
                toggleBtn.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>