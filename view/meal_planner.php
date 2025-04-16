<?php
require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/meal_planning_controller.php';
require '../controller/recipe_controller.php';

$mealPlanningController = new MealPlanningController($conn);
$recipeController = new RecipeController($conn);

// Check if user has admin role
$isAdmin = isset($_SESSION['roles']) && in_array($_SESSION['roles'], ['Superadmin', 'Admin', 'Mod']);

// Get all recipes for the dropdown
$allRecipes = $recipeController->getAllRecipes();

// Get user's meal plans or all meal plans if admin
if ($isAdmin) {
    $userMealPlans = $mealPlanningController->getAllMealPlans();
} else {
    $userMealPlans = $mealPlanningController->getUserMealPlans($_SESSION['user_id']);
}

// Get selected category filter
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'All';

// Filter meal plans by category if a specific category is selected
$filteredMealPlans = null;
if ($selectedCategory !== 'All') {
    if ($isAdmin) {
        $filteredMealPlans = $mealPlanningController->getAllMealPlansByCategory($selectedCategory);
    } else {
        $filteredMealPlans = $mealPlanningController->getMealPlansByCategory($_SESSION['user_id'], $selectedCategory);
    }
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
            
            $result = $mealPlanningController->createMealPlan(
                $recipe_id, 
                $user_id, 
                $plan_name, 
                $meal_category, 
                $meal_time, 
                $meal_date
            );
            
            if ($result) {
                $_SESSION['success_message'] = "Meal plan added successfully!";
                header("Location: meal_planner.php");
                exit();
            } else {
                $_SESSION['error_message'] = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : "Failed to add meal plan. Please try again.";
                header("Location: meal_planner.php");
                exit();
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
    <link rel="stylesheet" href="../assets/css/meal_planner.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <style>
        .event-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 10000;
            pointer-events: none;
            max-width: 300px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: opacity 0.3s ease;
        }

        .meal-plans-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .meal-plans-list::-webkit-scrollbar {
            width: 6px;
        }

        .meal-plans-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .meal-plans-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .meal-plans-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .username-badge {
            background-color: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-left: 5px;
        }
    </style>
    <script>
        let tooltip = null;

        function createTooltip() {
            if (!tooltip) {
                tooltip = document.createElement('div');
                tooltip.className = 'event-tooltip';
                tooltip.style.display = 'none';
                document.body.appendChild(tooltip);
            }
        }

        function showTooltip(event, info) {
            const rect = event.currentTarget.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            tooltip.innerHTML = `
                <strong>${info.event.title}</strong><br>
                ${info.event.extendedProps.recipe_name}<br>
                ${info.event.extendedProps.meal_category}<br>
                ${info.event.extendedProps.username ? 'User: ' + info.event.extendedProps.username + '<br>' : ''}
                Time: ${info.event.start.toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'})}
            `;
            
            tooltip.style.display = 'block';
            
            const tooltipHeight = tooltip.offsetHeight;
            const tooltipWidth = tooltip.offsetWidth;
            
            let top = rect.top + scrollTop - tooltipHeight - 10;
            let left = rect.left + scrollLeft + (rect.width - tooltipWidth) / 2;
            
            if (top < scrollTop) {
                top = rect.bottom + scrollTop + 10;
            }
            if (left < scrollLeft) {
                left = scrollLeft + 10;
            } else if (left + tooltipWidth > window.innerWidth + scrollLeft) {
                left = window.innerWidth + scrollLeft - tooltipWidth - 10;
            }
            
            tooltip.style.top = top + 'px';
            tooltip.style.left = left + 'px';
        }

        function hideTooltip() {
            if (tooltip) {
                tooltip.style.display = 'none';
            }
        }

        // Helper function to darken/lighten colors
        function adjustColor(color, amount) {
            return color.replace(/^#/, '').match(/.{2}/g).map(function(hex) {
                const val = Math.max(0, Math.min(255, parseInt(hex, 16) + amount));
                return val.toString(16).padStart(2, '0');
            }).join('');
        }

        // Function to update time options based on selected category
        function updateTimeBasedOnCategory() {
            const category = document.getElementById('meal_category').value;
            const timeSelect = document.getElementById('meal_time');
            const timeError = document.getElementById('timeError');
            const selectedTime = timeSelect.value;
            
            timeSelect.classList.remove('is-invalid');
            timeError.textContent = '';
            
            if (!category) return;

            Array.from(timeSelect.options).forEach(option => {
                if (option.value === '') return;
                
                if (option.getAttribute('data-category') === category) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            if (selectedTime) {
                const selectedOption = timeSelect.options[timeSelect.selectedIndex];
                if (selectedOption.getAttribute('data-category') !== category) {
                    timeSelect.value = '';
                }
            }
        }

        function isTimeInRange(time, start, end) {
            return time >= start && time <= end;
        }

        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const hour = parseInt(hours);
            const period = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${period}`;
        }

        // Meal Plan Delete Mode
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
                handleCheckboxChange();
            } else {
                actionBar.classList.remove('show');
                setTimeout(() => actionBar.style.display = 'none', 300);
                document.querySelectorAll('.meal-plan-checkbox input').forEach(cb => cb.checked = false);
                updateSelectedCount();
            }
        }

        function handleCheckboxChange() {
            const checkedCount = document.querySelectorAll('.meal-plan-checkbox input:checked').length;
            const deleteActionBar = document.getElementById('deleteActionBar');
            if (deleteMode) {
                deleteActionBar.style.display = 'block';
            } else {
                deleteActionBar.style.display = checkedCount > 0 ? 'block' : 'none';
            }
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.meal-plan-checkbox input:checked').length;
            const total = document.querySelectorAll('.meal-plan-checkbox input').length;
            const countElement = document.getElementById('selectedCount');
            if (countElement) {
                countElement.textContent = `${count} of ${total} item${total !== 1 ? 's' : ''} selected`;
            }
        }

        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.meal-plan-checkbox input');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            
            handleCheckboxChange();
        }

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

        document.addEventListener('DOMContentLoaded', function() {
            createTooltip();
            var calendar = null;

            function initializeCalendar() {
                var calendarEl = document.getElementById('calendar');
                
                var events = [];
                <?php if ($filteredMealPlans && $filteredMealPlans->num_rows > 0): ?>
                    <?php 
                    $filteredMealPlans->data_seek(0);
                    while ($plan = $filteredMealPlans->fetch_assoc()): 
                        $hours = floor($plan['meal_time'] / 60);
                        $minutes = $plan['meal_time'] % 60;
                        $formattedTime = sprintf("%02d:%02d", $hours, $minutes);
                        $dateTime = $plan['meal_date'] . 'T' . $formattedTime . ':00';
                        $recipeInfo = $recipeController->getRecipeInfo($plan['recipe_id']);
                        $recipeTitle = $recipeInfo['title'];
                    ?>
                        events.push({
                            id: '<?php echo $plan['plan_id']; ?>',
                            title: '<?php echo htmlspecialchars($plan['plan_name']); ?>',
                            start: '<?php echo $dateTime; ?>',
                            allDay: false,
                            extendedProps: {
                                plan_id: '<?php echo $plan['plan_id']; ?>',
                                meal_category: '<?php echo htmlspecialchars($plan['meal_category']); ?>',
                                meal_time: '<?php echo $formattedTime; ?>',
                                recipe_name: '<?php echo htmlspecialchars($recipeTitle); ?>',
                                username: '<?php echo $isAdmin ? htmlspecialchars($plan['username']) : ''; ?>'
                            }
                        });
                    <?php endwhile; ?>
                <?php endif; ?>
                
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    selectable: true,
                    events: events.map(event => ({
                        ...event,
                        backgroundColor: getEventColor(event.extendedProps.meal_category),
                        borderColor: getEventBorderColor(event.extendedProps.meal_category),
                        textColor: '#000000'
                    })),
                    eventOrder: 'start',
                    displayEventTime: true,
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                    eventDidMount: function(info) {
                        const eventDate = new Date(info.event.start);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        
                        if (eventDate < today) {
                            info.el.style.backgroundColor = '#808080';
                            info.el.style.borderColor = '#696969';
                            info.el.style.color = '#ffffff';
                            info.el.style.opacity = '0.9';
                            
                            info.el.style.transition = 'all 0.3s ease';
                            info.el.addEventListener('mouseover', function(e) {
                                info.el.style.backgroundColor = '#696969';
                                info.el.style.transform = 'scale(1.05)';
                                info.el.style.zIndex = '5';
                                showTooltip(e, info);
                            });
                            info.el.addEventListener('mouseout', function() {
                                info.el.style.backgroundColor = '#808080';
                                info.el.style.transform = 'scale(1)';
                                info.el.style.zIndex = '1';
                                hideTooltip();
                            });
                        } else {
                            info.el.style.transition = 'all 0.3s ease';
                            info.el.style.backgroundColor = getEventColor(info.event.extendedProps.meal_category);
                            info.el.addEventListener('mouseover', function(e) {
                                info.el.style.backgroundColor = getHoverColor(info.event.extendedProps.meal_category);
                                info.el.style.color = '#ffffff';
                                info.el.style.transform = 'scale(1.05)';
                                info.el.style.zIndex = '5';
                                showTooltip(e, info);
                            });
                            info.el.addEventListener('mouseout', function() {
                                info.el.style.backgroundColor = getEventColor(info.event.extendedProps.meal_category);
                                info.el.style.color = '#000000';
                                info.el.style.transform = 'scale(1)';
                                info.el.style.zIndex = '1';
                                hideTooltip();
                            });
                        }

                        info.el.addEventListener('mousemove', function(e) {
                            if (tooltip.style.display === 'block') {
                                showTooltip(e, info);
                            }
                        });
                    },
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

            function getEventColor(category) {
                const colors = {
                    'Breakfast': '#fff3e0',
                    'Lunch': '#e8f5e9',
                    'Dinner': '#e3f2fd',
                    'Snacks': '#f3e5f5'
                };
                return colors[category] || '#e9ecef';
            }

            function getEventBorderColor(category) {
                const colors = {
                    'Breakfast': '#ff9800',
                    'Lunch': '#4caf50',
                    'Dinner': '#2196f3',
                    'Snacks': '#9c27b0'
                };
                return colors[category] || '#6c757d';
            }

            function getHoverColor(category) {
                const colors = {
                    'Breakfast': '#ff9800',
                    'Lunch': '#4caf50',
                    'Dinner': '#2196f3',
                    'Snacks': '#9c27b0'
                };
                return colors[category] || '#6c757d';
            }

            initializeCalendar();

            document.querySelector('form[action=""]').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('meal_planner.php', {
                    method: 'POST',
                    body: formData,
                    redirect: 'follow'
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

            document.querySelectorAll('.meal-plan-checkbox input').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            const deleteSelectedBtn = document.getElementById('deleteSelected');
            if (deleteSelectedBtn) {
                deleteSelectedBtn.addEventListener('click', deleteSelectedPlans);
            }

            document.querySelectorAll('.meal-plan-item a').forEach(link => {
                link.addEventListener('click', (e) => {
                    if (deleteMode) {
                        e.preventDefault();
                        const checkbox = link.parentElement.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;
                            updateSelectedCount();
                        }
                    }
                });
            });

            document.querySelector('form[action=""]').addEventListener('submit', function(e) {
                const category = document.getElementById('meal_category').value;
                const timeSelect = document.getElementById('meal_time');
                const timeError = document.getElementById('timeError');

                if (category && category !== 'Snacks') {
                    const timeRanges = {
                        'Breakfast': { start: '05:00', end: '11:30' },
                        'Lunch': { start: '11:30', end: '16:30' },
                        'Dinner': { start: '17:00', end: '22:30' }
                    };

                    const range = timeRanges[category];
                    if (range && !isTimeInRange(timeSelect.value, range.start, range.end)) {
                        e.preventDefault();
                        timeSelect.classList.add('is-invalid');
                        timeError.textContent = `${category} time should be between ${formatTime(range.start)} and ${formatTime(range.end)}`;
                        return;
                    }
                }
            });

            updateTimeBasedOnCategory();
        });
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- Multi-delete action bar -->
        <div id="deleteActionBar" class="action-bar" style="display: none;">
            <div class="action-bar-content">
                <span id="selectedCount">0 items selected</span>
                <button class="btn btn-secondary me-2" onclick="toggleSelectAll()">Select All</button>
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
                    <h4 class="mb-0"><?php echo $isAdmin ? 'All Meal Plans' : 'Your Meal Plans'; ?></h4>
                    <button id="toggleDelete" class="btn btn-outline-danger btn-sm" onclick="toggleDeleteMode()" style="background-color: red !important;">
                        <img src="..\assets\images\meal_planner\trash.png" alt="Delete" style="width: 20px; height: 20px;">
                    </button>
                </div>
                <div class="meal-plans-list">
                    <?php 
                    if ($filteredMealPlans) {
                        $filteredMealPlans->data_seek(0);
                        $today = date('Y-m-d');
                        
                        if ($filteredMealPlans->num_rows > 0): 
                            while ($plan = $filteredMealPlans->fetch_assoc()): 
                                $isPastDate = $plan['meal_date'] < $today;
                    ?>
                        <div class="meal-plan-item <?php echo $isPastDate ? 'past-date' : ''; ?>" data-category="<?php echo htmlspecialchars($plan['meal_category']); ?>">
                            <div class="d-flex align-items-center">
                                <div class="meal-plan-checkbox" style="display: none;">
                                    <input type="checkbox" class="form-check-input me-2" value="<?php echo $plan['plan_id']; ?>">
                                </div>
                                <a href="view_meal_plan.php?plan_id=<?php echo $plan['plan_id']; ?>" class="text-decoration-none text-dark flex-grow-1">
                                    <strong><?php echo htmlspecialchars($plan['plan_name']); ?></strong>
                                    <?php if ($isAdmin): ?>
                                        <span class="username-badge"><?php echo htmlspecialchars($plan['username']); ?></span>
                                    <?php endif; ?>
                                    <br>
                                    <small>Recipe: <?php echo htmlspecialchars($recipeController->getRecipeInfo($plan['recipe_id'])['title']); ?><br>
                                    Category: <?php echo htmlspecialchars($plan['meal_category']); ?><br>
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
                                <select class="form-select" id="meal_category" name="meal_category" required onchange="updateTimeBasedOnCategory()">
                                    <option value="">-- Select Category --</option>
                                    <option value="Breakfast">Breakfast (5:00 AM - 11:30 AM)</option>
                                    <option value="Lunch">Lunch (11:30 AM - 4:30 PM)</option>
                                    <option value="Dinner">Dinner (5:00 PM - 10:30 PM)</option>
                                    <option value="Snacks">Snacks (Any Time)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="meal_time" class="form-label">Meal Time</label>
                                <select class="form-select" id="meal_time" name="meal_time" required>
                                    <option value="">-- Select Time --</option>
                                    <?php 
                                    $timeRanges = [
                                        'Breakfast' => ['start' => '05:00', 'end' => '11:30'],
                                        'Lunch' => ['start' => '11:30', 'end' => '16:30'],
                                        'Dinner' => ['start' => '17:00', 'end' => '22:30'],
                                        'Snacks' => ['start' => '00:00', 'end' => '23:30']
                                    ];
                                    
                                    foreach ($timeRanges as $category => $range) {
                                        echo "<optgroup label=\"$category\">";
                                        
                                        list($startHour, $startMin) = explode(':', $range['start']);
                                        list($endHour, $endMin) = explode(':', $range['end']);
                                        
                                        $startTime = ($startHour * 60) + $startMin;
                                        $endTime = ($endHour * 60) + $endMin;
                                        
                                        for ($time = $startTime; $time <= $endTime; $time += 30) {
                                            $hours = floor($time / 60);
                                            $minutes = $time % 60;
                                            $timeStr = sprintf("%02d:%02d", $hours, $minutes);
                                            
                                            $displayHour = $hours % 12;
                                            $displayHour = $displayHour == 0 ? 12 : $displayHour;
                                            $period = $hours >= 12 ? 'PM' : 'AM';
                                            $displayTime = sprintf("%d:%02d %s", $displayHour, $minutes, $period);
                                            
                                            echo "<option value=\"$timeStr\" data-category=\"$category\">$displayTime</option>";
                                        }
                                        
                                        echo "</optgroup>";
                                    }
                                    ?>
                                </select>
                                <div id="timeError" class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="meal_date" class="form-label">Meal Date</label>
                            <input type="date" class="form-control" id="meal_date" name="meal_date" required min="<?php echo date('Y-m-d'); ?>">
                            <div id="dateError" class="invalid-feedback"></div>
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: red !important;">Cancel</button>
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
