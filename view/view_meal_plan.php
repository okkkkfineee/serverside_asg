<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/meal_planning_controller.php';
require '../controller/recipe_controller.php';

$mealPlanningController = new MealPlanningController($conn);
$recipeController = new RecipeController($conn);

// Get meal plan details
if (isset($_GET['plan_id'])) {
    $plan_id = $_GET['plan_id'];
    $mealPlan = $mealPlanningController->getMealPlanById($plan_id);
    
    if ($mealPlan && $mealPlan->num_rows > 0) {
        $plan = $mealPlan->fetch_assoc();
        
        // Get recipe details
        $recipe = $recipeController->getRecipe($plan['recipe_id']);
        
        // Check if user is authorized to view/edit this meal plan
        if ($plan['user_id'] != $_SESSION['user_id']) {
            header("Location: meal_planner.php");
            exit();
        }
    } else {
        header("Location: meal_planner.php");
        exit();
    }
} else {
    header("Location: meal_planner.php");
    exit();
}

// Handle form submission for updating meal plan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_meal_plan') {
            $plan_id = $_POST['plan_id'];
            $recipe_id = $_POST['recipe_id'];
            $plan_name = $_POST['plan_name'];
            $meal_category = $_POST['meal_category'];
            $meal_time = $_POST['meal_time'];
            
            $result = $mealPlanningController->updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $meal_time);
            
            if ($result) {
                $success_message = "Meal plan updated successfully!";
                // Refresh meal plan data
                $mealPlan = $mealPlanningController->getMealPlanById($plan_id);
                $plan = $mealPlan->fetch_assoc();
                $recipe = $recipeController->getRecipe($plan['recipe_id']);
            } else {
                $error_message = "Failed to update meal plan. Please try again.";
            }
        } elseif ($_POST['action'] === 'delete_meal_plan') {
            $plan_id = $_POST['plan_id'];
            
            $result = $mealPlanningController->deleteMealPlan($plan_id);
            
            if ($result) {
                header("Location: meal_planner.php?deleted=1");
                exit();
            } else {
                $error_message = "Failed to delete meal plan. Please try again.";
            }
        }
    }
}

// Get all recipes for the dropdown
$allRecipes = $recipeController->getAllRecipes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Meal Plan</title>
    <link rel="icon" href="../assets/images/icon.png">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/meal_planner.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Meal Plan Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Plan Name:</strong> <?php echo htmlspecialchars($plan['plan_name']); ?></p>
                                <p><strong>Meal Category:</strong> <?php echo htmlspecialchars($plan['meal_category']); ?></p>
                                <p><strong>Meal Time:</strong> <?php echo htmlspecialchars($plan['meal_time']); ?>:00</p>
                                <p><strong>Meal Date:</strong> <?php echo date('F j, Y', strtotime($plan['meal_date'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($plan['created_date'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo isset($plan['updated_at']) ? date('F j, Y', strtotime($plan['updated_at'])) : 'Not updated'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Recipe</h4>
                        <div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                                Edit Plan
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete Plan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text">
                            <?php echo nl2br(htmlspecialchars($recipe['description'])); ?>
                        </p>
                        
                        <h5 class="mt-4">Ingredients</h5>
                        <ul>
                            <?php foreach ($recipe['ingredients'] as $ingredient): ?>
                                <li><?php echo htmlspecialchars($ingredient); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <h5 class="mt-4">Instructions</h5>
                        <ol>
                            <?php foreach ($recipe['steps'] as $step): ?>
                                <li><?php echo htmlspecialchars($step); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class