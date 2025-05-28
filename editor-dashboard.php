<?php
session_start();
require 'db.php';

$stmt = $conn->query("SELECT news.*, user.name AS author FROM news JOIN user ON news.author_id = user.id ORDER BY dateposted DESC");
$news = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Editor Dashboard</title></head>
<body>
<h1>Editor Dashboard</h1>
<table border="1">
<tr><th>Title</th><th>Author</th><th>Date</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($news as $item): ?>
<tr>
    <td><?= htmlspecialchars($item['title']) ?></td>
    <td><?= htmlspecialchars($item['author']) ?></td>
    <td><?= $item['dateposted'] ?></td>
    <td><?= $item['status'] ?></td>
    <td>
        <a href="approve.php?id=<?= $item['id'] ?>">Approve</a>
        <a href="deny.php?id=<?= $item['id'] ?>">Deny</a>
        <a href="delete.php?id=<?= $item['id'] ?>">Delete</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
<?php