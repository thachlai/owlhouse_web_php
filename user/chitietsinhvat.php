<?php
session_start();
include('conn.php');
include('function.php');
include 'header.php';
// check_login();

// Lấy ID sinh vật từ URL
$id_sinhvat = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin sinh vật
$query = "SELECT s.*, l.tenloai 
          FROM sinhvat s 
          JOIN loai l ON s.id_loai = l.id_loai 
          WHERE s.id_sinhvat = '$id_sinhvat'";
$result = mysqli_query($conn, $query);
$sinhvat = mysqli_fetch_assoc($result);

// Lấy các ảnh của sinh vật
$query_imgs = "SELECT * FROM anh_sinhvat WHERE id_sinhvat = '$id_sinhvat'";
$imgs_result = mysqli_query($conn, $query_imgs);

// Xử lý hỏi đáp
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['question'])) {
    check_login();
    if (isset($_SESSION['id_nguoidung'])) {
        $question_content = $_POST['question'];
        $user_id = $_SESSION['id_nguoidung'];

        // Làm sạch đầu vào
        $question_content = mysqli_real_escape_string($conn, $question_content);

        // Thêm hỏi đáp vào cơ sở dữ liệu
        $query_question = "INSERT INTO hoidap (id_nguoidung, id_sinhvat, hoidap) VALUES ('$user_id', '$id_sinhvat', '$question_content')";
        mysqli_query($conn, $query_question);
    } else {
        echo "<script>alert('Bạn cần đăng nhập để đặt câu hỏi.');</script>";
    }
}

// Lấy các hỏi đáp với trangthai = 0
$query_questions = "SELECT h.*, u.fullname, u.email, u.img as avatar 
                    FROM hoidap h 
                    JOIN nguoidung u ON h.id_nguoidung = u.id_nguoidung 
                    WHERE h.id_sinhvat = '$id_sinhvat' AND h.trangthai = 0 
                    ORDER BY h.thoigiantao DESC";
$questions_result = mysqli_query($conn, $query_questions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sinh Vật</title>
    <style>
        body {
            background-color: #f4f4f4;
        }
        .post-content, .questions-section {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .post-title {
            margin: 10px 0;
        }
        .post-content img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
            display: block;
        }
        .main-img {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
        }
        .question .avatar {
            margin-right: 15px;
        }
        .question .avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .question .question-details {
            flex: 1;
        }
        .question-author {
            font-weight: bold;
        }
        .question-date {
            color: #777;
            font-size: 0.9em;
        }
        .question-content {
            margin-top: 10px;
        }
        .question-form {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .question-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .question-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .question-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="post-content">
        <h1 class="post-title"><?php echo htmlspecialchars($sinhvat['tensinhvat']); ?></h1> <!-- Hiển thị tên sinh vật -->
        <h3>Ảnh chính</h3>
        <img src="../uploads/<?php echo htmlspecialchars($sinhvat['img']); ?>" alt="Main Image" class="main-img"> <!-- Hiển thị ảnh chính -->
        <h3>Đặc điểm</h3>
        <p><?php echo nl2br(($sinhvat['mota'])); ?></p> <!-- Hiển thị mô tả sinh vật -->
        <h3>ảnh phụ</h3>
        <?php while ($img = mysqli_fetch_assoc($imgs_result)): ?>
            <img src="../uploads/<?php echo htmlspecialchars($img['anh']); ?>" alt="Additional Image">
        <?php endwhile; ?>
    </div>

    <div class="questions-section">
        <h2>Hỏi Đáp</h2>
        <?php while ($question = mysqli_fetch_assoc($questions_result)): ?>
            <div class="question">
                <a href="hoso.php?id=<?php echo htmlspecialchars($question['id_nguoidung']); ?>" class="avatar">
                    <?php
                    // Đường dẫn đến ảnh đại diện của người hỏi
                    $question_avatar_path = '../uploads/' . htmlspecialchars($question['avatar']);
                    ?>
                    <img src="<?php echo $question_avatar_path; ?>" alt="Avatar">
                </a>
                <div class="question-details">
                    <p class="question-author">
                        <a href="hoso.php?id=<?php echo htmlspecialchars($question['id_nguoidung']); ?>" class="username">
                            <?php echo htmlspecialchars($question['fullname']); ?>
                        </a>
                        (<?php echo htmlspecialchars($question['email']); ?>)
                    </p>
                    <p class="question-date"><?php echo htmlspecialchars($question['thoigiantao']); ?></p>
                    <p class="question-content"><?php echo nl2br(($question['hoidap'])); ?></p>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="question-form">
            <h3>Đặt Câu Hỏi</h3>
            <form method="POST" action="">
                <textarea id="hoidap" name="question" rows="4" placeholder="Viết câu hỏi của bạn ở đây..."></textarea><br>
                <button type="submit">Gửi Câu Hỏi</button>
            </form>
        </div>
    </div>
</body>
</html>
<script>
    CKEDITOR.replace('hoidap');
</script>
