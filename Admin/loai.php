<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Danh sách cấp loại</title>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tên Loài:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Loài">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>

<?php
$sql = "SELECT * FROM loai";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Loài chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM loai WHERE tenloai LIKE '%$search%'";
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả Loài
    $sql = "SELECT * FROM loai";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Loài</th>
        <th>Mô Tả</th>
        <th>Hình Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . $row["tenloai"] . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . $row["img"] . "' alt='Hình ảnh Loài' style='width: 80px; height: 80px;'></td>";
        echo "<td>
                <a href='sualoai.php?id_loai=" . $row["id_loai"] . "'><i class='fas fa-edit'></i></a> | 
                <a style='color: red' href='javascript:void(0)' onclick='showConfirmation(" . $row["id_loai"] . ")'><i class='fas fa-trash-alt'></i></a>
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
    function showConfirmation(loaiId) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Loài này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số id_loai
            window.location.href = 'xoa.php?type=loai&id=' + loaiId;
        }
    }
</script>