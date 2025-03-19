<?php
require '../includes/auth.php';
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
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                eventClick: function(info) {
                    alert("Meal: " + info.event.title);
                },
                select: function(info) {
                    document.getElementById('mealDate').value = info.startStr;
                    var modal = new bootstrap.Modal(document.getElementById('mealModal'));
                    modal.show();
                }
            });
            calendar.render();
        });
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                <h4>Meal Categories</h4>
                <div class="meal-category">Breakfast</div>
                <div class="meal-category">Lunch</div>
                <div class="meal-category">Dinner</div>
                <div class="meal-category">Snacks</div>
            </div>

            <!-- Calendar -->
            <div class="col-md-9">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Meal Entry Modal -->
    <div class="modal fade" id="mealModal" tabindex="-1" aria-labelledby="mealModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mealModalLabel">Add Meal Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="mealDate" class="form-label">Date</label>
                            <input type="text" class="form-control" id="mealDate" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="mealType" class="form-label">Meal Type</label>
                            <select class="form-select" id="mealType">
                                <option>Breakfast</option>
                                <option>Lunch</option>
                                <option>Dinner</option>
                                <option>Snacks</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="mealName" class="form-label">Meal Name</label>
                            <input type="text" class="form-control" id="mealName">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Meal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
