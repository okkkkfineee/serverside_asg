<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/forum_controller.php';
require '../controller/user_controller.php';

$forumController = new ForumController($conn);
$userController = new UserController($conn);

// Fetch categories here
$categories = $forumController->getAllCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Categories</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Forum Categories</h1>

        <!-- Conditionally display the Create Category button -->
        <?php if ($userController->isSuperadmin() || $userController->isAdmin() || $userController->isMod()): ?>
            <a href="create_category.php" class="btn btn-success mb-3">Create Category</a>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($categories)): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">No categories available at the moment.</div>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card shadow-sm border-light" style="height: 180px;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($category['description']) ?></p>
                            </div>
                            <div class="d-flex justify-content-between p-3">
                                <?php if ($userController->isSuperadmin() || $userController->isAdmin() || $userController->isMod()): ?>
                                    <div class="d-flex gap-2">
                                        <a href="edit_category.php?id=<?= $category['category_id'] ?>" class="btn btn-warning">Edit</a>
                                        <form action="../controller/forum_controller.php?action=deleteCategory" method="POST" style="display:inline;">
                                            <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                            <button type="submit" class="btn btn-danger" style="background-color: red !important;" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="category_threads.php?id=<?= $category['category_id'] ?>" class="btn btn-primary">View Threads</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
