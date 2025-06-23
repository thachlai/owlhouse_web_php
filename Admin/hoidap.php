<?php
// Phần đầu của trang (bao gồm kết nối cơ sở dữ liệu, xác thực người dùng, v.v.)
session_start();
include('conn.php');
include('function.php');
check_login();
check_admin();
include('header.php');

// Xử lý cập nhật trạng thái
if (isset($_POST['update_status'])) {
    foreach ($_POST['status'] as $id_hoidap => $status) {
        $status = intval($status);
        $sql_update = "UPDATE hoidap SET trangthai='$status' WHERE id_hoidap='$id_hoidap'";
        $conn->query($sql_update);
    }
    echo "<div class='success-message'>Cập nhật trạng thái thành công.</div>";
}

// Lấy id_sinhvat từ tham số URL
if (isset($_GET['id_sinhvat'])) {
    $id_sinhvat = intval($_GET['id_sinhvat']);
} else {
    echo "<div class='error-message'>ID sinh vật không hợp lệ.</div>";
    exit;
}

// Lấy thông tin sinh vật
$sql_sinhvat = "SELECT * FROM sinhvat WHERE id_sinhvat='$id_sinhvat'";
$result_sinhvat = $conn->query($sql_sinhvat);
if ($result_sinhvat->num_rows > 0) {
    $sinhvat = $result_sinhvat->fetch_assoc();
    echo "<h1>Hỏi đáp về Sinh Vật: " . htmlspecialchars($sinhvat['tensinhvat']) . "</h1>";
} else {
    echo "<div class='error-message'>Không tìm thấy sinh vật.</div>";
    exit;
}

// Lấy các câu hỏi và câu trả lời
$sql_hoidap = "SELECT * FROM hoidap WHERE id_sinhvat='$id_sinhvat' ORDER BY thoigiantao DESC";
$result_hoidap = $conn->query($sql_hoidap);

echo "<form method='POST' action='hoidap.php?id_sinhvat=" . $id_sinhvat . "'>";
echo "<div class='table-danhmuc'>";
echo "<table>
    <tr>
        <th>STT</th>
        <th>Câu Hỏi</th>
        <th>Trạng Thái</th>
        <th>Ngày Tạo</th>
        <th>Tùy biến</th>
    </tr>";

$count = 1; // Biến đếm số thứ tự

while ($row = $result_hoidap->fetch_assoc()) {
    // Lấy thông tin người dùng hỏi
    $id_nguoidung = $row['id_nguoidung'];
    $sql_user = "SELECT fullname FROM nguoidung WHERE id_nguoidung='$id_nguoidung'";
    $result_user = $conn->query($sql_user);
    $user_name = ($result_user->num_rows > 0) ? $result_user->fetch_assoc()["fullname"] : 'Người dùng';

    echo "<tr>";
    echo "<td>" . $count . "</td>";
    echo "<td>" . nl2br(($row["hoidap"])) . "</td>";

    // Tùy chọn trạng thái
    $status_options = "<select name='status[" . $row["id_hoidap"] . "]'>";
    $status_options .= "<option value='0'" . ($row["trangthai"] == 0 ? " selected" : "") . ">Mở</option>";
    $status_options .= "<option value='1'" . ($row["trangthai"] == 1 ? " selected" : "") . ">Khóa</option>";
    $status_options .= "</select>";

    echo "<td>" . $status_options . "</td>";
    echo "<td>" . htmlspecialchars($row["thoigiantao"]) . "</td>";
    echo "<td>
            <a href='javascript:void(0)' onclick='deleteHoidap(" . $row["id_hoidap"] . ", " . $id_sinhvat . ")'><i class='fas fa-trash-alt'></i></a>
          </td>";
    echo "</tr>";
    $count++;
}

echo "</table>";
echo "<button type='submit' name='update_status'>Cập nhật trạng thái</button>";
echo "</div>";
echo "</form>";

$conn->close();
?>
<script>
    function deleteHoidap(id, sinhvatid) {
        if (confirm("Bạn có muốn xóa câu hỏi này không?")) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'xoahoidap.php?type=hoidap&id=' + id + '&id_sinhvat=' + sinhvatid, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert(response.message);
                        location.reload(); // Tải lại trang để cập nhật danh sách
                    } else {
                        alert(response.message);
                    }
                } else {
                    alert('Có lỗi xảy ra.');
                }
            };
            xhr.send();
        }
    }
</script>
<title>Danh sách hỏi đáp</title>