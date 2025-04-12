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
            $meal_date = $_POST['meal_date'];
            
            $result = $mealPlanningController->updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $meal_time, $meal_date);
            
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
                                <p><strong>Meal Time:</strong> <?php 
                                    $hour = (int)$plan['meal_time'];
                                    $period = $hour >= 12 ? 'PM' : 'AM';
                                    $displayHour = $hour % 12;
                                    $displayHour = $displayHour == 0 ? 12 : $displayHour;
                                    echo $displayHour . ':00 ' . $period;
                                ?></p>
                                <p><strong>Meal Date:</strong> <?php echo date('F j, Y', strtotime($plan['meal_date'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created:</strong> <?php echo date('F j, Y h:i A', strtotime($plan['created_date'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo isset($plan['updated_at']) ? date('F j, Y h:i A', strtotime($plan['updated_at'])) : 'Not updated'; ?></p>
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
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="meal_planner.php" class="btn btn-secondary w-100 mb-2">Back to Meal Planner</a>
                        <a href="view_recipe.php?recipe_id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-info w-100">View Full Recipe</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Meal Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_meal_plan">
                        <input type="hidden" name="plan_id" value="<?php echo $plan['plan_id']; ?>">
                        
                        <div class="mb-3">
                            <label for="plan_name" class="form-label">Plan Name</label>
                            <input type="text" class="form-control" id="plan_name" name="plan_name" value="<?php echo htmlspecialchars($plan['plan_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="meal_category" class="form-label">Meal Category</label>
                            <select class="form-select" id="meal_category" name="meal_category" required>
                                <option value="Breakfast" <?php echo ($plan['meal_category'] == 'Breakfast') ? 'selected' : ''; ?>>Breakfast</option>
                                <option value="Lunch" <?php echo ($plan['meal_category'] == 'Lunch') ? 'selected' : ''; ?>>Lunch</option>
                                <option value="Dinner" <?php echo ($plan['meal_category'] == 'Dinner') ? 'selected' : ''; ?>>Dinner</option>
                                <option value="Snacks" <?php echo ($plan['meal_category'] == 'Snacks') ? 'selected' : ''; ?>>Snacks</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="meal_time" class="form-label">Meal Time</label>
                            <select class="form-select" id="meal_time" name="meal_time" required>
                                <?php for($i = 0; $i < 24; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($plan['meal_time'] == $i) ? 'selected' : ''; ?>>
                                        <?php 
                                            $period = $i >= 12 ? 'PM' : 'AM';
                                            $displayHour = $i % 12;
                                            $displayHour = $displayHour == 0 ? 12 : $displayHour;
                                            echo $displayHour . ':00 ' . $period;
                                        ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="meal_date" class="form-label">Meal Date</label>
                            <input type="date" class="form-control" id="meal_date" name="meal_date" value="<?php echo $plan['meal_date']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="recipe_id" class="form-label">Recipe</label>
                            <select class="form-select" id="recipe_id" name="recipe_id" required>
                                <?php foreach ($allRecipes as $r): ?>
                                    <option value="<?php echo $r['recipe_id']; ?>" <?php echo ($plan['recipe_id'] == $r['recipe_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($r['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Meal Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Meal Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this meal plan? This action cannot be undone.</p>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="delete_meal_plan">
                        <input type="hidden" name="plan_id" value="<?php echo $plan['plan_id']; ?>">
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete Meal Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>