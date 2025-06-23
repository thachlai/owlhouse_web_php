<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_nhanvien();
include('header.php');
?>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tên Lớp:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Lớp">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
<?php

$sql = "SELECT * FROM lop";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Lớp chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM lop WHERE tenlop LIKE '%$search%'";
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả Lớp
    $sql = "SELECT * FROM lop";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Lớp</th>
        <th>Ngành</th>
        <th>Mô Tả</th>
        <th>Hình Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . $row["tenlop"] . "</td>";
        
        // Lấy tên Ngành
        $id_nganh = $row["id_nganh"];
        $sql_nganh = "SELECT tennganh FROM nganh WHERE id_nganh='$id_nganh'";
        $result_nganh = $conn->query($sql_nganh);
        $nganh_name = ($result_nganh->num_rows > 0) ? $result_nganh->fetch_assoc()["tennganh"] : '';

        echo "<td>" . $nganh_name . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . $row["img"] . "' alt='Hình ảnh Lớp' style='width: 80px; height: 80px;'></td>";
        echo "<td>
                <a href='sualop.php?id_lop=" . $row["id_lop"] . "'><i class='fas fa-edit'></i></a> | 
              
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
    function showConfirmation(lopid) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Giới này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoa.php?type=lop&id=' + lopid;
        }
    }
</script>
