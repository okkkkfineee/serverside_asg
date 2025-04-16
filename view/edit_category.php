<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';

$forumController = new ForumController($conn);
$categoryId = $_GET['id'];
$category = $forumController->getCategoryById($categoryId);

if (!$category) {
    echo "Category not found.";
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
        <h2>Edit Category</h2>
        <form action="../controller/forum_controller.php?action=editCategory" method="POST">
            <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id']) ?>">
    <div class="mb-3">
        <label for="title" class="form-label">Category Title</label>
        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label for="content" class="form-label">Category Description</label>
        <textarea class="form-control" name="content" rows="5" required><?= htmlspecialchars($category['description']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Category</button>
</form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>