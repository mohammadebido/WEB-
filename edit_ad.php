<?php
// edit_ad.php

// اتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "news_system");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// جلب بيانات الإعلان الحالي
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ad = null;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM ads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $ad = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<style>
    body {
        font-family: 'Tahoma', Arial, sans-serif;
        background: #f7f7f7;
        margin: 0;
        padding: 0;
        direction: rtl;
    }
    .container {
        background: #fff;
        max-width: 500px;
        margin: 40px auto 0 auto;
        padding: 30px 35px 25px 35px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }
    label {
        font-weight: bold;
        color: #444;
        display: block;
        margin-bottom: 7px;
        margin-top: 15px;
    }
    input[type="file"], textarea {
        width: 100%;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 7px;
        font-size: 15px;
        background: #fafafa;
        box-sizing: border-box;
    }
    textarea {
        resize: vertical;
        min-height: 80px;
    }
    button[type="submit"] {
        background: #1976d2;
        color: #fff;
        border: none;
        padding: 10px 0;
        width: 100%;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 15px;
        transition: background 0.2s;
    }
    button[type="submit"]:hover {
        background: #125ea2;
    }
    img {
        margin: 10px 0;
        border-radius: 6px;
        border: 1px solid #ddd;
        max-width: 100%;
        height: auto;
    }
    a {
        display: inline-block;
        margin-top: 18px;
        color: #1976d2;
        text-decoration: none;
        font-size: 15px;
    }
    a:hover {
        text-decoration: underline;
    }
    .msg {
        text-align: center;
        margin-bottom: 15px;
        font-size: 15px;
    }
</style>

<div class="container">
<?php
// تحديث بيانات الإعلان
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
    $content = $_POST['content'] ?? '';
    $stmt = $conn->prepare("UPDATE ads SET description = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $content, $id);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>تم تحديث الإعلان بنجاح.</p>";
            // تحديث البيانات المعروضة بعد التعديل
            $ad['description'] = $content;
        } else {
            echo "<p style='color:red;'>حدث خطأ أثناء التحديث.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>خطأ في تحضير الاستعلام: " . $conn->error . "</p>";
    }
}

if (!$ad) {
    echo "<p style='color:red;'>الإعلان غير موجود.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل الإعلان</title>
</head>
<body>
    <h2>تعديل الإعلان</h2>
    <form method="post">
      

        <label>صورة الإعلان الحالية:</label><br>
        <?php if (!empty($ad['image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($ad['image']); ?>" alt="صورة الإعلان" style="max-width:200px;"><br>
        <?php else: ?>
            <span>لا توجد صورة حالية.</span><br>
        <?php endif; ?>
        <label>تحديث صورة الإعلان:</label><br>
        <input type="file" name="image"><br><br>
        <label>محتوى الإعلان:</label><br>
        <textarea name="content" rows="5" cols="40" required><?php echo htmlspecialchars($ad['description']); ?></textarea><br><br>
        <button type="submit">تحديث</button>
    </form>
    <a href="manage_ads.php">العودة إلى قائمة الإعلانات</a>
</body>
</html>