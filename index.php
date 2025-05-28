<?php
require_once 'db.php';

// الأخبار العادية
$stmt = $conn->prepare("SELECT n.id, n.title, n.body, n.image, n.datePosted, n.views, c.name AS category_name, u.name AS author_name 
                       FROM news n 
                       JOIN category c ON n.category_id = c.id 
                       JOIN users u ON n.author_id = u.id 
                       WHERE n.status = 1 
                       ORDER BY n.datePosted DESC");
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);

// الأكثر قراءة
$stmt = $conn->prepare("SELECT n.id, n.title, n.views 
                       FROM news n 
                       WHERE n.status = 1 
                       ORDER BY n.views DESC LIMIT 5");
$stmt->execute();
$most_read = $stmt->fetchAll(PDO::FETCH_ASSOC);
// الإعلانات
$stmt = $conn->prepare("SELECT * FROM ads ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
?>


<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>موقع الأخبار</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h1>آخر الأخبار</h1>
                <div class="row">
                    <?php foreach ($news as $article): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <?php if ($article['image']): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($article['image']); ?>" class="card-img-top" alt="صورة الخبر">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                                    <p class="card-text"><?php echo substr(htmlspecialchars($article['body']), 0, 100) . '...'; ?></p>
                                    <p class="card-text"><small class="text-muted">بواسطة: <?php echo htmlspecialchars($article['author_name']); ?> | القسم: <?php echo htmlspecialchars($article['category_name']); ?> | <?php echo $article['datePosted']; ?></small></p>
                                    <a href="news_detail.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">اقرأ المزيد</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-4">
                <h2>الأكثر قراءة</h2>
                <ul class="list-group">
                    <?php foreach ($most_read as $article): ?>
                        <li class="list-group-item">
                            <a href="news_detail.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a>
                            <p><small class="text-muted">المشاهدات: <?php echo $article['views']; ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h2 class="mt-4">الإعلانات</h2>
                <?php foreach ($ads as $ad): ?>
                    <div class="card mb-3">
                        <?php if (!empty($ad['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($ad['image']); ?>" class="card-img-top" alt="إعلان">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($ad['link']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($ad['description']); ?></p>
                            <?php if (!empty($ad['link'])): ?>
                                <a href="<?php echo htmlspecialchars($ad['link']); ?>" class="btn btn-success" target="_blank">المزيد</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>