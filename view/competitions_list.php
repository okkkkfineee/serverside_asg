<?php

if (session_status() == PHP_SESSION_NONE){
    session_start();
}

require '../config/db_connection.php';
require '../controller/comp_controller.php';

$compController = new CompetitionController($conn);

// $comps = $compController->getAllComp();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'getList') {
        $filters = [
            'search' => $_POST['search'] ?? '',
            'theme' => $_POST['theme'] ?? '',
            'status' => isset($_POST['status']) ? explode(',', $_POST['status']) : []
        ];
        $comps = $compController->getAllCompWithFilters($filters);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'result' => $comps]);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competitions List</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="d-flex">
        <div class="col-1"></div>
        <!-- Filter Section -->
        <div class="py-5 px-4 col-2">
            <div class="bg-white rounded p-4">
                <h4 class="mb-3 py-2"><i class="bi bi-funnel"></i> Filter </h4>
                <div class="mb-3">
                    <label for="comp_name" class="form-label">Search Competition Name:</label>
                    <input type="text" id="comp_name" name="comp_name" class="form-control" placeholder="Search...">
                </div>
                <br>
                <div class="mb-3">
                    <label for="theme" class="form-label">Theme:</label>
                    <select id="theme" name="theme" class="form-select">
                        <option value="">All Themes</option>
                        <optgroup label="Cuisine">
                            <option value="Any">Any</option>
                            <option value="Chinese">Chinese</option>
                            <option value="Indian">Indian</option>
                            <option value="Japanese">Japanese</option>
                            <option value="Malay">Malay</option>
                            <option value="Thai">Thai</option>
                            <option value="Western">Western</option>
                        </optgroup>
                        <optgroup label="Cooking Time">
                            <option value="Under 15 Minutes">Under 15 Minutes</option>
                            <option value="Under 30 Minutes">Under 30 Minutes</option>
                            <option value="Under 1 Hour">Under 1 Hour</option>
                            <option value="Slow Cooked">Slow Cooked</option>
                        </optgroup>
                        <optgroup label="Difficulty">
                            <option value="Beginner-Friendly">Beginner-Friendly</option>
                            <option value="Easy">Easy</option>
                            <option value="Moderate">Moderate</option>
                            <option value="Challenging">Challenging</option>
                            <option value="Expert-Level">Expert-Level</option>
                        </optgroup>
                    </select>
                </div>
                <br>
                <div class="mb-3">
                    <label for="status" class="form-label">Status:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="status_all" name="status_all" value="" onchange="toggleAllStatus(this)">
                        <label class="form-check-label" for="status_all">All</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="status_ongoing" name="status_ongoing" value="ongoing" onchange="toggleStatusAll(this)">
                        <label class="form-check-label" for="status_ongoing">Ongoing</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="status_voting" name="status_voting" value="voting" onchange="toggleStatusAll(this)">
                        <label class="form-check-label" for="status_voting">Voting Period</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="status_ended" name="status_ended" value="ended" onchange="toggleStatusAll(this)">
                        <label class="form-check-label" for="status_ended">Ended</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Competitions Section -->
        <div class="p-4 col-8">
            <h4 class="pt-4 ps-3">Competitions List</h4>
            <div class="container mt-4" id="competition-list">
                <div class="row justify-content-center">
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/competitions_list.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>