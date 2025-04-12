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

// Get selected category filter
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'All';

// Filter meal plans by category if a specific category is selected
$filteredMealPlans = null;
if ($selectedCategory !== 'All') {
    $filteredMealPlans = $mealPlanningController->getMealPlansByCategory($_SESSION['user_id'], $selectedCategory);
} else {
    $filteredMealPlans = $userMealPlans;
}

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
                // Store success message in session
                $_SESSION['success_message'] = "Meal plan added successfully!";
                // Redirect to GET request
                header("Location: meal_planner.php");
                exit();
            } else {
                $error_message = "Failed to add meal plan. Please try again.";
            }
        } elseif ($_POST['action'] === 'delete_multiple') {
            if (isset($_POST['plan_ids'])) {
                $plan_ids = json_decode($_POST['plan_ids']);
                $success = true;
                
                foreach ($plan_ids as $plan_id) {
                    $result = $mealPlanningController->deleteMealPlan($plan_id);
                    if (!$result) {
                        $success = false;
                        break;
                    }
                }
                
                if ($success) {
                    $_SESSION['success_message'] = count($plan_ids) . " meal plan(s) deleted successfully!";
                } else {
                    $_SESSION['error_message'] = "Failed to delete some meal plans. Please try again.";
                }
                
                header("Location: meal_planner.php");
                exit();
            }
        }
    }
}

// Get success/error messages from session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
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
    <link rel="stylesheet" href="../assets/css/meal_planner.css?v=<?php echo time(); ?>">  <!-- Force reload CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendar = null;

            function initializeCalendar() {
                var calendarEl = document.getElementById('calendar');
                
                // Prepare events data from PHP
                var events = [];
                <?php if ($filteredMealPlans && $filteredMealPlans->num_rows > 0): ?>
                    <?php 
                    $filteredMealPlans->data_seek(0);
                    while ($plan = $filteredMealPlans->fetch_assoc()): 
                    ?>
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
                
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    selectable: true,
                    events: events,
                    eventClick: function(info) {
                        window.location.href = 'view_meal_plan.php?plan_id=' + info.event.extendedProps.plan_id;
                    },
                    select: function(info) {
                        document.getElementById('meal_date').value = info.startStr;
                        var modal = new bootstrap.Modal(document.getElementById('mealModal'));
                        modal.show();
                    }
                });
                calendar.render();
            }

            // Initialize calendar on page load
            initializeCalendar();

            // Handle form submission
            document.querySelector('form[action=""]').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('meal_planner.php', {
                    method: 'POST',
                    body: formData,
                    redirect: 'follow' // Follow redirects
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text().then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const errorMessage = doc.querySelector('.alert-danger');
                            if (errorMessage) {
                                alert(errorMessage.textContent);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the meal plan.');
                });
            });

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

        <!-- Multi-delete action bar -->
        <div id="deleteActionBar" class="action-bar" style="display: none;">
            <div class="action-bar-content">
                <span id="selectedCount">0 items selected</span>
                <button class="btn btn-danger" onclick="deleteSelectedPlans()">Delete Selected</button>
            </div>
        </div>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Meal Categories</h4>
                </div>
                <nav class="meal-categories">
                    <a href="meal_planner.php?category=All" class="meal-category <?php echo $selectedCategory === 'All' ? 'active' : ''; ?>">All</a>
                    <a href="meal_planner.php?category=Breakfast" class="meal-category <?php echo $selectedCategory === 'Breakfast' ? 'active' : ''; ?>">Breakfast</a>
                    <a href="meal_planner.php?category=Lunch" class="meal-category <?php echo $selectedCategory === 'Lunch' ? 'active' : ''; ?>">Lunch</a>
                    <a href="meal_planner.php?category=Dinner" class="meal-category <?php echo $selectedCategory === 'Dinner' ? 'active' : ''; ?>">Dinner</a>
                    <a href="meal_planner.php?category=Snacks" class="meal-category <?php echo $selectedCategory === 'Snacks' ? 'active' : ''; ?>">Snacks</a>
                </nav>
                
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h4 class="mb-0">Your Meal Plans</h4>
                    <button id="toggleDelete" class="btn btn-outline-danger btn-sm" onclick="toggleDeleteMode()">
                        <img src="../uploads/meal_planner/trash.png" alt="Delete" style="width: 20px; height: 20px;">
                    </button>
                </div>
                <div class="meal-plans-list">
                    <?php 
                    if ($filteredMealPlans) {
                        $filteredMealPlans->data_seek(0);
                        
                        if ($filteredMealPlans->num_rows > 0): 
                            while ($plan = $filteredMealPlans->fetch_assoc()): 
                    ?>
                        <div class="meal-plan-item">
                            <div class="d-flex align-items-center">
                                <div class="meal-plan-checkbox" style="display: none;">
                                    <input type="checkbox" class="form-check-input me-2" value="<?php echo $plan['plan_id']; ?>">
                                </div>
                                <a href="view_meal_plan.php?plan_id=<?php echo $plan['plan_id']; ?>" class="text-decoration-none text-dark flex-grow-1">
                                    <strong><?php echo htmlspecialchars($plan['plan_name']); ?></strong><br>
                                    <small>Category: <?php echo htmlspecialchars($plan['meal_category']); ?><br>
                                    Time: <?php 
                                        $hour = (int)$plan['meal_time'];
                                        $period = $hour >= 12 ? 'PM' : 'AM';
                                        $displayHour = $hour % 12;
                                        $displayHour = $displayHour == 0 ? 12 : $displayHour;
                                        echo $displayHour . ':00 ' . $period;
                                    ?><br>
                                    Date: <?php echo date('F j, Y', strtotime($plan['meal_date'])); ?></small>
                                </a>
                            </div>
                        </div>
                    <?php 
                            endwhile; 
                        else: 
                    ?>
                        <p>No meal plans found for this category.</p>
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
                                        <option value="<?php echo $i; ?>">
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
    <script>
        let deleteMode = false;

        function toggleDeleteMode() {
            deleteMode = !deleteMode;
            const checkboxes = document.querySelectorAll('.meal-plan-checkbox');
            const actionBar = document.getElementById('deleteActionBar');
            const mealPlanItems = document.querySelectorAll('.meal-plan-item');
            
            checkboxes.forEach(checkbox => {
                checkbox.style.display = deleteMode ? 'block' : 'none';
            });
            
            mealPlanItems.forEach(item => {
                item.classList.toggle('delete-mode');
            });
            
            if (deleteMode) {
                actionBar.style.display = 'block';
                setTimeout(() => actionBar.classList.add('show'), 10);
            } else {
                actionBar.classList.remove('show');
                setTimeout(() => actionBar.style.display = 'none', 300);
                // Uncheck all checkboxes
                document.querySelectorAll('.meal-plan-checkbox input').forEach(cb => cb.checked = false);
                updateSelectedCount();
            }
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.meal-plan-checkbox input:checked').length;
            document.getElementById('selectedCount').textContent = `${count} item${count !== 1 ? 's' : ''} selected`;
        }

        // Add event listeners to checkboxes
        document.querySelectorAll('.meal-plan-checkbox input').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        function deleteSelectedPlans() {
            const selectedPlans = document.querySelectorAll('.meal-plan-checkbox input:checked');
            if (selectedPlans.length === 0) {
                alert('Please select at least one meal plan to delete.');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selectedPlans.length} meal plan(s)?`)) {
                const planIds = Array.from(selectedPlans).map(checkbox => checkbox.value);
                
                const formData = new FormData();
                formData.append('action', 'delete_multiple');
                formData.append('plan_ids', JSON.stringify(planIds));

                fetch('meal_planner.php', {
                    method: 'POST',
                    body: formData,
                    redirect: 'follow'
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text().then(text => {
                            try {
                                const data = JSON.parse(text);
                                if (data.error) {
                                    alert(data.error);
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting meal plans. Please try again.');
                });
            }
        }

        // Prevent clicking on meal plan links when in delete mode
        document.querySelectorAll('.meal-plan-item a').forEach(link => {
            link.addEventListener('click', (e) => {
                if (deleteMode) {
                    e.preventDefault();
                    const checkbox = link.parentElement.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    updateSelectedCount();
                }
            });
        });

        document.getElementById('deleteSelected').addEventListener('click', deleteSelectedPlans);
    </script>
</body>
</html>
