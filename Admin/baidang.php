<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Danh sách bài đăng</title>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tiêu đề bài đăng:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tiêu đề bài đăng">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>

<?php

$sql = "SELECT * FROM baidang";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tiêu đề bài đăng chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM baidang WHERE tieude LIKE '%$search%'";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tiêu Đề</th>
        <th>Mô Tả</th>
        <th>Ảnh Chính</th>
        <th>Ngày Tạo</th>
        <th>Người Đăng</th>
        <th>Trạng Thái</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . htmlspecialchars($row["tieude"]) . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        
        // Lấy danh sách ảnh chính từ bảng anh_baidang
        $id_baidang = $row["id_baidang"];
        $sql_anh = "SELECT anh FROM anh_baidang WHERE id_baidang='$id_baidang'";
        $result_anh = $conn->query($sql_anh);
        
        // Hiển thị ảnh chính với khu vực cuộn
        echo "<td class='anh-chinh'>";
        if ($result_anh->num_rows > 0) {
            echo "<div class='anh-chinh-container'>";
            // Hiển thị tất cả ảnh chính liên quan đến bài đăng
            while ($row_anh = $result_anh->fetch_assoc()) {
                // Kiểm tra xem khóa "anh" có tồn tại không
                $img_src = isset($row_anh["anh"]) ? htmlspecialchars($row_anh["anh"]) : 'default-image.jpg';
                echo "<img src='../uploads/" . $img_src . "' alt='Ảnh Chính' class='anh-chinh-img'>";
            }
            echo "</div>";
        } else {
            echo "Không có ảnh chính";
        }
        echo "</td>";

        echo "<td>" . htmlspecialchars($row["thoigiantao"]) . "</td>";
        
        // Lấy tên người dùng từ bảng nguoidung
        $id_nguoidung = $row["id_nguoidung"];
        $sql_user = "SELECT fullname FROM nguoidung WHERE id_nguoidung='$id_nguoidung'";
        $result_user = $conn->query($sql_user);
        $user_name = ($result_user->num_rows > 0) ? $result_user->fetch_assoc()["fullname"] : '';

        echo "<td>" . htmlspecialchars($user_name) . "</td>";

        // Hiển thị trạng thái bài đăng
        $status = $row["trangthai"];
        $status_text = ($status == 0) ? 'Mở' : 'Khóa';
        echo "<td>" . htmlspecialchars($status_text) . "</td>";

        echo "<td>
                <a href='chitietbaidang.php?id=" . $row["id_baidang"] . "'><i class='fas fa-edit'></i></a> | 
    <a style='color: red' href='javascript:void(0)' onclick='showConfirmation(" . $row["id_baidang"] . ")'><i class='fas fa-trash-alt'></i></a>
            </td>";
        echo "</tr>";
        $count++;
    }

    echo "</table>";
} else {
    echo "<div class='success-message'>Không có dữ liệu</div>";
}

$conn->close();
?>
<script>
    function showConfirmation(baidangid) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa bài đăng này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoabaidang.php?type=baidang&id=' + baidangid;
        }
    }
</script>
