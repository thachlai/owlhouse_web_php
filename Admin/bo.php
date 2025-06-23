<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Danh sách cấp bộ</title>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tên Bộ:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Bộ">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
<?php

$sql = "SELECT * FROM bo";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Bộ chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM bo WHERE tenbo LIKE '%$search%'";
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả Bộ
    $sql = "SELECT * FROM bo";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Bộ</th>
        <th>Lớp</th>
        <th>Mô Tả</th>
        <th>Hình Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . $row["tenbo"] . "</td>";
        
        // Lấy tên Lớp
        $id_lop = $row["id_lop"];
        $sql_lop = "SELECT tenlop FROM lop WHERE id_lop='$id_lop'";
        $result_lop = $conn->query($sql_lop);
        $lop_name = ($result_lop->num_rows > 0) ? $result_lop->fetch_assoc()["tenlop"] : '';

        echo "<td>" . $lop_name . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . $row["img"] . "' alt='Hình ảnh Bộ' style='width: 80px; height: 80px;'></td>";
        echo "<td>
                <a href='suabo.php?id_bo=" . $row["id_bo"] . "'><i class='fas fa-edit'></i></a> | 
                <a style='color: red' href='javascript:void(0)' onclick='showConfirmation(" . $row["id_bo"] . ")'><i class='fas fa-trash-alt'></i></a>
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
    function showConfirmation(boid) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Giới này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoa.php?type=bo&id=' + boid;
        }
    }
</script>

