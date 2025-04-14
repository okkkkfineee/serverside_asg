<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';
require '../controller/user_controller.php';

$forumController = new ForumController($conn);
$userController = new UserController($conn);

if (!isset($_SESSION['user_id']) || 
    (!$userController->isSuperadmin() && !$userController->isAdmin() && !$userController->isMod())) {
    // Redirect to an error page or show an error message
    echo "You do not have permission to create a category.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Category</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Create Category</h1>
        <form action="../controller/forum_controller.php?action=createCategoryAction" method="post">
             <div class="mb-3">
                <label for="category_name" class="form-label">Category Name:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <div class="mb-3">
                <label for="category_description" class="form-label">Description:</label>
                <textarea class="form-control" id="category_description" name="category_description" rows="5" required></textarea>
            </div>
    <button type="submit" class="btn btn-primary">Create Category</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>