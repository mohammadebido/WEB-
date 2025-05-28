<?php

include 'db.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']); 

    
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "User deleted successfully.";
        } else {
            echo "Error deleting user: " . $stmt->error;
        }

        unset($stmt);
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No user ID provided.";
}

$conn = null;

?>