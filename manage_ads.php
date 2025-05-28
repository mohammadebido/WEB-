<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_URL);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $image = null;

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

    if (!isset($error) && $image) {
        try {
            $stmt = $conn->prepare("INSERT INTO ads (image, description, link) VALUES (:image, :description, :link)");
            $stmt->execute([
                'image' => $image,
                'description' => $description,
                'link' => $link
            ]);
            header("Location: manage_ads.php");
            exit;
        } catch(PDOException $e) {
            $error = "خطأ: " . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $ad_id = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("DELETE FROM ads WHERE id = :id");
    $stmt->execute(['id' => $ad_id]);
    header("Location: manage_ads.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM ads ORDER BY created_at DESC");
$stmt->execute();
$ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة الإعلانات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>إدارة الإعلانات</h1>
        <a href="dashboard.php" class="btn btn-secondary">العودة</a>
        <hr>
        <h2>إضافة إعلان جديد</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>صورة الإعلان:</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            <div class="mb-3">
                <label>وصف الإعلان:</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label>رابط الإعلان (اختياري):</label>
                <input type="url" name="link" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">إضافة الإعلان</button>
        </form>
        <hr>
        <h2>قائمة الإعلانات</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>الرابط</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ads as $ad): ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($ad['image']); ?>" alt="إعلان" width="100"></td>
                        <td><?php echo htmlspecialchars($ad['link'] ?: 'لا يوجد رابط'); ?></td>
                        <td>
                            <a href="edit_ad.php?id=<?php echo $ad['id']; ?>" class="btn btn-warning btn-sm">تعديل</a>
                            <a href="manage_ads.php?delete=<?php echo $ad['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>