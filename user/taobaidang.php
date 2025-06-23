<?php
session_start();
include('conn.php');
include('function.php');
check_login();
// check_admin();
include('header.php');

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title']; // Lấy tiêu đề từ form
    $content = $_POST['content'];
    $user_id = $_SESSION['id_nguoidung']; // Lấy ID người dùng từ session

    // Định nghĩa thư mục upload
    $img_upload_dir = '../uploads/';

    // Thêm dữ liệu vào cơ sở dữ liệu
    $query = "INSERT INTO baidang (id_nguoidung, tieude, mota) VALUES ('$user_id', '$title', '$content')";
    mysqli_query($conn, $query);
    $last_id = mysqli_insert_id($conn); // Lấy ID của bài đăng vừa thêm

    // Xử lý upload ảnh phụ
    if (isset($_FILES['additional_imgs']) && !empty($_FILES['additional_imgs']['name'][0])) {
        $imgs = $_FILES['additional_imgs'];

        foreach ($imgs['name'] as $key => $name) {
            if ($imgs['error'][$key] == UPLOAD_ERR_OK) {
                $img_tmp_name = $imgs['tmp_name'][$key];
                $img_path = $img_upload_dir . basename($name);

                if (move_uploaded_file($img_tmp_name, $img_path)) {
                    // Thêm dữ liệu vào bảng ảnh phụ
                    $query = "INSERT INTO anh_baidang (id_baidang, anh) VALUES ('$last_id', '$img_path')";
                    mysqli_query($conn, $query);
                } else {
                    echo "Lỗi khi upload ảnh phụ.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Bài Đăng</title>
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css"/>
    <script>
        function previewImages(input) {
            var preview = input.nextElementSibling; // Phần tử xem trước của ảnh phụ
            preview.innerHTML = ""; // Xóa các ảnh đã xem trước trước đó

            if (input.files) {
                var files = input.files;
                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100px'; // Kích thước của ảnh xem trước
                        img.style.marginRight = '10px'; // Khoảng cách giữa các ảnh
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(files[i]);
                }
            }
        }

        function addImageField() {
            // Tạo một phần tử mới cho ảnh phụ
            var container = document.getElementById('additional_image_fields');
            var newField = document.createElement('div');
            newField.className = 'image_field';
            newField.innerHTML = `
                <input type="file" name="additional_imgs[]" accept="image/*" onchange="previewImages(this)" />
                <div class="image_preview"></div>
                <br>
                <button type="button" onclick="removeImageField(this)">Xóa</button>
            `;
            // <input type="file" name="imgs[]" accept="image/*" onchange="previewImages(this)" />
            

            // <div class="image_preview"></div>
            // <br>
            // <button type="button" onclick="removeImageField(this)">Xóa</button>
           

            container.appendChild(newField);
        }

        function removeImageField(button) {
            var container = document.getElementById('additional_image_fields');
            container.removeChild(button.parentNode);
        }
    </script>
    <style>
        .content {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form label {
            display: block;
            margin-bottom: 8px;
            margin-top: 8px;
            font-weight: bold;
        }
        form textarea, form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #45a049;
        }
        form input[type="file"] {
            padding: 8px;
        }
        #additional_image_fields .image_field {
            margin-bottom: 10px;
        }
        #additional_image_fields .image_field .image_preview img {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Thêm Bài Đăng</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="title">Tiêu Đề:</label>
            <input type="text" id="title" name="title" required /><br>

            <label for="content">Nội Dung:</label>
            <textarea id="content" name="content" rows="10"></textarea><br>

            <label>Ảnh Phụ:</label>
            <div id="additional_image_fields">
                <div class="image_field">
                    <input type="file" name="additional_imgs[]" accept="image/*" onchange="previewImages(this)" />
                    
                    <div class="image_preview"></div>
                    <button type="button" onclick="removeImageField(this)">Xóa</button><br>
                </div>
            </div>
            <button type="button" onclick="addImageField()">Cộng Thêm</button><br><br>

            <button type="submit">Thêm Bài Đăng</button>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor.js"></script>
    <script>
        // ClassicEditor
        //     .create(document.querySelector('#content'))
        //     .catch(error => {
        //         console.error(error);
        //     });
            CKEDITOR.replace('content');
    </script>
</body>
</html>
