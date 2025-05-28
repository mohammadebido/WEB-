<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "news_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$title = $content = "";
if ($id > 0) {
    $stmt = $conn->prepare("SELECT title, body FROM news WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($title, $content);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $id > 0) {
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE news SET title = ?, body = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_title, $new_content, $id);
    if ($stmt->execute()) {
        echo "News updated successfully.";
    } else {
        echo "Error updating news.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit News</title>
</head>
<body>
    <h2>Edit News</h2>
    <form method="post">
        <label>Title:</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br><br>
        <label>Content:</label><br>
        <textarea name="content" rows="8" cols="50" required><?php echo htmlspecialchars($content); ?></textarea><br><br>
        <input type="submit" value="Update News">
    </form>
</body>
</html>