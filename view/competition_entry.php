<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/recipe_controller.php';
require '../controller/comp_controller.php';

$recipeController = new RecipeController($conn);
$compController = new CompetitionController($conn);

$comp = $compController->getComp($_GET['comp_id']);
$checkEntry = $compController->checkEntry($_GET['comp_id'], $_SESSION['user_id']);
$recipes = $recipeController->getMatchedRecipes($comp['comp_theme']);

if (strtotime($comp['end_date']) < time()) {
    echo "<script>alert('This competition is no longer accepting entries. \\nThank you for your interest!');
        window.history.back();</script>";
    exit();
} else if ($checkEntry) {
    echo "<script>alert('You have already entered this competition.');
        window.history.back();</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_recipe'])) {
    $selected_recipe_id = (int) $_POST['selected_recipe'];
    $res = $compController->submitEntry($_GET['comp_id'], $_SESSION['user_id'], $selected_recipe_id);
    if ($res) {
        echo "<script>alert('You have successfully entered this competition.');
        window.history.back();</script>";
        exit();
    } else {
        echo "<script>alert('An error occurred while submitting your entry.');
        window.history.back();</script>";
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition #<?php echo $_GET['comp_id']; ?> Entry </title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #comp-image-section {
            background-color: black; 
            height: 300px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .info-box {
            border-radius: 10px;
            border: 1px solid white; 
            margin: 20px;       
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div id="comp-image-section">
        <img src="<?= (!empty($comp['comp_image']) ? '../uploads/comp/' . $comp['comp_image'] : '../assets/images/default_comp.png'); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Competition Image">
    </div>
    
    <div class="container mb-5 mt-3">
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h1><?php echo $comp['comp_title']; ?></h1>
                <p><?php echo $comp['comp_desc']; ?></p>
            </div>
        </div>

        <form action="competition_entry?comp_id=<?php echo $_GET['comp_id']; ?>" method="post">
            <div class="content-section">
                <div class="container">
                    <div class="row row-cols-4">
                        <?php if (empty($recipes)) : ?>
                            <div class="col-12 text-center">
                                <p class="lead">You have no recipes that fit the theme.</p>
                                <a href="view_comp?comp_id=<?php echo $_GET['comp_id']?>#entries" class="btn btn-primary">Back</a>
                            </div>
                        <?php else : ?>
                            <?php foreach ($recipes as $i => $recipe) : ?>
                                <div class="col">
                                    <label class="card border shadow-sm w-100" style="cursor: pointer;">
                                        <input type="radio" name="selected_recipe" value="<?= $recipe['recipe_id'] ?>" class="form-check-input m-2" required>
                                        <img src="<?= (!empty($recipe['images']) ? '../uploads/recipes/' . $recipe['images'] : '../assets/images/default_recipe.png'); ?>"
                                            class="card-img-top" alt="Recipe Image" 
                                            style="height: 200px; object-fit: cover;">
                                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start">
                                            <h5 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h5>
                                            <p class="card-text"><?= htmlspecialchars(substr($recipe['description'], 0, 50)) . '...' ?></p>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($recipes)) : ?>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">Submit Selected Recipe</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
        

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>