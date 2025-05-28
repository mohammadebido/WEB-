<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($name) || empty($username) || empty($password) || empty($role)) {
        $error = "يرجى ملء جميع الحقول.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :username, :password, :role)");
            $stmt->execute([
                'name' => $name,
                'username' => $username,
                'password' => $hashed_password,
                'role' => $role
            ]);
            $success = "تم إضافة المستخدم بنجاح.";
        } catch(PDOException $e) {
            $error = "خطأ: " . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إدارة المستخدمين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>إدارة المستخدمين</h1>
        <a href="dashboard.php" class="btn btn-secondary">العودة</a>
        <hr>
        <h2>إضافة مستخدم جديد</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>الاسم:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>اسم المستخدم (البريد الإلكتروني):</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>كلمة المرور:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>الدور:</label>
                <select name="role" class="form-control">
                    <option value="admin">مدير</option>
                    <option value="author">كاتب</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">إضافة مستخدم</button>
            
        </form>
        <hr>
        <h2>قائمة المستخدمين</h2>
        <table class="table">
            <thead>
            <tr>
            <th>الاسم</th>
            <th>اسم المستخدم</th>
            <th>الدور</th>
            <th>الإجراءات</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['role'] === 'admin' ? 'مدير' : 'كاتب'; ?></td>
            <td>
            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">تعديل</a>
            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">حذف</a>
            </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>