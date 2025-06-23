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
        <label for="search">Tìm kiếm theo tên Chi:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Chi">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>

<?php
$sql = "SELECT * FROM chi";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Chi chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM chi WHERE tenchi LIKE '%$search%'";
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả Chi
    $sql = "SELECT * FROM chi";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Chi</th>
        <th>Họ</th>
        <th>Mô Tả</th>
        <th>Hình Ảnh</th>
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . $row["tenchi"] . "</td>";

        // Lấy tên Họ
        $id_ho = $row["id_ho"];
        $sql_ho = "SELECT tenho FROM ho WHERE id_ho='$id_ho'";
        $result_ho = $conn->query($sql_ho);
        $ho_name = ($result_ho->num_rows > 0) ? $result_ho->fetch_assoc()["tenho"] : '';

        echo "<td>" . $ho_name . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . $row["img"] . "' alt='Hình ảnh Chi' style='width: 80px; height: 80px;'></td>";
        // echo "<td><a href='suachii.php?id_chi=" . $row["id_chi"] . "'><i class='fas fa-edit'></i></a> | <a style='color: red' href='xoachi.php?id_chi=" . $row["id_chi"] . "'><i class='fas fa-trash-alt'></i></a></td>";
        echo "<td>
                <a href='suachi.php?id_chi=" . $row["id_chi"] . "'><i class='fas fa-edit'></i></a> | 
               
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
    function showConfirmation(chiId) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Chi này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoa.php?type=chi&id=' + chiId;
        }
    }
</script>
