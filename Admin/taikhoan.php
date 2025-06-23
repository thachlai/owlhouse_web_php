<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin(); // Kiểm tra quyền admin
include('header.php');
?>
<title>Danh sách tài khoản</title>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tên người dùng:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên người dùng">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
<?php

$sql = "SELECT * FROM nguoidung";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên người dùng chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM nguoidung WHERE fullname LIKE '%$search%'";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Họ và Tên</th>
        <th>Email</th>
        <th>Giới Tính</th>
        <th>Địa Chỉ</th>
        <th>Ngày Sinh</th>
        <th>Số Điện Thoại</th>
        <th>Trạng Thái</th>
        <th>Quyền</th>
        <th>Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["gioitinh"]) . "</td>";
        echo "<td><textarea class='mota-textarea' readonly>" . htmlspecialchars($row["diachi"]) . "</textarea></td>";
        echo "<td>" . htmlspecialchars($row["ngaysinh"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["sdt"]) . "</td>";
        echo "<td>" . ($row["trangthai"] == 0 ? "Mở" : "Khóa") . "</td>";
        echo "<td>" . ($row["quyen"] == 0 ? "Người dùng" : ($row["quyen"] == 1 ? "Admin" : "Nhà sinh vật học")) . "</td>";
        echo "<td><img src='../uploads/" . htmlspecialchars($row["img"]) . "' alt='Ảnh Người Dùng' style='width: 80px; height: 80px;'></td>";
        echo "<td>
                <a href='suatk.php?id_nguoidung=" . $row["id_nguoidung"] . "'><i class='fas fa-edit'></i></a> | 
                <a style='color: red' href='javascript:void(0)' onclick='showConfirmation(" . $row["id_nguoidung"] . ")'><i class='fas fa-trash-alt'></i></a>
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
    function showConfirmation(id) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa tài khoản này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoatk.php?type=nguoidung&id=' + id;
        }
    }
</script>
