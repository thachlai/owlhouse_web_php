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
        <label for="search">Tìm kiếm theo tên Họ:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Họ">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
<?php

$sql = "SELECT * FROM ho";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Họ chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM ho WHERE tenho LIKE '%$search%'";
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả Họ
    $sql = "SELECT * FROM ho";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Họ</th>
        <th>Bộ</th>
        <th>Mô Tả</th>
        <th>Hình Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . $row["tenho"] . "</td>";
        
        // Lấy tên Bộ
        $id_bo = $row["id_bo"];
        $sql_bo = "SELECT tenbo FROM bo WHERE id_bo='$id_bo'";
        $result_bo = $conn->query($sql_bo);
        $bo_name = ($result_bo->num_rows > 0) ? $result_bo->fetch_assoc()["tenbo"] : '';

        echo "<td>" . $bo_name . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . $row["img"] . "' alt='Hình ảnh Họ' style='width: 80px; height: 80px;'></td>";
        echo "<td>
                <a href='suaho.php?id_ho=" . $row["id_ho"] . "'><i class='fas fa-edit'></i></a> | 
               
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
    function showConfirmation(hoId) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Họ này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang suaho.php với tham số id_ho
            window.location.href = 'xoa.php?type=ho&id=' + hoId;
        }
    }
</script>
