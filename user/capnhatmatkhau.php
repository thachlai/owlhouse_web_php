<?php
session_start();
include('conn.php');
include('function.php');
check_login();
include('header.php');

// Lấy ID người dùng từ session
$id_nguoidung = $_SESSION['id_nguoidung'];

// Xử lý cập nhật mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra mật khẩu hiện tại
    $query = "SELECT password FROM nguoidung WHERE id_nguoidung = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_nguoidung);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (md5($current_password) !== $user['password']) {
        echo '<script>alert("Mật khẩu hiện tại không đúng.");</script>';
    } elseif ($new_password !== $confirm_password) {
        echo '<script>alert("Mật khẩu mới và xác nhận mật khẩu không khớp.");</script>';
    } elseif (strlen($new_password) < 6) {
        echo '<script>alert("Mật khẩu mới phải có ít nhất 6 ký tự.");</script>';
    } else {
        $hashed_new_password = md5($new_password);
        $query = "UPDATE nguoidung SET password = ? WHERE id_nguoidung = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $hashed_new_password, $id_nguoidung);
        $stmt->execute();
        echo '<script>
                alert("Cập nhật mật khẩu thành công!");
                window.location.href = "hoso.php";
              </script>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Mật Khẩu</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .password-update-form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .password-update-form h1 {
            text-align: center;
            color: #333;
        }
        .password-update-form label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }
        .password-update-form .input-container {
            position: relative;
            margin-bottom: 15px;
        }
        .password-update-form input {
            width: calc(100% - 30px); /* Tạo không gian cho biểu tượng mắt */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .password-update-form .show-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #007bff;
        }
        .password-update-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .password-update-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="password-update-form">
        <h1>Cập Nhật Mật Khẩu</h1>
        <form action="" method="post">
            <div class="input-container">
                <label for="current_password">Mật Khẩu Hiện Tại</label>
                <input type="password" id="current_password" name="current_password" required>
                <span class="show-password" onclick="togglePasswordVisibility('current_password')">👁️</span>
            </div>

            <div class="input-container">
                <label for="new_password">Mật Khẩu Mới</label>
                <input type="password" id="new_password" name="new_password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                        title="Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một số."
                        placeholder="Nhập mật khẩu *">
                <span class="show-password" onclick="togglePasswordVisibility('new_password')">👁️</span>
            </div>

            <div class="input-container">
                <label for="confirm_password">Xác Nhận Mật Khẩu Mới</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="show-password" onclick="togglePasswordVisibility('confirm_password')">👁️</span>
            </div>

            <button type="submit">Cập Nhật</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(id) {
            var input = document.getElementById(id);
            var type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
        }
    </script>
</body>
</html>
