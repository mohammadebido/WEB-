<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: add_news.php");
    exit;
}

$role = $_SESSION['role'];
$author_id = $_SESSION['user_id'];

// حذف خبر
if (isset($_GET['delete'])) {
    $news_id = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("DELETE FROM news WHERE id = :id" . ($role !== 'admin' ? " AND author_id = :author_id" : ""));
    $params = ['id' => $news_id];
    if ($role !== 'admin') $params['author_id'] = $author_id;
    $stmt->execute($params);
    header("Location: manage_news.php");
    exit;
}

// نشر خبر (للمدير فقط)
if (isset($_GET['publish']) && $role === 'admin') {
    $news_id = filter_input(INPUT_GET, 'publish', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("UPDATE news SET published = 1 WHERE id = :id");
    $stmt->execute(['id' => $news_id]);
    header("Location: manage_news.php");
    exit;
}

// جلب الأخبار
$query = "SELECT n.id, n.title, n.body, n.datePosted ,n.category_id,n.author_name,n.status
         FROM news n ";

$stmt = $conn->prepare($query);
if ($role !== 'admin') $stmt->execute(['author_id' => $author_id]);
else $stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);  
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة الأخبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>إدارة الأخبار</h1>
        <a href="dashboard.php" class="btn btn-secondary">العودة</a>
        <hr>
        <table class="table">
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>القسم</th>
                    <th>الكاتب</th>
                    <th>تاريخ النشر</th>
                    <th>منشور</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $article): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                        <td><?php echo htmlspecialchars($article['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($article['author_name']); ?></td>
                        <td><?php echo $article['datePosted']; ?></td>
                        <td><?php echo $article['status'] ? 'نعم' : 'لا'; ?></td>
                        <td>
                            <a href="edit_news.php?id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm">تعديل</a>
                            <a href="manage_news.php?delete=<?php echo $article['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                            <?php if ($role === 'admin' && !$article['status']): ?>
                             
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>