<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';

$forumController = new ForumController($conn);
$threadId = $_GET['thread_id'] ?? null;

if (!$threadId) {
    echo "Thread ID is required.";
    exit;
}

// Fetch thread details for display (optional)
$thread = $forumController->getThreadById($threadId);
if (!$thread) {
    echo "Thread not found.";
    exit;
}

$userId = $_SESSION['user_id'] ?? null; // Ensure userId is set
$message = $_GET['message'] ?? null; // Optional message to display after rating
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rate Thread: <?= htmlspecialchars($thread['title']) ?></title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .rating-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px auto;
            max-width: 400px;
        }
        .star-rating {
            display: flex;
            justify-content: space-between;
            width: 120px;
        }
        .star {
            font-size: 24px;
            cursor: pointer;
            color: #ccc; /* Default color */
        }
        .star:hover,
        .star.selected {
            color: #f39c12; /* Highlight color */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="rating-form">
            <h2>Rate the Thread: <?= htmlspecialchars($thread['title']) ?></h2>
            <form action="../controller/forum_controller.php?action=rateThread" method="POST">
                <input type="hidden" name="thread_id" value="<?= $threadId ?>">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
                <input type="hidden" name="category_id" value="<?= htmlspecialchars($thread['category_id']) ?>">
                <div class="star-rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star" data-value="<?= $i ?>">★</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="rating" id="rating" required>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Submit Rating</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = star.getAttribute('data-value');
                ratingInput.value = value; // Set hidden input
                updateStars(value);
            });
        });

        function updateStars(value) {
            stars.forEach(star => {
                if (star.getAttribute('data-value') <= value) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }
    </script>
</body>
</html>