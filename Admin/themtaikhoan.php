<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin(); // Kiểm tra quyền admin
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $gioitinh = $_POST['gioitinh'];
    $diachi = $_POST['diachi'];
    $ngaysinh = $_POST['ngaysinh'];
    $sdt = $_POST['sdt'];
    $trangthai = $_POST['trangthai'];
    $quyen = $_POST['quyen'];

    // Kiểm tra email có định dạng hợp lệ và thuộc miền cho phép
    $valid_domains = ['gmail.com', 'st.vlute.edu.vn', 'yahoo.com']; // Thay đổi danh sách miền nếu cần
    $email_parts = explode('@', $email);
    $domain = array_pop($email_parts);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($domain, $valid_domains)) {
        echo "<div class='error-message'>Email phải có định dạng hợp lệ và thuộc miền cho phép, ví dụ: example@gmail.com hoặc example@st.vlute.edu.vn</div>";
    } else {
        // Kiểm tra email đã tồn tại trong cơ sở dữ liệu chưa
        $stmt = $conn->prepare("SELECT email FROM nguoidung WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<div class='error-message'>Email đã tồn tại trong cơ sở dữ liệu.</div>";
        } else {
            // Xử lý ảnh
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $img = $_FILES['img'];
                $img_name = $img['name'];
                $img_tmp = $img['tmp_name'];
                $img_size = $img['size'];
                
                // Kiểm tra kích thước ảnh và điều chỉnh kích thước nếu cần
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
            } else {
                $img_name = null; // Không có ảnh
            }

            // Thực hiện thêm tài khoản vào cơ sở dữ liệu
            $sql = "INSERT INTO nguoidung (fullname, email, password, gioitinh, diachi, ngaysinh, sdt, trangthai, quyen, img) 
                    VALUES ('$fullname', '$email', '$password','$gioitinh', '$diachi', '$ngaysinh', '$sdt', '$trangthai', '$quyen', '$img_name')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='success-message'>Tài khoản đã được thêm thành công!</div>";
            } else {
                echo "<div class='error-message'>Lỗi: " . $conn->error . "</div>";
            }
        }
    }
}
?>
<title>Thêm Tài khoản</title>
<div class="content">
    <h1>Thêm Tài Khoản</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Các trường nhập liệu -->
        <label for="fullname">Họ và Tên:</label>
        <input type="text" id="fullname" name="fullname" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required placeholder="example@domain.com">
        
        <label for="password">Mật Khẩu:</label>
        <input type="text" id="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
        title="Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một số.">
        

        <label for="gioitinh">Giới Tính:</label>
        <select id="gioitinh" name="gioitinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
        </select>
        
        <label for="diachi">Địa Chỉ:</label>
        <textarea id="diachi" name="diachi"></textarea>
        
        <label for="ngaysinh">Ngày Sinh:</label>
        <input type="date" id="ngaysinh" name="ngaysinh">
        
        <label for="sdt">Số Điện Thoại:</label>
        <input type="text" id="sdt" name="sdt" pattern="\d{10,}" 
       title="Số điện thoại phải là ít nhất 11 chữ số và chỉ chứa số" 
       placeholder="Nhập số điện thoại">
        
        <label for="trangthai">Trạng Thái:</label>
        <select id="trangthai" name="trangthai">
            <option value="0">Mở</option>
            <option value="1">Khóa</option>
        </select>
        
        <label for="quyen">Quyền:</label>
        <select id="quyen" name="quyen">
            <option value="0">Người dùng</option>
            <option value="1">Admin</option>
            <option value="2">Nhà sinh vật học</option>
        </select>
        
        <label for="img">Ảnh:</label>
        <input type="file" id="img" name="img" accept="image/*" onchange="previewImage()">
        <img id="preview" src="" alt="Ảnh Xem Trước" style="display: none; width: 150px; height: 150px;">
        <br>
        <button type="submit">Thêm Tài Khoản</button>
    </form>
</div>

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
