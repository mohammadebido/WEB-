<?php
session_start();
require 'db.php';

$author_id = $_SESSION['user_id']; 
$stmt = $conn->prepare("SELECT * FROM news WHERE author_id = ?");
$stmt->execute([$author_id]);
$news = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Author Dashboard</title></head>
<body>
<h1>Author Dashboard</h1>
<a href="add-news.php">Add New News Item</a>
<table border="1">
    <tr><th>Title</th><th>Date</th><th>Status</th></tr>
    <?php foreach ($news as $item): ?>
    <tr>
        <td><?= htmlspecialchars($item['title']) ?></td>
        <td><?= $item['dateposted'] ?></td>
        <td><?= $item['status'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
<?php
