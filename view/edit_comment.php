<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../controller/comment_controller.php';

// Initialize database connection
$db = require_once '../config/db_connection.php';

// Initialize comment controller
$commentController = new CommentController($db);

// Get comment ID and discussion ID
$commentId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$discussionId = isset($_GET['discussion_id']) ? intval($_GET['discussion_id']) : 0;

// Redirect if IDs are missing
if ($commentId <= 0 || $discussionId <= 0) {
    $_SESSION['error'] = "Comment ID or Discussion ID is required.";
    header('Location: discussions.php');
    exit;
}

// Get comment details (you'll need to add this method to CommentController and Comment model)
$comment = $commentController->getComment($commentId);
if (!$comment) {
    $_SESSION['error'] = "Comment not found.";
    header("Location: view_discussion.php?id=$discussionId");
    exit;
}

// Check if user has permission to edit
if ($comment['user_id'] != $_SESSION['user_id'] && $_SESSION['roles'] != 'admin') {
    $_SESSION['error'] = "You don't have permission to edit this comment.";
    header("Location: view_discussion.php?id=$discussionId");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = isset($_POST['commentText']) ? trim($_POST['commentText']) : '';
    $userId = $_SESSION['user_id'];

    $result = $commentController->updateComment($commentId, $userId, $text);
    if ($result === true) {
        $_SESSION['success'] = "Comment updated successfully.";
        header("Location: view_discussion.php?id=$discussionId");
        exit;
    } else {
        $_SESSION['error'] = $result;
    }
}
?>

<div class="container mt-4">
    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="discussions.php">Discussions</a></li>
            <li class="breadcrumb-item"><a href="view_discussion.php?id=<?php echo $discussionId; ?>">Discussion</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Comment</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h1 class="h3 mb-0">Edit Comment</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="edit_comment.php?id=<?php echo $commentId; ?>&discussion_id=<?php echo $discussionId; ?>">
                <div class="form-group">
                    <label for="commentText">Comment</label>
                    <textarea class="form-control" id="commentText" name="commentText" rows="3" required><?php echo htmlspecialchars($comment['comment_text']); ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="view_discussion.php?id=<?php echo $discussionId; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>