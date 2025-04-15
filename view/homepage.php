<?php

session_start();
require '../config/db_connection.php';
require '../controller/recipe_controller.php';

$recipeController = new RecipeController($conn);

$recipes = $recipeController->getAllRecipes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>Welcome to Recipe.com!</h1>
            <p class="lead">Discover, create, and share your favorite recipes.</p>
        </div>

        <div class="row text-center">
            <div class="col-md-6 mb-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Browse Recipes</h5>
                        <p class="card-text">Explore a wide variety of dishes from different cuisines and difficulty levels.</p>
                        <a href="recipe_list" class="btn btn-primary">Explore</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Join Competitions</h5>
                        <p class="card-text">Show off your cooking skills and compete with others in exciting competitions.</p>
                        <a href="competition_list" class="btn btn-success">Join Now</a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mt-5">

        <h3 class="text-center mt-5">Recent Recipes</h3>
        <?php
        if (!empty($recipes)) : ?>
            <div class="row mt-4">
                <?php foreach (array_slice($recipes, 0, 9) as $recipe) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm" style="width: 100%; height: 400px;">
                            <img src="../uploads/recipes/<?= $recipe['images'] ?>" class="card-img-top" alt="<?= $recipe['title'] ?>" style="width: 100%; height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= $recipe['title'] ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'], 0, 80)) . '...'; ?></p>
                                <a href="view_recipe?recipe_id=<?= $recipe['recipe_id'] ?>" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>