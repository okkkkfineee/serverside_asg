<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';
require '../controller/user_controller.php';

$forumController = new ForumController($conn);
$userController = new UserController($conn);

$categoryId = $_GET['id'] ?? null;
$category = $forumController->getCategoryById($categoryId);

if (!$category) {
    echo "Category not found.";
    exit;
}

// Fetch threads for the given category
$threads = $forumController->getThreadsByCategory($categoryId);
$userDetail = $forumController->getThreadDetails($categoryId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Threads in <?= htmlspecialchars($category['name']) ?></title>
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      color: #343a40;
    }
    .thread-box {
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-bottom: 20px;
    }
    .thread-title {
      font-size: 24px;
      margin-bottom: 10px;
      font-weight: bold;
      cursor: pointer; /* Indicate clickable */
    }
    .thread-content {
      font-size: 16px;
      margin-bottom: 20px;
      border-left: 4px solid #007bff;
      padding-left: 10px;
      color: #495057;
    }
    .reply-button {
      cursor: pointer;
    }
    .reply-actions {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="container mt-5">
    <h2 class="mb-4"><?= htmlspecialchars($category['name']) ?></h2>
    <p class="lead"><?= htmlspecialchars($category['description']) ?></p>
    <a href="create_thread_form.php?category_id=<?= $categoryId ?>" class="btn btn-success mb-3">Create New Thread</a>

    <?php if (empty($threads)): ?>
      <div class="alert alert-info">No threads yet. Be the first to post!</div>
    <?php else: ?>
        <?php foreach ($threads as $thread): ?>
            <div class="thread-box">
                <div class="thread-title" onclick="toggleReplies(<?= $thread['thread_id'] ?>)">
                    <?= htmlspecialchars($thread['title']) ?>
                    <i class="fas fa-chevron-down toggle-icon" id="icon-<?= $thread['thread_id'] ?>"></i>
                </div>
                <p class="text-muted">Created by: <strong><?= htmlspecialchars($userDetail['username'] ?? 'Unknown') ?></strong> at <?= htmlspecialchars($thread['created_time']) ?></p>
                <div class="thread-content"><?= htmlspecialchars($thread['content']) ?></div>

                <div id="replies-<?= $thread['thread_id'] ?>" class="replies" style="display:none;">
                    <?php $posts = $forumController->getPostsByThread($thread['thread_id']); ?>
                    <?php foreach ($posts as $post): ?>
                        <p><strong><?= htmlspecialchars($post['user_id']) ?>:</strong> <?= htmlspecialchars($post['content']) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="thread-actions reply-actions">
                    <a href="edit_thread_form.php?id=<?= $thread['thread_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_thread.php?id=<?= $thread['thread_id'] ?>&category_id=<?= $categoryId ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this thread?');">Delete</a>
                    <a href="thread.php?id=<?= $thread['thread_id'] ?>" class="btn btn-primary btn-sm">Create Post (Reply)</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script>
    function toggleReplies(threadId) {
        const repliesDiv = document.getElementById(`replies-${threadId}`);
        const icon = document.getElementById(`icon-${threadId}`);
        const isVisible = repliesDiv.style.display === "block";

        repliesDiv.style.display = isVisible ? "none" : "block";
        icon.classList.toggle("fa-chevron-down", isVisible);
        icon.classList.toggle("fa-chevron-up", !isVisible);
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>