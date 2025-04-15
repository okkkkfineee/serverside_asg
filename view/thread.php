<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';

$forumController = new ForumController($conn);
$threadId = $_GET['id'] ?? null; 

$thread = $forumController->getThreadById($threadId);
$posts = $forumController->getPostsByThread($threadId);

if (!$thread) {
    echo "Thread not found.";
    exit;
}

$userId = $_SESSION['user_id'] ?? null; // Ensure userId is set
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($thread['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .thread-container {
            position: relative; /* Make container relative for absolute positioning of back button */
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
        }
        .thread-title {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .thread-content {
            font-size: 16px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            padding-left: 10px;
            color: #495057;
        }
        .reply-form {
            margin-top: 30px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 8px;
            border: 1px solid #007bff;
        }
        .reply-button {
            background-color: #007bff;
            color: white;
        }
        .reply-button:hover {
            background-color: #0056b3;
        }
        .back-button {
            position: absolute; /* Positioning it absolutely within the container */
            top: 20px;
            right: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .reply {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .reply:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="thread-container">
            <a href="category_threads.php?id=<?= $thread['category_id'] ?>" class="back-button">Back to Threads</a>
            <h2 class="thread-title"><?= htmlspecialchars($thread['title']) ?></h2>
            <p class="thread-content"><?= htmlspecialchars($thread['content']) ?></p>
            <p class="text-muted">Created on: <?= htmlspecialchars($thread['created_time']) ?></p>

            <h4>Replies:</h4>
            <?php if (empty($posts)): ?>
                <div class="alert alert-info">No replies yet. Be the first to reply!</div>
            <?php endif; ?>
            <div class="replies">
                <?php foreach ($posts as $post): ?>
                    <div class="reply">
                        <?php 
                        $postUser = $forumController->getPostUserName($post['post_id']); 
                        ?>
                        <p><strong><?= htmlspecialchars($postUser) ?>:</strong> <?= htmlspecialchars($post['content']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Reply Form -->
            <div class="reply-form">
                <h4>Reply to this thread:</h4>
                <form id="replyForm" action="../controller/forum_controller.php?action=createPost" method="POST">
                    <input type="hidden" name="thread_id" value="<?= $threadId ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="Write your reply..." required></textarea>
                    </div>
                    <button type="submit" class="btn reply-button">Submit Reply</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>