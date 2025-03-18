<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}

require '../config/db_connection.php';
require '../controller/recipe_controller.php';

$recipeController = new RecipeController($conn);

$title = isset($_POST['titleType']) ? htmlspecialchars(trim($_POST['titleType'])) : '';
$cuisine = $_POST['cuisineType'] ?? '';
$difficulty = $_POST['difficultyLevel'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipes = $recipeController->filterRecipes($title, $cuisine, $difficulty);
} else {
    $recipes = $recipeController->filterRecipes('', '', '');
}
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
                <button style="margin-left: 83px; width: 150px;" class=" btn general-button d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>

            <div class="collapse d-lg-block" id="filterCollapse">
                <div class="row pt-4 d-flex justify-content-center">
                    <div class="bg-white py-3 rounded-3 d-flex justify-content-center w-75" style="box-shadow: 0 0 3px grey;">
                        <form action="" method="POST" id="filterForm">

                            <div class="row">
                                <div class="fw-bold pb-2 fs-6">
                                    <i class="bi bi-funnel"></i> Filter
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label for="titleType" class="form-label">Title Name:
                                        <input type="text" id="titleType" name="titleType" class="form-control">
                                    </label>                               
                                </div>

                                <div class="col-12 col-sm-6 col-lg-4">
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

                                <div class="col-12 col-sm-6 col-lg-4">
                                    <label for="difficultyLevel" class="form-label">Difficulty:
                                        <select name="difficultyLevel" id="difficultyLevel" class="form-select">
                                            <option value="">-- Difficulty --</option>
                                            <option value="1">1<i class="bi bi-star-fill"></i></option>
                                            <option value="2">2<i class="bi bi-star-fill"></i></option>
                                            <option value="3">3<i class="bi bi-star-fill"></i></option>
                                            <option value="4">4<i class="bi bi-star-fill"></i></option>
                                            <option value="5">5<i class="bi bi-star-fill"></i></option>
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

            <h4 class="mb-3 py-2 text-center">Recipes</h4>
            <div class="container" id="data-container">
                <div class="row justify-content-center">
                    <?php if (empty($recipes)) : ?>
                        <div class="text-center mt-5">
                            <p>No recipes yet.</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($recipes as $recipe) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4 d-flex justify-content-center">
                            <div class="card shadow-sm border" style="width: 100%; max-width: 20rem; height: 100%;">
                                <img src="../uploads/<?php echo $recipe['images'] ?? 'default_recipe.png'; ?>" class="card-img-top rounded-top" alt="Recipe Image" style="width: 100%; height: 200px; object-fit: cover;">
                                <div class="card-body text-start p-3 d-flex flex-column justify-content-between" style=" flex-grow: 1;">
                                    <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                    <p class="card-text">Cuisine Type: <?php echo htmlspecialchars($recipe['cuisine']); ?><br> Difficulty: <?php echo htmlspecialchars($recipe['difficulty']); ?></p>
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