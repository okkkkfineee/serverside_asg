<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';
require '../controller/user_controller.php';

$forumController = new ForumController($conn);
$userController = new UserController($conn);

// Get the current user ID from session
$currentUserId = $_SESSION['user_id'] ?? null;

$categoryId = $_GET['id'] ?? null;
$category = $forumController->getCategoryById($categoryId);

if (!$category) {
    echo "Category not found.";
    exit;
}

// Fetch threads for the given category
$threads = $forumController->getThreadsByCategory($categoryId);

// Check for a message in the URL parameters
$message = $_GET['message'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Threads in <?= htmlspecialchars($category['name']) ?></title>
  <link rel="icon" href="../assets/images/icon.png">
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
      position: relative; /* Enable positioning for rating */
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
    .reply-actions {
      margin-top: 10px;
    }
    .rating {
      position: absolute;
      top: 20px;
      right: 20px;
      font-weight: bold;
      color: #007bff;
    }
  </style>
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="container mt-5">
    <h2 class="mb-4"><?= htmlspecialchars($category['name']) ?></h2>
    <p class="lead"><?= htmlspecialchars($category['description']) ?></p>
    <a href="create_thread_form.php?category_id=<?= htmlspecialchars($categoryId) ?>" class="btn btn-success mb-3">Create New Thread</a>

    <?php if (empty($threads)): ?>
      <div class="alert alert-info">No threads yet. Be the first to post!</div>
    <?php else: ?>
        <?php foreach ($threads as $thread): ?>
            <?php 
              // Get thread creator details to display username
              $threadCreatorId = $forumController->getThreadUser($thread['thread_id']);
              // Get username from user controller
              $threadCreator = $userController->getUserInfo($threadCreatorId);
              // Get average rating for the thread
              $averageRating = $forumController->getAverageRating($thread['thread_id'], $thread['category_id']);
            ?>
            <div class="thread-box">
                <div class="rating">Rating: <?= number_format($averageRating, 1) ?> / 5</div>
                <div class="thread-title" onclick="toggleReplies(<?= $thread['thread_id'] ?>)">
                    <?= htmlspecialchars($thread['title']) ?>
                    <i class="fas fa-chevron-down toggle-icon" id="icon-<?= $thread['thread_id'] ?>"></i>
                </div>
                <p class="text-muted">Created by: <strong><?= htmlspecialchars($threadCreator['username'] ?? 'Unknown') ?></strong> at <?= htmlspecialchars($thread['created_time']) ?></p>
                <div class="thread-content"><?= htmlspecialchars($thread['content']) ?></div>

                <div id="replies-<?= $thread['thread_id'] ?>" class="replies d-none">
                    <?php $posts = $forumController->getPostsByThread($thread['thread_id']); ?>
                    <?php foreach ($posts as $post): ?>
                        <?php 
                            // Get post author's username
                            $postAuthor = $userController->getUserInfo($post['user_id']);
                        ?>
                        <p><strong><?= htmlspecialchars($postAuthor['username'] ?? 'Unknown') ?>:</strong> <?= htmlspecialchars($post['content']) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="thread-actions reply-actions d-flex align-items-center mt-3">
                    <div class="me-2">
                        <!-- if the current user is the thread creator or an admin, show edit and delete buttons -->
                        <?php if ($currentUserId === $threadCreatorId || $userController->isSuperadmin() || $userController->isAdmin() || $userController->isMod()): ?>
                            <a href="edit_thread_form.php?id=<?= $thread['thread_id'] ?>" class="btn btn-warning btn-sm px-3">Edit</a>
                            <a href="delete_thread.php?id=<?= $thread['thread_id'] ?>&category_id=<?= $categoryId ?>" class="btn btn-danger btn-sm me-2" onclick="return confirm('Are you sure you want to delete this thread?');">Delete</a>
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="thread.php?id=<?= $thread['thread_id'] ?>" class="btn btn-primary btn-sm me-2">Create Post (Reply)</a>
                        <?php if ($currentUserId !== $threadCreatorId): ?>
                            <a href="rating_form.php?thread_id=<?= $thread['thread_id'] ?>" class="btn btn-secondary btn-sm">Rate this Thread</a> <!-- Rating Button -->
                        <?php else: ?>
                            <span class="text-light btn btn-secondary btn-sm disabled" disabled>Cannot Rate Your Own Thread</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if ($message): ?>
    <script>
        alert("<?= htmlspecialchars($message) ?>");
    </script>
  <?php endif; ?>

  <script>
    function toggleReplies(threadId) {
      const repliesDiv = document.getElementById(`replies-${threadId}`);
      const icon = document.getElementById(`icon-${threadId}`);

      // Toggle visibility using classList
      repliesDiv.classList.toggle("d-none"); 
      icon.classList.toggle("fa-chevron-down");
      icon.classList.toggle("fa-chevron-up");
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>