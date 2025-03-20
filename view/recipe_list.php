<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}

require '../config/db_connection.php';
require '../controller/recipe_controller.php';

$recipeController = new RecipeController($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['titleType']) ? htmlspecialchars(trim($_POST['titleType'])) : '';
    $cuisine = $_POST['cuisineType'] ?? '';
    $difficulty = $_POST['difficultyLevel'] ?? '';

    $_SESSION['titleType'] = $title;
    $_SESSION['cuisineType'] = $cuisine;
    $_SESSION['difficultyLevel'] = $difficulty;

    header("Location: recipe_list.php");
    exit();
}

$title = $_SESSION['titleType'] ?? '';
$cuisine = $_SESSION['cuisineType'] ?? '';
$difficulty = $_SESSION['difficultyLevel'] ?? '';

$recipes = $recipeController->filterRecipes($title, $cuisine, $difficulty);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Recipes</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="row">
        <div class="container-fluid">

            <!-- Filter Section -->
            <div class="row mt-2">
                <button style="margin-left: 83px; width: 150px;" class="d-lg-none btn general-button" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>

            <div class="collapse d-lg-block" id="filterCollapse">
                <div class="d-flex row justify-content-center pt-4">
                    <div class="d-flex bg-white justify-content-center rounded-3 w-75 py-3" style="box-shadow: 0 0 3px grey;">
                        <form action="" method="POST" id="filterForm">

                            <div class="row">
                                <div class="fs-6 fw-bold pb-2">
                                    <i class="bi bi-funnel"></i> Filter
                                </div>

                                <div class="col-12 col-lg-4 col-sm-6">
                                    <label for="titleType" class="form-label">Title Name:
                                        <input type="text" id="titleType" name="titleType" class="form-control">
                                    </label>                               
                                </div>

                                <div class="col-12 col-lg-4 col-sm-6">
                                    <label for="cuisineType" class="form-label">Cuisine Type:
                                        <select name="cuisineType" id="cuisineType" class="form-select">
                                            <option value="">-- Cuisine Type --</option>
                                            <option value="Chinese">Chinese</option>
                                            <option value="Indian">Indian</option>
                                            <option value="Japanese">Japanese</option>
                                            <option value="Malay">Malay</option>
                                            <option value="Thai">Thai</option>
                                            <option value="Western">Western</option>
                                            <option value="Thai">Other</option>
                                        </select>
                                    </label>
                                </div>

                                <div class="col-12 col-lg-4 col-sm-6">
                                    <label for="difficultyLevel" class="form-label">Difficulty:
                                        <select name="difficultyLevel" id="difficultyLevel" class="form-select">
                                            <option value="">-- Difficulty --</option>
                                            <option value="1">1 -- Beginner-Friendly<i class="bi bi-star-fill"></i></option>
                                            <option value="2">2 -- Easy<i class="bi bi-star-fill"></i></option>
                                            <option value="3">3 -- Moderate<i class="bi bi-star-fill"></i></option>
                                            <option value="4">4 -- Challenging<i class="bi bi-star-fill"></i></option>
                                            <option value="5">5 -- Expert-Level<i class="bi bi-star-fill"></i></option>
                                        </select>
                                    </label>                             
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-12 filter-button">
                                    <button type="reset" class="btn general-button" style="background-color: red !important;" id="filter-clear">Clear</button>
                                    <button type="submit" class="btn general-button" style="background-color: #1448f5 !important;" value="apply" name="action">Apply</button>
                                </div>
                            </div>
                        </form>    
                    </div>
                </div>
            </div>
            <br>

            
            <div class="container" id="data-container">
                <div class="d-flex justify-content-between position-relative mb-3 title-container">
                    <h4 class="position-absolute py-2 start-50 translate-middle-x">Recipes</h4>
                    <form method="POST" action="" class="ms-auto">
                        <button type="submit" name="view_all"   class="btn btn-secondary">View All Recipes</button>
                    </form>
                </div>
                <div class="row justify-content-center">
                    <?php if (empty($recipes)) : ?>
                        <div class="text-center mt-5">
                            <p>No recipes yet.</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($recipes as $recipe) : ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xl-3 d-flex justify-content-center mb-4">
                            <div class="card border shadow-sm" style="width: 100%; max-width: 20rem; height: 100%;">
                                <img src="../uploads/<?php echo $recipe['images'] ?? 'default_recipe.png'; ?>" class="card-img-top rounded-top" alt="Recipe Image" style="width: 100%; height: 200px; object-fit: cover;">
                                <div class="d-flex flex-column card-body justify-content-between p-3 text-start" style=" flex-grow: 1;">
                                    <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                    <p class="card-text">Cuisine Type: <?php echo htmlspecialchars($recipe['cuisine']); ?><br> Difficulty: 
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
                                    <a href="view_recipe?recipe_id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-primary mt-auto">View    Recipe</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div id="pagination-container">

            </div>
        </div>
    </div>
    <script src="../assets/js/recipe_list.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>