<?php
session_start();
include('conn.php');
include('function.php');
include('header.php');
// check_login();
// Số lượng bài đăng và sinh vật cần hiển thị
$posts_limit = 5;
$species_limit = 5;

// Truy vấn các bài đăng có nhiều lượt bình luận nhất
$query_most_commented_posts = "SELECT b.id_baidang, b.tieude, b.mota, b.thoigiantao, 
                                      u.fullname AS author_name, 
                                      a.anh AS post_image,
                                      COUNT(c.id_binhluan) as comment_count
                               FROM baidang b
                               LEFT JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung
                               LEFT JOIN anh_baidang a ON b.id_baidang = a.id_baidang
                               LEFT JOIN binhluan c ON b.id_baidang = c.id_baidang
                               WHERE b.trangthai = 0
                               GROUP BY b.id_baidang
                               ORDER BY comment_count DESC
                               LIMIT $posts_limit";

// Truy vấn các bài đăng mới nhất
$query_latest_posts = "SELECT b.id_baidang, b.tieude, b.mota, b.thoigiantao, 
                               u.fullname AS author_name,
                               a.anh AS post_image
                       FROM baidang b
                       LEFT JOIN nguoidung u ON b.id_nguoidung = u.id_nguoidung
                       LEFT JOIN anh_baidang a ON b.id_baidang = a.id_baidang
                       WHERE b.trangthai = 0
                       ORDER BY b.thoigiantao DESC
                       LIMIT $posts_limit";

// Truy vấn các sinh vật có nhiều hỏi đáp nhất
$query_most_qa_species = "SELECT s.id_sinhvat, s.tensinhvat, s.img, COUNT(q.id_hoidap) as qa_count
                          FROM sinhvat s
                          LEFT JOIN hoidap q ON s.id_sinhvat = q.id_sinhvat
                          GROUP BY s.id_sinhvat
                          ORDER BY qa_count DESC
                          LIMIT $species_limit";

// Truy vấn các sinh vật mới nhất
$query_latest_species = "SELECT s.id_sinhvat, s.tensinhvat, s.img
                         FROM sinhvat s
                         ORDER BY s.thoigiantao DESC
                         LIMIT $species_limit";

// Thực thi các câu truy vấn
$result_most_commented_posts = mysqli_query($conn, $query_most_commented_posts);
$result_latest_posts = mysqli_query($conn, $query_latest_posts);
$result_most_qa_species = mysqli_query($conn, $query_most_qa_species);
$result_latest_species = mysqli_query($conn, $query_latest_species);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    
    <title>Trang Chủ</title>
    <style>
       <style>
    /* Tổng thể */
   /* Tổng thể */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
}

.content {
    max-width: 1500px;
    margin: auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 28px;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

/* Danh sách bài viết và sinh vật */
.post-list, .species-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.post-item, .species-item {
    flex: 1 1 calc(33.333% - 20px);
    box-sizing: border-box;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-bottom: 20px;
}

.post-item:hover, .species-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    background-color:#97eab4;
    
}

.post-item img, .species-item img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.post-info, .species-info {
    padding: 15px;
}

.post-info h3, .species-info h3 {
    margin: 0;
    font-size: 20px;
    color: #007bff;
    text-decoration: none; /* Remove underline */
}

.post-info p, .species-info p {
    margin: 5px 0;
    line-height: 1.5;
}

.post-info p strong, .species-info p strong {
    color: #333;
}

.post-item a, .species-item a {
    color: inherit; /* Inherit color from parent */
    text-decoration: none; /* Remove underline */
}

.post-item a:hover, .species-item a:hover {
    text-decoration: none; /* Ensure no underline on hover */
}

/* Responsive */
@media (max-width: 768px) {
    .post-item, .species-item {
        flex: 1 1 100%;
    }

    .post-item img, .species-item img {
        height: 150px;
    }
}

</style>

    </style>
</head>
<body>
<div class="content">
        <!-- Phần bài đăng có nhiều lượt bình luận nhất -->
        <div class="section">
            <div class="section-title">Bài đăng có nhiều lượt bình luận nhất</div>
            <div class="post-list">
                <?php while ($row = mysqli_fetch_assoc($result_most_commented_posts)): ?>
                    <div class="post-item">
                        <a href="chitietbaidang.php?id=<?php echo $row['id_baidang']; ?>">
                            <?php if (!empty($row['post_image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($row['post_image']); ?>" alt="<?php echo htmlspecialchars($row['tieude']); ?>">
                            <?php endif; ?>
                            <div class="post-info">
                                <h3><?php echo htmlspecialchars($row['tieude']); ?></h3>
                                <p><?php echo substr(($row['mota']), 0, 100) . '...'; ?></p>
                                <p><strong>Người đăng:</strong> <?php echo htmlspecialchars($row['author_name']); ?></p>
                                <p><strong>Bình luận:</strong> <?php echo $row['comment_count']; ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Phần bài đăng mới nhất -->
        <div class="section">
            <div class="section-title">Bài đăng mới nhất</div>
            <div class="post-list">
                <?php while ($row = mysqli_fetch_assoc($result_latest_posts)): ?>
                    <div class="post-item">
                        <a href="chitietbaidang.php?id=<?php echo $row['id_baidang']; ?>">
                            <?php if (!empty($row['post_image'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($row['post_image']); ?>" alt="<?php echo htmlspecialchars($row['tieude']); ?>">
                            <?php endif; ?>
                            <div class="post-info">
                                <h3><?php echo htmlspecialchars($row['tieude']); ?></h3>
                                <p><?php echo substr(($row['mota']), 0, 100) . '...'; ?></p>
                                <p><strong>Người đăng:</strong> <?php echo htmlspecialchars($row['author_name']); ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Phần sinh vật có nhiều hỏi đáp nhất -->
        <div class="section">
            <div class="section-title">Sinh vật có nhiều hỏi đáp nhất</div>
            <div class="species-list">
                <?php while ($row = mysqli_fetch_assoc($result_most_qa_species)): ?>
                    <div class="species-item">
                        <a href="chitietsinhvat.php?id=<?php echo $row['id_sinhvat']; ?>">
                            <?php if (!empty($row['img'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="<?php echo htmlspecialchars($row['tensinhvat']); ?>">
                            <?php endif; ?>
                            <div class="species-info">
                                <h3><?php echo htmlspecialchars($row['tensinhvat']); ?></h3>
                                <p><?php echo $row['qa_count']; ?> hỏi đáp</p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Phần sinh vật mới nhất -->
        <div class="section">
            <div class="section-title">Sinh vật mới nhất</div>
            <div class="species-list">
                <?php while ($row = mysqli_fetch_assoc($result_latest_species)): ?>
                    <div class="species-item">
                        <a href="chitietsinhvat.php?id=<?php echo $row['id_sinhvat']; ?>">
                            <?php if (!empty($row['img'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($row['img']); ?>" alt="<?php echo htmlspecialchars($row['tensinhvat']); ?>">
                            <?php endif; ?>
                            <div class="species-info">
                                <h3><?php echo htmlspecialchars($row['tensinhvat']); ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>