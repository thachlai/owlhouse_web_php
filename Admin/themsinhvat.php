<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Hàm lấy các tùy chọn từ cơ sở dữ liệu
function getOptions($table, $id_field, $name_field, $parent_id_field = null, $parent_id = null) {
    global $conn;
    $query = "SELECT $id_field, $name_field FROM $table";
    if ($parent_id_field && $parent_id !== null) {
        $query .= " WHERE $parent_id_field = $parent_id";
    }
    $result = mysqli_query($conn, $query);
    $options = '<option value="">Chọn</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='{$row[$id_field]}'>{$row[$name_field]}</option>";
    }
    return $options;
}

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_loai = $_POST['id_loai'];
    $tensinhvat = $_POST['tensinhvat'];
    $mota = $_POST['mota'];

    // Định nghĩa thư mục upload
    $img_upload_dir = '../uploads/';

    // Xử lý upload file ảnh chính
    $img_path = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $img_tmp_name = $_FILES['img']['tmp_name'];
        $img_name = $_FILES['img']['name'];
        $img_path = $img_upload_dir . basename($img_name);

        // Tạo thư mục upload nếu chưa tồn tại
        if (!file_exists($img_upload_dir)) {
            mkdir($img_upload_dir, 0777, true);
        }

        // Di chuyển file từ thư mục tạm đến thư mục uploads
        if (move_uploaded_file($img_tmp_name, $img_path)) {
            // Thêm dữ liệu vào cơ sở dữ liệu
            $query = "INSERT INTO sinhvat (id_loai, tensinhvat, mota, img) 
                      VALUES ('$id_loai', '$tensinhvat', '$mota', '$img_path')";
            mysqli_query($conn, $query);
            $last_id = mysqli_insert_id($conn); // Lấy ID của sinh vật vừa thêm
        } else {
            echo "Lỗi khi upload ảnh chính.";
        }
    }

        // Xử lý upload file ảnh phụ
        if (isset($_FILES['imgs']) && !empty($_FILES['imgs']['name'][0])) {
            $imgs = $_FILES['imgs'];
    
            foreach ($imgs['name'] as $key => $name) {
                if ($imgs['error'][$key] == UPLOAD_ERR_OK) {
                    $img_tmp_name = $imgs['tmp_name'][$key];
                    $img_path = $img_upload_dir . basename($name);
    
                    if (move_uploaded_file($img_tmp_name, $img_path)) {
                        // Thêm dữ liệu vào bảng ảnh phụ
                        if (isset($last_id)) { // Kiểm tra xem biến $last_id đã được định nghĩa
                            $query = "INSERT INTO anh_sinhvat (id_sinhvat, anh) 
                                      VALUES ('$last_id', '$img_path')";
                            mysqli_query($conn, $query);
                        }
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
    <title>Thêm Sinh Vật</title>
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css"/>
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5-premium-features/43.0.0/ckeditor5-premium-features.css"/>
    <script>
        function updateOptions(level, parent_id) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_options.php?level=' + level + '&parent_id=' + parent_id, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('select_' + level).innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function previewImages(input) {
            var preview = document.getElementById('image_preview');
            preview.innerHTML = ""; // Clear previous previews

            if (input.files) {
                var files = input.files;
                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100px';
                        img.style.marginRight = '10px';
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(files[i]);
                }
            }
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
        form input[type="text"], form select, form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form textarea {
            resize: vertical;
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
        #image_preview img {
            display: inline-block;
        }
        
    </style>
</head>
<body>
    <div class="content">
        <h1>Thêm Sinh Vật</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Dropdown cho Giới -->
            <label for="select_gioi">Giới:</label>
            <select id="select_gioi" name="id_gioi" onchange="updateOptions('nganh', this.value)">
                <?php echo getOptions('gioi', 'id_gioi', 'tengioi'); ?>
            </select><br>

            <!-- Dropdown cho Ngành -->
            <label for="select_nganh">Ngành:</label>
            <select id="select_nganh" name="id_nganh" onchange="updateOptions('lop', this.value)">
                <option value="">Chọn</option>
            </select><br>

            <!-- Dropdown cho Lớp -->
            <label for="select_lop">Lớp:</label>
            <select id="select_lop" name="id_lop" onchange="updateOptions('bo', this.value)">
                <option value="">Chọn</option>
            </select><br>

            <!-- Dropdown cho Bộ -->
            <label for="select_bo">Bộ:</label>
            <select id="select_bo" name="id_bo" onchange="updateOptions('ho', this.value)">
                <option value="">Chọn</option>
            </select><br>

            <!-- Dropdown cho Họ -->
            <label for="select_ho">Họ:</label>
            <select id="select_ho" name="id_ho" onchange="updateOptions('chi', this.value)">
                <option value="">Chọn</option>
            </select><br>

            <!-- Dropdown cho Chi -->
            <label for="select_chi">Chi:</label>
            <select id="select_chi" name="id_chi" onchange="updateOptions('loai', this.value)">
                <option value="">Chọn</option>
            </select><br>

            <!-- Dropdown cho Loài -->
            <label for="select_loai">Loài:</label>
            <select id="select_loai" name="id_loai">
                <option value="">Chọn</option>
            </select><br>

            <label for="tensinhvat">Tên Sinh Vật:</label>
            <input type="text" id="tensinhvat" name="tensinhvat" required><br>

            <label for="mota">Mô Tả:</label>
            <textarea id="mota" name="mota" rows="10"></textarea><br>

            <label for="img">Ảnh Chính:</label>
            <input type="file" id="img" name="img" accept="image/*" onchange="previewMainImage(this)"><br>
            <div id="main_image_preview" style="margin-top: 10px;"></div>

            <label>Ảnh Phụ:</label>
            <div id="image_fields">
                <div class="image_field">
                    <input type="file" name="imgs[]" accept="image/*" onchange="previewImages(this)" />
                    
                <div class="image_preview"></div>
                <button type="button" onclick="removeImageField(this)">Xóa</button><br>
            </div>
</div>
<br>
<button type="button" onclick="addImageField()">Cộng Thêm</button><br><br>

            <button type="submit">Thêm Sinh Vật</button>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('mota');
        ClassicEditor
            .create(document.querySelector('#mota'))
            .catch(error => {
                console.error(error);
            });
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
        var container = document.getElementById('image_fields');
        var newField = document.createElement('div');
        newField.className = 'image_field';
        newField.innerHTML = `
            <input type="file" name="imgs[]" accept="image/*" onchange="previewImages(this)" />
            

            <div class="image_preview"></div>
            <br>
            <button type="button" onclick="removeImageField(this)">Xóa</button>
           
        `;
        container.appendChild(newField);
    }

    function removeImageField(button) {
        var container = document.getElementById('image_fields');
        container.removeChild(button.parentNode);
    }

    function previewMainImage(input) {
        var preview = document.getElementById('main_image_preview');
        preview.innerHTML = ""; // Xóa các ảnh đã xem trước trước đó

        if (input.files && input.files[0]) {
            var file = input.files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '300px'; // Kích thước của ảnh chính
                img.style.marginRight = '10px'; // Khoảng cách giữa các ảnh chính
                preview.appendChild(img);
            }

            reader.readAsDataURL(file);
        }
    }
    </script>
</body>
</html>
