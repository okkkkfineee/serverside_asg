<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/meal_planning_controller.php';
require '../controller/recipe_controller.php';

$mealPlanningController = new MealPlanningController($conn);
$recipeController = new RecipeController($conn);

// Get all recipes for the dropdown
$allRecipes = $recipeController->getAllRecipes();

// Get user's meal plans
$userMealPlans = $mealPlanningController->getUserMealPlans($_SESSION['user_id']);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_meal_plan') {
            $recipe_id = $_POST['recipe_id'];
            $user_id = $_SESSION['user_id'];
            $plan_name = $_POST['plan_name'];
            $meal_category = $_POST['meal_category'];
            $meal_time = $_POST['meal_time'];
            $meal_date = $_POST['meal_date'];
            
            $result = $mealPlanningController->createMealPlan($recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date);
            
            if ($result) {
                $success_message = "Meal plan added successfully!";
                // Refresh meal plans
                $userMealPlans = $mealPlanningController->getUserMealPlans($_SESSION['user_id']);
            } else {
                $error_message = "Failed to add meal plan. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Planner</title>
    <link rel="icon" href="../assets/images/icon.png">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/meal_planner.css">  <!-- External meal planner styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            // Prepare events data from PHP
            var events = [];
            <?php if ($userMealPlans && $userMealPlans->num_rows > 0): ?>
                <?php while ($plan = $userMealPlans->fetch_assoc()): ?>
                    events.push({
                        id: '<?php echo $plan['plan_id']; ?>',
                        title: '<?php echo htmlspecialchars($plan['plan_name']); ?>',
                        start: '<?php echo $plan['meal_date']; ?>',
                        extendedProps: {
                            plan_id: '<?php echo $plan['plan_id']; ?>',
                            meal_category: '<?php echo htmlspecialchars($plan['meal_category']); ?>',
                            meal_time: '<?php echo $plan['meal_time']; ?>'
                        }
                    });
                <?php endwhile; ?>
            <?php endif; ?>
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                events: events,
                eventClick: function(info) {
                    // Navigate to meal plan details
                    window.location.href = 'view_meal_plan.php?plan_id=' + info.event.extendedProps.plan_id;
                },
                select: function(info) {
                    // Set the selected date in the meal date field
                    document.getElementById('meal_date').value = info.startStr;
                    var modal = new bootstrap.Modal(document.getElementById('mealModal'));
                    modal.show();
                }
            });
            calendar.render();
            
            // Toggle between existing recipe and new recipe
            document.getElementById('mealSource').addEventListener('change', function() {
                var existingRecipeSection = document.getElementById('existingRecipeSection');
                var newRecipeSection = document.getElementById('newRecipeSection');
                
                if (this.value === 'existing') {
                    existingRecipeSection.style.display = 'block';
                    newRecipeSection.style.display = 'none';
                } else {
                    existingRecipeSection.style.display = 'none';
                    newRecipeSection.style.display = 'block';
                }
            });
        });
    </script>
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
        
        <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
            <div class="alert alert-success">Meal plan deleted successfully!</div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                <h4>Meal Categories</h4>
                <div class="meal-category">Breakfast</div>
                <div class="meal-category">Lunch</div>
                <div class="meal-category">Dinner</div>
                <div class="meal-category">Snacks</div>
                
                <h4 class="mt-4">Your Meal Plans</h4>
                <div class="meal-plans-list">
                    <?php 
                    // Reset the pointer to the beginning of the result set
                    if ($userMealPlans) {
                        $userMealPlans->data_seek(0);
                        
                        if ($userMealPlans->num_rows > 0): 
                            while ($plan = $userMealPlans->fetch_assoc()): 
                    ?>
                        <div class="meal-plan-item">
                            <a href="view_meal_plan.php?plan_id=<?php echo $plan['plan_id']; ?>" class="text-decoration-none text-dark">
                                <strong><?php echo htmlspecialchars($plan['plan_name']); ?></strong><br>
                                <small>Category: <?php echo htmlspecialchars($plan['meal_category']); ?><br>
                                Time: <?php echo $plan['meal_time']; ?>:00</small>
                            </a>
                        </div>
                    <?php 
                            endwhile; 
                        else: 
                    ?>
                        <p>No meal plans yet. Create a new meal plan to get started.</p>
                    <?php 
                        endif; 
                    } else {
                    ?>
                        <p>Error loading meal plans. Please try again later.</p>
                    <?php } ?>
                </div>
            </div>

            <!-- Calendar -->
            <div class="col-md-9">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Meal Entry Modal -->
    <div class="modal fade" id="mealModal" tabindex="-1" aria-labelledby="mealModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mealModalLabel">Add Meal Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add_meal_plan">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="meal_category" class="form-label">Meal Category</label>
                                <select class="form-select" id="meal_category" name="meal_category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="Breakfast">Breakfast</option>
                                    <option value="Lunch">Lunch</option>
                                    <option value="Dinner">Dinner</option>
                                    <option value="Snacks">Snacks</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="meal_time" class="form-label">Meal Time (Hour)</label>
                                <select class="form-select" id="meal_time" name="meal_time" required>
                                    <option value="">-- Select Hour --</option>
                                    <?php for($i = 0; $i < 24; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?>:00</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meal_date" class="form-label">Meal Date</label>
                            <input type="date" class="form-control" id="meal_date" name="meal_date" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="plan_name" class="form-label">Plan Name</label>
                            <input type="text" class="form-control" id="plan_name" name="plan_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mealSource" class="form-label">Meal Source</label>
                            <select class="form-select" id="mealSource" name="meal_source">
                                <option value="existing">Use Existing Recipe</option>
                                <option value="new">Create New Recipe</option>
                            </select>
                        </div>
                        
                        <!-- Existing Recipe Section -->
                        <div id="existingRecipeSection" class="mb-3">
                            <label for="recipe_id" class="form-label">Select Recipe</label>
                            <select class="form-select" id="recipe_id" name="recipe_id">
                                <option value="">-- Select a Recipe --</option>
                                <?php foreach ($allRecipes as $recipe): ?>
                                    <option value="<?php echo $recipe['recipe_id']; ?>">
                                        <?php echo htmlspecialchars($recipe['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- New Recipe Section (initially hidden) -->
                        <div id="newRecipeSection" class="mb-3" style="display: none;">
                            <div class="alert alert-info">
                                <p>You'll be redirected to create a new recipe. After creating the recipe, you can come back to the meal planner to add it to your plan.</p>
                                <a href="manage_recipe.php?type=add" class="btn btn-primary">Create New Recipe</a>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Meal Plan</button>
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
