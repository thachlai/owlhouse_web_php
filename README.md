# owlhouse_web_php
# 🦉 OwlHouse – Species Dictionary Website
Đây là dự án do tui phát triển nhầm làm báo cáo đồ án tại đại học nhưng vẫn phục vụ đam mê khoa học của bản thân mình, do đây là dự án cá nhân nên nó còn khá sơ sài và thiếu rất nhiều chức năng
Trang web từ điển sinh vật học với 7 cấp phân loại: Giới, Ngành, Lớp, Bộ, Họ, Chi, Loài. Giải thích chi tiết hơn thì đây 7 cấp phân loại sinh học  (taxonomy) tập trung chủ yếu vào việc tìm kiếm các loài (Species)
Trang web này sẽ giúp bạn tìm kiếm cũng như tra cứu thông tin của các sinh vật mà bạn đã bắt gặp nhưng không xác định được tên và phân loài của chúng, cũng như hỏi đáp và tra cứu,..
Phần dữ liệu sinh vật sẽ được các người dùng có phân quyền là nhà sinh vật học cung cấp và đăng cũng như bỗ sung

## 📌 Tính năng
- Tìm kiếm phần loài sinh vật(người dùng)
- Đăng bài và Đăng ảnh, hỏi đáp , bình luận (người dùng)
- Quản lý dữ liệu qua giao diện web, quản lý dữ liệu của sinh vật (admin, nhà sinh vật học)

## 💻 Công nghệ sử dụng
- PHP + MySQL
- HTML/CSS + JavaScript
- PHPmailer
- CKediter

### Cấu trúc thư mưc source 
owlhouse_web/
├── admin/                 # Trang quản trị (dành cho admin và nhà sinh vật học)
│   ├── admin.php
│   └── gioi.php
│   └──  ....... Các trang PHP xử lý của khu vực Admin
├── nguoidung/             # Trang dành cho người dùng thường
│   ├── trangchu.php
│   ├── hoso.php
│   └──  ....... Các trang PHP xử lý của khu vực User
├── nhanvien/             # Trang dành cho người dùng có phân quyền nhà sinh vật học
│   ├── index.php
│   ├── chitiet.php
│   └── hoidap.php
├── ckeditor
├── PHPMailer-master
├── uploads                #chứa các ảnh mà người dùng up lên
├──
├── README.md              # Mô tả dự án (file này)
└── LICENSE                # Giấy phép (MIT, GPL, v.v.)


