<?php

require '../includes/auth.php';
require '../config/db_connection.php';
include '../controller/recipe_controller.php';
 
$recipeController = new RecipeController($conn);

if (!isset($_GET['recipe_id']) || empty($_GET['recipe_id'])) {
    die("Recipe ID not provided!");
}

$recipe_id = intval($_GET['recipe_id']);
$user_id = $_SESSION['user_id'];
$recipe = $recipeController->getOwnRecipeInfo($recipe_id, $user_id);

if (!$recipe) {
    die("Recipe not found!");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>recipe_name</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <div class="p-4">
            <div class="d-flex align-items-start justify-content-between gap-4">
                <div>
                    <h1 class="mb-3"><?php echo htmlspecialchars($recipe['title']); ?></h1>
                    <p class="lead">
                        <?php echo nl2br(htmlspecialchars($recipe['description'])); ?>
                    </p>
                    <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine']); ?></p>
                    <p><strong>Diffivulty Level:</strong>
                    <?php 
                    $difficulty_labels = [
                    1 => "Beginner-Friendly",
                    2 => "Easy ",
                    3 => "Moderate ",
                    4 => "Challenging",
                    5 => "Expert-Level"
                    ];
                    $difficulty = htmlspecialchars($recipe['difficulty']);
                    echo isset($difficulty_labels[$difficulty]) ? $difficulty_labels[$difficulty] : "-";?></p>
                    <p><strong>Cooking Time:</strong> 
                    <?php
                    $cookingTime = (int) $recipe['cooking_time'];

                    if ($cookingTime >= 60) {
                        $hours = floor($cookingTime / 60); 
                        $minutes = $cookingTime % 60; 
                    
                        if ($minutes > 0) {
                            $formattedTime = "{$hours} hours {$minutes} minutes";
                        } else {
                            $formattedTime = "{$hours} hours";
                        }
                    } else {
                        $formattedTime = "{$cookingTime} minutes";
                    }
                    echo htmlspecialchars($formattedTime); ?></p>
                </div>
                <?php if (!empty($recipe['images'])): ?>
                    <div class="ms-3">
                        <img src="../uploads/recipes/<?php echo htmlspecialchars($recipe['images']); ?>" 
                             alt="<?php echo htmlspecialchars($recipe['title']); ?>" 
                             class="rounded shadow-sm img-fluid" style="width: 200px; height: auto; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="p-3 mt-4" style="background-color: grey; color: white; border-radius: 10px;">
                <h3>Ingredients:</h3>
                <ul>
                    <?php foreach ($recipe['ingredients'] as $ingredient): ?>
                        <li><?php echo htmlspecialchars($ingredient); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="p-3 mt-4" style="background-color: grey; color: white; border-radius: 10px;">
                <h3>Instructions:</h3>
                <ol>
                    <?php foreach ($recipe['steps'] as $step): ?>
                        <li><?php echo htmlspecialchars($step); ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>

            <div class="text-center mt-4" >
                <a href="profile.php" class="btn btn-success">Back</a>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>