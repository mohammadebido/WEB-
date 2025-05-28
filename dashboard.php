<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>مرحبًا بك في لوحة التحكم</h1>
        <a href="logout.php" class="btn btn-danger">تسجيل الخروج</a>
        <hr>
        <?php if ($role === 'admin'): ?>
            <h2>إدارة الحسابات</h2>
            <a href="manage_users.php" class="btn btn-primary">إدارة المستخدمين</a>
            <h2>إدارة الإعلانات</h2>
            <a href="manage_ads.php" class="btn btn-primary">إدارة الإعلانات</a>
        <?php endif; ?>
        <h2>إدارة الأخبار</h2>
        <a href="add_news.php" class="btn btn-success">إضافة خبر جديد</a>
        <a href="manage_news.php" class="btn btn-info">إدارة الأخبار</a>
    </div>
</body>
</html>