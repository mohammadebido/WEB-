<?php
require_once 'db.php';

// معالجة إضافة التعليق عبر AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json; charset=utf-8');
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    $news_id = filter_input(INPUT_POST, 'news_id', FILTER_SANITIZE_NUMBER_INT);

    if (empty($user_name) || empty($comment)) {
        echo json_encode(['success' => false, 'error' => 'يرجى ملء جميع الحقول.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO comments (news_id, name, comment) VALUES (:news_id, :user_name, :comment)");
        $stmt->execute(['news_id' => $news_id, 'user_name' => $user_name, 'comment' => $comment]);
        $comment_id = $conn->lastInsertId();
        $stmt = $conn->prepare("SELECT * FROM comments WHERE id = :id");
        $stmt->execute(['id' => $comment_id]);
        $new_comment = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'comment' => $new_comment]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'خطأ: ' . $e->getMessage()]);
    }
    exit;
}

$news_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$stmt = $conn->prepare("SELECT n.*, c.name AS category_name, u.name AS author_name 
                       FROM news n 
                       JOIN category c ON n.category_id = c.id 
                       JOIN users u ON n.author_id = u.id 
                       WHERE n.id = :id AND n.status = 1");
$stmt->execute(['id' => $news_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: index.php");
    exit;
}

// زيادة عدد المشاهدات
$stmt = $conn->prepare("UPDATE news SET views = views + 1 WHERE id = :id");
$stmt->execute(['id' => $news_id]);

// إضافة تعليق
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    if (empty($user_name) || empty($comment)) {
        $error = "يرجى ملء جميع الحقول.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (news_id, name, comment) VALUES (:news_id, :user_name, :comment)");
            $stmt->execute(['news_id' => $news_id, 'user_name' => $user_name, 'comment' => $comment]);
        } catch(PDOException $e) {
            $error = "خطأ: " . $e->getMessage();
        }
    }
}

// استرجاع التعليقات
$stmt = $conn->prepare("SELECT * FROM comments WHERE news_id = :news_id ORDER BY created_at DESC");
$stmt->execute(['news_id' => $news_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
        <p><small class="text-muted">بواسطة: <?php echo htmlspecialchars($article['author_name']); ?> | القسم: <?php echo htmlspecialchars($article['category_name']); ?> | <?php echo $article['datePosted']; ?> | المشاهدات: <?php echo $article['views']; ?></small></p>
        <?php if ($article['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($article['image']); ?>" alt="صورة الخبر" class="img-fluid mb-3">
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($article['body'])); ?></p>
        <p><strong>الكلمات المفتاحية:</strong> <?php echo htmlspecialchars($article['keywords']); ?></p>
        <a href="index.php" class="btn btn-secondary">العودة</a>

        <hr>
        <h2>التعليقات</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>اسمك:</label>
                <input type="text" name="user_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>تعليقك:</label>
                <textarea name="comment" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إضافة تعليق</button>
        </form>
        <hr>
        <?php foreach ($comments as $comment): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($comment['user_name']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    <p class="card-text"><small class="text-muted"><?php echo $comment['created_at']; ?></small></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>