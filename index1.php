<?php
session_start();
require_once 'db.php';

// Generate CSRF token for search form
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch categories
$stmt = $conn->prepare("SELECT id, name FROM category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle search
$search_query = '';
$articles_by_category = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $search_query = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_STRING);
    $search_query = '%' . $search_query . '%';
    foreach ($categories as $category) {
        $stmt = $conn->prepare(
            "SELECT n.*, c.name AS category_name, u.name AS author_name 
             FROM news n 
             JOIN category c ON n.category_id = c.id 
             JOIN users u ON n.author_id = u.id 
             WHERE n.status = 1 AND (n.title LIKE :search OR n.keywords LIKE :search) 
             AND c.id = :category_id 
             ORDER BY n.datePosted DESC LIMIT 3"
        );
        $stmt->execute(['search' => $search_query, 'category_id' => $category['id']]);
        $articles_by_category[$category['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // Fetch top 3 articles per category
    foreach ($categories as $category) {
        $stmt = $conn->prepare(
            "SELECT n.*, c.name AS category_name, u.name AS author_name 
             FROM news n 
             JOIN category c ON n.category_id = c.id 
             JOIN users u ON n.author_id = u.id 
             WHERE n.status = 1 AND c.id = :category_id 
             ORDER BY n.datePosted DESC LIMIT 3"
        );
        $stmt->execute(['category_id' => $category['id']]);
        $articles_by_category[$category['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>الصفحة الرئيسية | موقع الأخبار</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="app.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo">موقع الأخبار</div>
            <nav class="main-nav">
                <a href="index.php" onclick="navigateTo('index')">الرئيسية</a>
                <?php foreach ($categories as $category): ?>
                    <a href="category.php?cat_id=<?php echo $category['id']; ?>" onclick="navigateTo('category-<?php echo strtolower($category['name']); ?>')">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <form method="POST" class="d-flex">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="search" name="search" placeholder="ابحث هنا..." class="search-box form-control me-2" value="<?php echo htmlspecialchars($search_query); ?>" aria-label="ابحث عن أخبار">
                <button type="submit" class="btn btn-primary">بحث</button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main id="content" class="container">
        <section class="top-news">
            <h2>أبرز العناوين</h2>
            <?php foreach ($categories as $category): ?>
                <section class="section <?php echo strtolower($category['name']); ?>-section">
                    <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                    <div class="news-grid">
                        <?php foreach ($articles_by_category[$category['id']] as $article): ?>
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
                </section>
            <?php endforeach; ?>
        </section>
    </main>

    <!-- Footer -->
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