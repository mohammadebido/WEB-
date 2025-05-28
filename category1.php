<?php
require ("db.php");

$cat_id = filter_input(INPUT_GET, 'cat_id', FILTER_SANITIZE_NUMBER_INT);
if (!$cat_id) {
    header("Location: index1.php");
    exit;
}

$stmt = $conn->prepare("SELECT name FROM category WHERE id = :id");
$stmt->execute(['id' => $cat_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$category) {
    header("Location: index1.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT n.*, c.name AS category_name, u.name AS author_name 
     FROM news n 
     JOIN category c ON n.category_id = c.id 
     JOIN users u ON n.author_id = u.id 
     WHERE n.status = 1 AND c.id = :cat_id 
     ORDER BY n.datePosted DESC"
);
$stmt->execute(['cat_id' => $cat_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($category['name']); ?> | موقع الأخبار</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">موقع الأخبار</div>
            <nav class="main-nav">
                <a href="index.php">الرئيسية</a>
                <?php
                $stmt = $conn->prepare("SELECT id, name FROM category");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $cat): ?>
                    <a href="category.php?cat_id=<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <form method="POST" action="index.php" class="d-flex">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="search" name="search" placeholder="ابحث هنا..." class="search-box form-control me-2" aria-label="ابحث عن أخبار">
                <button type="submit" class="btn btn-primary">بحث</button>
            </form>
        </div>
    </header>

    <main class="container">
        <h2><?php echo htmlspecialchars($category['name']); ?></h2>
        <div class="news-grid">
            <?php foreach ($articles as $article): ?>
                <div class="news-card">
                    <?php if ($article['image']): ?>
                        <img src="Uploads/<?php echo htmlspecialchars($article['image']); ?>" alt="صورة الخبر" style="border-radius: 10px;" class="img-fluid">
                    <?php else: ?>
                        <img src="placeholder.jpg" alt="صورة افتراضية" style="border-radius: 10px;" class="img-fluid">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($article['body'], 0, 100)) . '...'; ?></p>
                    <p><small class="text-muted">بواسطة: <?php echo htmlspecialchars($article['author_name']); ?> | <?php echo date('Y-m-d', strtotime($article['datePosted'])); ?></small></p>
                    <a href="article.php?id=<?php echo $article['id']; ?>" class="read-more-link">قراءة المزيد</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>جميع الحقوق محفوظة © 2025</p>
            <p>تابعنا على:
                <span class="social-icons">
                    <a href="https://www.facebook.com" target="_blank"><img src="flq.jpg" alt="فيسبوك"></a>
                    <a href="https://www.twitter.com" target="_blank"><img src="2-27646_twitter-logo-png-transparent-background-logo-twitter-png.jpg" alt="تويتر"></a>
                    <a href="https://www.instagram.com" target="_blank"><img src="icon.jpg" alt="إنستغرام"></a>
                </span>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k8eQJ0R2y8eL9x7hT6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ6jJ" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>
</body>
</html>