<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = filter_input(INPUT_POST, 'news_id', FILTER_SANITIZE_NUMBER_INT);
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    if (empty($user_name) || empty($comment) || empty($news_id)) {
        echo json_encode(['success' => false, 'message' => 'يرجى ملء جميع الحقول.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO comments (news_id, user_name, comment) VALUES (:news_id, :user_name, :comment)");
        $stmt->execute(['news_id' => $news_id, 'user_name' => $user_name, 'comment' => $comment]);
        echo json_encode(['success' => true, 'message' => 'تم إضافة التعليق بنجاح.']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
    }
}
?>