<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/user_controller.php';
require '../controller/recipe_controller.php';

$userController = new UserController($conn);
$recipeController = new RecipeController($conn);

$type = isset($_GET['type']) ? $_GET['type'] : "add";

if ($type === "edit" && isset($_GET['recipe_id'])) {
    $recipe_id = $_GET['recipe_id'];
    $recipe_info = $recipeController->getOwnRecipeInfo($recipe_id, $_SESSION['user_id']);
}

if ($type === "delete" && isset($_GET['recipe_id'])) {
    $recipe_id = $_GET['recipe_id'];
    $result = $recipeController->deleteRecipe($recipe_id);
    if ($result === true) {
        echo "<script>alert('Recipe deleted successfully!');
        window.location.href = 'profile';</script>";
    } else {
        $error = $result;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $difficulty = mysqli_real_escape_string($conn, $_POST['difficulty']);
    $cooking_time = mysqli_real_escape_string($conn, $_POST['cooking_time']);
    $ingredients = array_map(function($ingredient) use ($conn) {
        return mysqli_real_escape_string($conn, $ingredient);
    }, $_POST['ingredients']);
    $steps = array_map(function($step) use ($conn) {
        return mysqli_real_escape_string($conn, $step);
    }, $_POST['steps']);

    $image = $_FILES['image'] ?? null;

    if (isset($_POST['action']) && $_POST['action'] === 'Add') {
        $created_time = date("Y-m-d H:i:s");
        
        $result = $recipeController->manageRecipe("add", "", $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $ingredients, $steps,  $created_time);
        if ($result === true) {
            echo "<script>alert('Recipe added successfully!');
            window.location.href = 'profile';</script>";
        } else {
            $error = $result;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'Edit') {
        $result = $recipeController->manageRecipe("update", $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $ingredients, $steps, "");
        if ($result === true) {
            echo "<script>alert('Recipe updated successfully!');
            window.location.href = 'profile';</script>";
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Recipe</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($type === "add"): ?>
            <h2 class="text-center mb-3">Add New Recipe</h2>
            <form action="manage_recipe?type=add" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Image (JPG, JPEG, PNG only)</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description (Max 50 words)</label>
                    <textarea name="description" class="form-control" rows="3" maxlength="250" oninput="limitWords(this, 50)" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Cuisine</label>
                        <select name="cuisine" class="form-select" required>
                            <option value="">-- Choose a Cuisine --</option>
                            <option value="Chinese">Chinese</option>
                            <option value="Indian">Indian</option>
                            <option value="Japanese">Japanese</option>
                            <option value="Malay">Malay</option>
                            <option value="Thai">Thai</option>
                            <option value="Western">Western</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-select" required>
                            <option value="">-- Select Difficulty --</option>
                            <option value="1">1 -- Beginner-Friendly</option>
                            <option value="2">2 -- Easy</option>
                            <option value="3">3 -- Moderate</option>
                            <option value="4">4 -- Challenging</option>
                            <option value="5">5 -- Expert-Level</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cooking Time (minutes)</label>
                        <input type="number" name="cooking_time" class="form-control" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                <label class="form-label">Ingredients</label>
                <div id="ingredientsContainer">
                    <div class="d-flex align-items-center mb-2">
                        <input type="text" name="ingredients[]" class="form-control" placeholder="Ingredient 1" required>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" onclick="addIngredient()">Add Ingredient</button>
            </div>

                <div class="mb-3">
                    <label class="form-label">Steps</label>
                    <div id="stepsContainer">
                        <div class="d-flex align-items-center mb-2">
                            <input type="text" name="steps[]" class="form-control" placeholder="Step 1" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" onclick="addStep()">Add Step</button>
                </div>

                <button type="submit" name="action" value="Add" class="btn btn-primary">Submit Recipe</button>
            </form>
        <?php elseif ($type === "edit"): ?>
            <h2 class="text-center mb-3">Edit Recipe</h2>
            <form action="manage_recipe?type=edit&recipe_id=<?= $recipe_id ?>" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="hidden" name="recipe_id" value="<?= $recipe_id ?>">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($recipe_info['title']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload New Image (JPG, JPEG, PNG only)</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                        <p>Current Image File: <strong><?= htmlspecialchars($recipe_info['images']) ?></strong></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description (Max 50 words)</label>
                    <textarea name="description" class="form-control" rows="3" maxlength="250" oninput="limitWords(this, 50)" required><?= htmlspecialchars($recipe_info['description']) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Cuisine</label>
                        <select name="cuisine" class="form-select" required>
                            <option value="Chinese" <?= ($recipe_info['cuisine'] == 'Chinese') ? 'selected' : '' ?>>Chinese</option>
                            <option value="Indian" <?= ($recipe_info['cuisine'] == 'Indian') ? 'selected' : '' ?>>Indian</option>
                            <option value="Japanese" <?= ($recipe_info['cuisine'] == 'Japanese') ? 'selected' : '' ?>>Japanese</option>
                            <option value="Malay" <?= ($recipe_info['cuisine'] == 'Malay') ? 'selected' : '' ?>>Malay</option>
                            <option value="Thai" <?= ($recipe_info['cuisine'] == 'Thai') ? 'selected' : '' ?>>Thai</option>
                            <option value="Western" <?= ($recipe_info['cuisine'] == 'Western') ? 'selected' : '' ?>>Western</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-select" required>
                            <option value="1" <?= ($recipe_info['difficulty'] == '1') ? 'selected' : '' ?>>1 -- Beginner-Friendly</option>
                            <option value="2" <?= ($recipe_info['difficulty'] == '2') ? 'selected' : '' ?>>2 -- Easy</option>
                            <option value="3" <?= ($recipe_info['difficulty'] == '3') ? 'selected' : '' ?>>3 -- Moderate</option>
                            <option value="4" <?= ($recipe_info['difficulty'] == '4') ? 'selected' : '' ?>>4 -- Challenging</option>
                            <option value="5" <?= ($recipe_info['difficulty'] == '5') ? 'selected' : '' ?>>5 -- Expert-Level</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cooking Time (minutes)</label>
                        <input type="number" name="cooking_time" class="form-control" min="1" value="<?= $recipe_info['cooking_time'] ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ingredients</label>
                    <div id="ingredientsContainer">
                        <?php foreach ($recipe_info['ingredients'] as $index => $ingredient): ?>
                            <div class="d-flex align-items-center mb-2">
                                <input type="text" name="ingredients[]" class="form-control" value="<?= htmlspecialchars($ingredient) ?>" required>
                                <?php if ($index > 0): ?>
                                    <button type="button" class="btn btn-danger ms-2" onclick="removeIngredient(this)">X</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" onclick="addIngredient()">Add Ingredient</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Steps</label>
                    <div id="stepsContainer">
                        <?php foreach ($recipe_info['steps'] as $index => $step): ?>
                            <div class="d-flex align-items-center mb-2">
                                <input type="text" name="steps[]" class="form-control" value="<?= htmlspecialchars($step) ?>" required>
                                <?php if ($index > 0): ?>
                                    <button type="button" class="btn btn-danger ms-2" onclick="removeStep(this)">X</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" onclick="addStep()">Add Step</button>
                </div>
                
                <div class="button-container">
                <button type="submit" name="action" value="Edit" class="btn btn-primary">Update Changes</button>
                <a href="profile.php#" class="btn btn-secondary">Discard Changes</a>
            </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function addIngredient() {
            const ingredientsContainer = document.getElementById('ingredientsContainer');
            const ingredientsCount = ingredientsContainer.getElementsByTagName('input').length + 1;
            
            const newIngredient = document.createElement('div');
            newIngredient.classList.add('mb-2', 'd-flex', 'align-items-center');
            newIngredient.innerHTML = `
                <input type="text" name="ingredients[]" class="form-control" placeholder="Ingredient ${ingredientsCount}" required>
                <button type="button" class="btn btn-danger ms-2" onclick="removeIngredient(this)">X</button>
            `;
            ingredientsContainer.appendChild(newIngredient);
        }
        
        function addStep() {
            const stepContainer = document.getElementById('stepsContainer');
            const stepCount = stepContainer.getElementsByTagName('input').length + 1;
            
            const newStep = document.createElement('div');
            newStep.classList.add('mb-2', 'd-flex', 'align-items-center');
            newStep.innerHTML = `
                <input type="text" name="steps[]" class="form-control" placeholder="Step ${stepCount}" required>
                <button type="button" class="btn btn-danger ms-2" onclick="removeStep(this)">X</button>
            `;
            stepContainer.appendChild(newStep);
        }

        function removeStep(button) {
            button.parentElement.remove();
        }


        function removeIngredient(button) {
            button.parentElement.remove();
        }

        function limitWords(input, maxWords) {
            const words = input.value.split(/\s+/).filter(word => word.length > 0);
            if (words.length > maxWords) {
                input.value = words.slice(0, maxWords).join(' ');
            }
        }
        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>