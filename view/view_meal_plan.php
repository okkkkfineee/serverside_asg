<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/meal_planning_controller.php';
require '../controller/recipe_controller.php';

$mealPlanningController = new MealPlanningController($conn);
$recipeController = new RecipeController($conn);

// Check if user has admin role
$isAdmin = isset($_SESSION['roles']) && in_array($_SESSION['roles'], ['Superadmin', 'Admin', 'Mod']);

// Get meal plan details
if (isset($_GET['plan_id'])) {
    $plan_id = $_GET['plan_id'];
    $plan = $mealPlanningController->getMealPlanById($plan_id);
    
    if ($plan) {
        // Get recipe details
        $recipe = $recipeController->getRecipeInfo($plan['recipe_id']);
        
        // Get ingredients for the recipe
        $ingredients = $recipeController->getIngredients($plan['recipe_id']);
        
        // Get steps for the recipe
        $steps = $recipeController->getSteps($plan['recipe_id']);
        
        // Check if user is authorized to view/edit this meal plan
        if (!$isAdmin && $plan['user_id'] != $_SESSION['user_id']) {
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
                $plan = $mealPlanningController->getMealPlanById($plan_id);
                $recipe = $recipeController->getRecipeInfo($plan['recipe_id']);
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Meal Plan Details</h5>
                        <div>
                            <?php if ($isAdmin || $plan['user_id'] == $_SESSION['user_id']): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                                Edit Plan
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete Plan
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Plan Name:</strong> <?php echo htmlspecialchars($plan['plan_name']); ?></p>
                                <p><strong>Meal Category:</strong> <?php echo htmlspecialchars($plan['meal_category']); ?></p>
                                <p><strong>Meal Time:</strong> <?php echo htmlspecialchars($plan['formatted_time']); ?></p>
                                <p><strong>Meal Date:</strong> <?php echo date('F j, Y', strtotime($plan['meal_date'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created:</strong> <?php echo date('F j, Y h:i A', strtotime($plan['created_date'])); ?></p>
                                <p><strong>Last Updated:</strong> <?php echo isset($plan['updated_at']) ? date('F j, Y h:i A', strtotime($plan['updated_at'])) : 'Not updated'; ?></p>
                                <?php if ($isAdmin): ?>
                                <p><strong>User:</strong> <?php echo htmlspecialchars($plan['username']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recipe Details</h5>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
                        
                        <div class="recipe-meta">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($recipe['cuisine']); ?></span>
                            <span class="badge bg-info">Difficulty: <?php echo str_repeat('★', $recipe['difficulty']); ?></span>
                            <span class="badge bg-secondary">Cooking Time: <?php echo $recipe['cooking_time']; ?> minutes</span>
                        </div>
                        <br>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Ingredients</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($ingredients && $ingredients->num_rows > 0): ?>
                                    <ul class="list-group">
                                        <?php while ($ingredient = $ingredients->fetch_assoc()): ?>
                                            <li class="list-group-item">
                                                <?php echo htmlspecialchars($ingredient['material']); ?>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">No ingredients listed.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Instructions</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($steps && $steps->num_rows > 0): ?>
                                    <ol class="list-group list-group-numbered">
                                        <?php while ($step = $steps->fetch_assoc()): ?>
                                            <li class="list-group-item">
                                                <?php echo htmlspecialchars($step['instruction']); ?>
                                            </li>
                                        <?php endwhile; ?>
                                    </ol>
                                <?php else: ?>
                                    <p class="text-muted">No instructions available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
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
                                <?php 
                                // Define time ranges for each category
                                $timeRanges = [
                                    'Breakfast' => ['start' => '05:00', 'end' => '11:30'],
                                    'Lunch' => ['start' => '11:30', 'end' => '16:30'],
                                    'Dinner' => ['start' => '17:00', 'end' => '22:30']
                                ];
                                
                                // Get the current category
                                $currentCategory = $plan['meal_category'];
                                
                                if ($currentCategory === 'Snacks') {
                                    // For Snacks, show all times in 30-minute intervals
                                    for($i = 0; $i < 24; $i++): 
                                        for($j = 0; $j < 60; $j += 30):
                                            $timeStr = sprintf("%02d:%02d", $i, $j);
                                            $period = $i >= 12 ? 'PM' : 'AM';
                                            $displayHour = $i % 12;
                                            $displayHour = $displayHour == 0 ? 12 : $displayHour;
                                            $displayTime = sprintf("%d:%02d %s", $displayHour, $j, $period);
                                            
                                            // Check if this is the selected time
                                            $isSelected = false;
                                            if (strpos($plan['formatted_time'], $timeStr) !== false) {
                                                $isSelected = true;
                                            }
                                            
                                            echo "<option value=\"$timeStr\" " . ($isSelected ? 'selected' : '') . ">$displayTime</option>";
                                        endfor;
                                    endfor;
                                } else if (isset($timeRanges[$currentCategory])) {
                                    // For other categories, use the defined time ranges
                                    $range = $timeRanges[$currentCategory];
                                    
                                    list($startHour, $startMin) = explode(':', $range['start']);
                                    list($endHour, $endMin) = explode(':', $range['end']);
                                    
                                    $startTime = ($startHour * 60) + $startMin;
                                    $endTime = ($endHour * 60) + $endMin;
                                    
                                    for ($time = $startTime; $time <= $endTime; $time += 30) {
                                        $hours = floor($time / 60);
                                        $minutes = $time % 60;
                                        $timeStr = sprintf("%02d:%02d", $hours, $minutes);
                                        
                                        // Format for display
                                        $displayHour = $hours % 12;
                                        $displayHour = $displayHour == 0 ? 12 : $displayHour;
                                        $period = $hours >= 12 ? 'PM' : 'AM';
                                        $displayTime = sprintf("%d:%02d %s", $displayHour, $minutes, $period);
                                        
                                        // Check if this is the selected time
                                        $isSelected = false;
                                        if (strpos($plan['formatted_time'], $timeStr) !== false) {
                                            $isSelected = true;
                                        }
                                        
                                        echo "<option value=\"$timeStr\" " . ($isSelected ? 'selected' : '') . ">$displayTime</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="meal_date" class="form-label">Meal Date</label>
                            <input type="date" class="form-control" id="meal_date" name="meal_date" value="<?php echo $plan['meal_date']; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                            <div id="dateError" class="invalid-feedback"></div>
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
    <script>
        // Function to update time options based on selected category
        function updateTimeBasedOnCategory() {
            const category = document.getElementById('meal_category').value;
            const timeSelect = document.getElementById('meal_time');
            
            // Define time ranges for each category
            const timeRanges = {
                'Breakfast': { start: '05:00', end: '11:30' },
                'Lunch': { start: '11:30', end: '16:30' },
                'Dinner': { start: '17:00', end: '22:30' }
            };
            
            // Clear existing options
            timeSelect.innerHTML = '';
            
            if (category === 'Snacks') {
                // For Snacks, show all times in 30-minute intervals
                for (let i = 0; i < 24; i++) {
                    for (let j = 0; j < 60; j += 30) {
                        const timeStr = `${String(i).padStart(2, '0')}:${String(j).padStart(2, '0')}`;
                        const displayHour = i % 12 || 12;
                        const period = i >= 12 ? 'PM' : 'AM';
                        const displayTime = `${displayHour}:${String(j).padStart(2, '0')} ${period}`;
                        
                        const option = document.createElement('option');
                        option.value = timeStr;
                        option.textContent = displayTime;
                        
                        // Check if this is the current selected time
                        const currentTime = '<?php echo $plan['formatted_time']; ?>';
                        if (currentTime.includes(timeStr)) {
                            option.selected = true;
                        }
                        
                        timeSelect.appendChild(option);
                    }
                }
            } else if (category && timeRanges[category]) {
                const range = timeRanges[category];
                
                // Parse start and end times
                const [startHour, startMin] = range.start.split(':').map(Number);
                const [endHour, endMin] = range.end.split(':').map(Number);
                
                // Convert to minutes for easier calculation
                const startTime = (startHour * 60) + startMin;
                const endTime = (endHour * 60) + endMin;
                
                // Generate options in 30-minute intervals
                for (let time = startTime; time <= endTime; time += 30) {
                    const hours = Math.floor(time / 60);
                    const minutes = time % 60;
                    const timeStr = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
                    
                    // Format for display
                    const displayHour = hours % 12 || 12;
                    const period = hours >= 12 ? 'PM' : 'AM';
                    const displayTime = `${displayHour}:${String(minutes).padStart(2, '0')} ${period}`;
                    
                    const option = document.createElement('option');
                    option.value = timeStr;
                    option.textContent = displayTime;
                    
                    // Check if this is the current selected time
                    const currentTime = '<?php echo $plan['formatted_time']; ?>';
                    if (currentTime.includes(timeStr)) {
                        option.selected = true;
                    }
                    
                    timeSelect.appendChild(option);
                }
            }
        }
        
        // Add event listener to update time options when category changes
        document.getElementById('meal_category').addEventListener('change', updateTimeBasedOnCategory);
        
        // Initialize time options on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateTimeBasedOnCategory();
        });
    </script>
</body>
</html>