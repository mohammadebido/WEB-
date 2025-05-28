<?php

$conn = new mysqli("localhost", "root", "", "news_system");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;

if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
    if ($stmt === false) {
        die("خطأ في تحضير الاستعلام: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $email, $role);
    if ($stmt->fetch()) {
        $user = ['name' => $username, 'email' => $email, 'role' => $role];
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id > 0) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    if ($stmt === false) {
        die("خطأ في تحضير الاستعلام: " . $conn->error);
    }
    $stmt->bind_param("sssi", $new_username, $new_email, $new_role, $user_id);
    if ($stmt->execute()) {
        echo "<p style='color:green;'>تم تحديث بيانات المستخدم بنجاح.</p>";
        $user = ['name' => $new_username, 'email' => $new_email, 'role' => $new_role];
    } else {
        echo "<p style='color:red;'>حدث خطأ أثناء التحديث.</p>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل مستخدم</title>
</head>
<body>
    <h2>تعديل بيانات المستخدم</h2>
    <?php if ($user): ?>
    <form method="post">
        <label>اسم المستخدم:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>
        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>
        <label>الدور:</label>
        <select name="role" required>
            <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>مدير</option>
            <option value="editor" <?php if($user['role']=='editor') echo 'selected'; ?>>محرر</option>
            <option value="user" <?php if($user['role']=='user') echo 'selected'; ?>>مستخدم</option>
        </select><br><br>
        <button type="submit">تحديث</button>
        <button type="button" onclick="window.location.href='manage_users.php'" style="margin-right:10px;">عودة</button>
    </form>
    <?php else: ?>
        <p>المستخدم غير موجود.</p>
    <?php endif; ?>
</body>
</html>