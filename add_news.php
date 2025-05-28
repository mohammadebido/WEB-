<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_STRING);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
    $published = isset($_POST['published']) && $_SESSION['role'] === 'admin' ? 1 : 0;
    $author_id = $_SESSION['user_id'];
    $image = null;

    // التحقق من صحة البيانات
    if (empty($title) || empty($body) || empty($category_id)) {
        $error = "يرجى ملء جميع الحقول المطلوبة.";
    } else {
        // معالجة الصورة
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
                $image = $_FILES['image']['name'];
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($image);
                move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            } else {
                $error = "الصورة غير صالحة أو كبيرة جدًا.";
            }
        }
        header("Location: manage_news.php");
        if (!isset($error)) {
            try {
                $stmt = $conn->prepare("INSERT INTO news (title, body, category_id, image ) VALUES (:title, :body, :category_id, :image)");
                $stmt->execute([
                    'title' => $title,
                    'body' => $body,
                    'category_id' => $category_id,
                    'image' => $image,
                   
                ]);
                
                exit;
            } catch(PDOException $e) {
                $error = "خطأ: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة خبر جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>إضافة خبر جديد</h1>
        <a href="dashboard.php" class="btn btn-secondary">العودة</a>
        <hr>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>العنوان:</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>المحتوى:</label>
                <textarea name="body" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id">القسم:</label>
                <select name="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
           
            <div class="mb-3">
                <label>صورة (اختياري):</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="mb-3">
                    <label>نشر الخبر:</label>
                    <input type="checkbox" name="published" value="1">
                </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">إضافة الخبر</button>
        </form>
    </div>
</body>
</html>