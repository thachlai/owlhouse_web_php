<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Danh sách Cấp Ngành</title>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tên Ngành:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Ngành">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
<?php

$sql = "SELECT * FROM nganh";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Ngành chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM nganh WHERE tennganh LIKE '%$search%'";
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả Ngành
    $sql = "SELECT * FROM nganh";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Ngành</th>
        <th>Giới</th>
        <th>Mô Tả</th>
        <th>Hình Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . $row["tennganh"] . "</td>";
        
        // Lấy tên Giới
        $id_gioi = $row["id_gioi"];
        $sql_gioi = "SELECT tengioi FROM gioi WHERE id_gioi='$id_gioi'";
        $result_gioi = $conn->query($sql_gioi);
        $gioi_name = ($result_gioi->num_rows > 0) ? $result_gioi->fetch_assoc()["tengioi"] : '';

        echo "<td>" . $gioi_name . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . $row["img"] . "' alt='Hình ảnh Ngành' style='width: 80px; height: 80px;'></td>";
        // echo "<td><a href='suanganh.php?id_nganh=" . $row["id_nganh"] . "'><i class='fas fa-edit'></i></a> | <a style='color: red' href='xoanganh.php?id_nganh=" . $row["id_nganh"] . "'><i class='fas fa-trash-alt'></i></a></td>";
        echo "<td>
                <a href='suanganh.php?id_nganh=" . $row["id_nganh"] . "'><i class='fas fa-edit'></i></a> | 
                <a style='color: red' href='javascript:void(0)' onclick='showConfirmation(" . $row["id_nganh"] . ")'><i class='fas fa-trash-alt'></i></a>
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
    function showConfirmation(nganhid) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Giới này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoa.php?type=nganh&id=' + nganhid;
        }
    }
</script>
