<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';

$forumController = new ForumController($conn);

if (isset($_GET['id'])) {
    $threadId = (int)$_GET['id'];
    $result = $forumController->deleteThread($threadId);

    if ($result === true) {
        header("Location: category_threads.php?id=" . $_GET['category_id']); // Redirect back to category
        exit;
    } else {
        echo "Error deleting thread: " . htmlspecialchars($result);
    }
} else {
    echo "Thread ID is missing.";
}
?>