<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';

$forumController = new ForumController($conn);
$threadId = $_GET['id'];
$thread = $forumController->getThreadById($threadId);

if (!$thread) {
    echo "Thread not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Thread</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Thread</h2>
        <form action="../controller/forum_controller.php?action=updateThread" method="POST">
            <input type="hidden" name="thread_id" value="<?= $thread['thread_id'] ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($thread['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" name="content" rows="5" required><?= htmlspecialchars($thread['content']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Thread</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>