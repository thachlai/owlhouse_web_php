<?php
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');
?>
<title>Danh sách sinh vật</title>
<!-- Thêm một biểu mẫu đơn giản để nhập từ khóa tìm kiếm -->
<div class="timkiem">
    <form action="" method="GET">
        <label for="search">Tìm kiếm theo tên Sinh Vật:</label>
        <input type="text" id="search" name="search" placeholder="Nhập tên Sinh Vật">
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
<?php

$sql = "SELECT * FROM sinhvat";

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu tìm kiếm
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Sử dụng LIKE để tìm kiếm tên Sinh Vật chứa từ khóa tìm kiếm
    $sql = "SELECT * FROM sinhvat WHERE tensinhvat LIKE '%$search%'";
}

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-danhmuc'>
    <table>
    <tr>
        <th>STT</th>
        <th>Tên Sinh Vật</th>
        <th>Loài</th>
        <th>Mô Tả</th>
        <th>Ảnh Chính</th>
        <th>Ảnh Phụ</th>
        <th>Hỏi đáp</th> <!-- Thêm cột Hỏi đáp -->
        <th style='text-align: center;'>Tùy biến</th>
    </tr>";

    $count = 1; // Biến đếm số thứ tự

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $count . "</td>";
        echo "<td>" . htmlspecialchars($row["tensinhvat"]) . "</td>";
        
        // Lấy tên Loài
        $id_loai = $row["id_loai"];
        $sql_loai = "SELECT tenloai FROM loai WHERE id_loai='$id_loai'";
        $result_loai = $conn->query($sql_loai);
        $loai_name = ($result_loai->num_rows > 0) ? $result_loai->fetch_assoc()["tenloai"] : '';

        echo "<td>" . htmlspecialchars($loai_name) . "</td>";
        echo "<td>
        <div class='mota-textarea' readonly>" . $row["mota"] . "</div>
      </td>";
        echo "<td><img src='../uploads/" . htmlspecialchars($row["img"]) . "' alt='Ảnh Sinh Vật' style='width: 80px; height: 80px;'></td>";
        
        // Lấy danh sách ảnh phụ từ bảng anh_sinhvat
        $id_sinhvat = $row["id_sinhvat"];
        $sql_anh_phu = "SELECT anh FROM anh_sinhvat WHERE id_sinhvat='$id_sinhvat'";
        $result_anh_phu = $conn->query($sql_anh_phu);
        
        // Hiển thị ảnh phụ với khu vực cuộn
        echo "<td class='anh-phu'>";
        if ($result_anh_phu->num_rows > 0) {
            echo "<div class='anh-phu-container'>";
            while ($row_anh_phu = $result_anh_phu->fetch_assoc()) {
                $img_src = isset($row_anh_phu["anh"]) ? htmlspecialchars($row_anh_phu["anh"]) : 'default-image.jpg';
                echo "<img src='../uploads/" . $img_src . "' alt='Ảnh Phụ' class='anh-phu-img'>";
            }
            echo "</div>";
        } else {
            echo "Không có ảnh phụ";
        }
        echo "</td>";

        // Thêm cột Hỏi đáp
        echo "<td>
                <a href='hoidap.php?id_sinhvat=" . $row["id_sinhvat"] . "'>Xem hỏi đáp</a>
            </td>";

        echo "<td>
                <a href='suasinhvat.php?id_sinhvat=" . $row["id_sinhvat"] . "'><i class='fas fa-edit'></i></a> | 
                <a style='color: red' href='javascript:void(0)' onclick='showConfirmation(" . $row["id_sinhvat"] . ")'><i class='fas fa-trash-alt'></i></a>
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
    function showConfirmation(sinhvatid) {
        // Hiển thị hộp thoại xác nhận
        var isConfirmed = confirm("Bạn có muốn xóa Sinh Vật này không?");
        if (isConfirmed) {
            // Nếu xác nhận, chuyển hướng đến trang xoa.php với tham số type và id
            window.location.href = 'xoa.php?type=sinhvat&id=' + sinhvatid;
        }
    }
</script>
