<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/user_controller.php';
require '../controller/comp_controller.php';

$userController = new UserController($conn);
$compController = new CompetitionController($conn);

$type = isset($_GET['type']) ? $_GET['type'] : "host";

if ($type === "edit" && isset($_GET['comp_id'])) {
    $comp_id = $_GET['comp_id'];
    $comp_info = $compController->getComp($comp_id);
}

if ($type === "delete" && isset($_GET['comp_id'])) {
    $comp_id = $_GET['comp_id'];
    $result = $compController->deleteComp($comp_id);
    if ($result === true) {
        echo "<script>alert('Competition deleted successfully!');
        window.location.href = 'admin_panel';</script>";
    } else {
        $error = $result;
        echo "<script>alert('$error');
        window.location.href = 'admin_panel';</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comp_title = mysqli_real_escape_string($conn, $_POST['comp_title']);
    $comp_desc = mysqli_real_escape_string($conn, $_POST['comp_desc']);
    $comp_prize = array(
        'comp_prize_1' => mysqli_real_escape_string($conn, $_POST['comp_prize_1']),
        'comp_prize_2' => mysqli_real_escape_string($conn, $_POST['comp_prize_2']),
        'comp_prize_3' => mysqli_real_escape_string($conn, $_POST['comp_prize_3']),
        'comp_prize_4' => isset($_POST['comp_prize_4']) ? mysqli_real_escape_string($conn, $_POST['comp_prize_4']) : ''
    );
    $comp_theme = mysqli_real_escape_string($conn, $_POST['comp_theme']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    if (isset($_POST['action']) && $_POST['action'] === 'Host') {
        if (!empty($_FILES["image"]["name"])) {
            $unique_id = uniqid();
            $comp_image = $unique_id . '_' . basename($_FILES["image"]["name"]);
        }
        $result = $compController->manageComp("host", "", $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date);
        if ($result === true) {
            echo "<script>alert('Competition hosted successfully!');
            window.location.href = 'admin_panel';</script>";
        } else {
            $error = $result;
            echo "<script>alert('$error');
            window.location.href = 'manage_comp?host';</script>";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'Edit') {
        $existingComp = $compController->getComp($comp_id);
        $old_image = $existingComp ? $existingComp['comp_image'] : null;
    
        if (!empty($_FILES["image"]["name"])) {
            $comp_image = $comp_id . '_' . basename($_FILES["image"]["name"]);
        } else {
            $comp_image = $old_image;
        }
        $result = $compController->manageComp("update", $comp_id, $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date);
        if ($result === true) {
            echo "<script>alert('Competition updated successfully!');
            window.location.href = 'admin_panel';</script>";
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
    <title>Manage Competitions</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4">
        <?php if ($type === "host"): ?>
            <h2 class="text-center mb-4">Host New Competition</h2>
            <form action="manage_comp?type=host" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Competition Title</label>
                        <input type="text" name="comp_title" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload Competition Image (JPG, JPEG, PNG only), Max 2 MB</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" data-max-size="2M" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="form-label">Competition Description (Max 100 words)</label>
                    <textarea name="comp_desc" class="form-control" rows="5" maxlength="600" oninput="limitWords(this, 100)" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Competition Prizes (Max 50 words)</label>
                        <div class="row">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">1st Prize</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="comp_prize_1" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">2nd Prize</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="comp_prize_2" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">3rd Prize</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="comp_prize_3" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">4-5th Prize (Optional)</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="comp_prize_4" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-4">
                            <label class="form-label">Theme Category</label>
                            <select id="themeCategory" name="theme_category" class="form-select" required>
                                <option value="">-- Choose a Theme --</option>
                                <option value="cuisine">Cuisine</option>
                                <option value="cooking_time">Cooking Time</option>
                                <option value="difficulty">Difficulty</option>
                            </select>
                        </div>
                        <div class="row">
                            <label class="form-label">Theme</label>
                            <select id="compTheme" name="comp_theme" class="form-select" required>
                                <option value="">-- Choose a Theme --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" name="action" value="Host" class="btn btn-primary">Host Competition</button>
                </div>
            </form>

        <?php elseif ($type === "edit"): ?>
            <h2 class="text-center mb-4">Edit Competition</h2>
            <form action="manage_comp?type=edit&comp_id=<?= $comp_id ?>" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="hidden" name="comp_id" value="<?= $recipe_id ?>">
                        <label class="form-label">Competition Title</label>
                        <input type="text" name="comp_title" class="form-control" value="<?= htmlspecialchars($comp_info['comp_title']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload New Image (JPG, JPEG, PNG only), Max 2 MB</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png">
                        <p>Current Image File: <strong><?= htmlspecialchars($comp_info['comp_image']) ?></strong></p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Competition Description (Max 100 words)</label>
                    <textarea name="comp_desc" class="form-control" rows="5" maxlength="600" oninput="limitWords(this, 100)" required><?= htmlspecialchars(str_replace(['\\r\\n', '\\n', '\\r'], "\n", $comp_info['comp_desc'])) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Competition Prizes</label>
                        <?php 
                        $prizes = $comp_info['prizes'] ?? [];
                        $labels = ['1st Prize', '2nd Prize', '3rd Prize', '4-5th Prize (Optional)']; // Define labels
                        for ($i = 0; $i < 4; $i++):
                            $value = isset($prizes[$i]) ? htmlspecialchars($prizes[$i]) : ''; 
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label"><?= $labels[$i] ?></label> <!-- Dynamic label based on $i -->
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="comp_prize_<?= $i + 1 ?>" class="form-control mb-2" value="<?= $value ?>" <?= $i < 3 ? 'required' : '' ?>>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-4">
                            <label class="form-label">Theme Category</label>
                            <select id="themeCategory" name="theme_category" class="form-select" required>
                                <option value="">-- Choose a Theme --</option>
                                <option value="cuisine">Cuisine</option>
                                <option value="cooking_time">Cooking Time</option>
                                <option value="difficulty">Difficulty</option>

                            </select>
                        </div>
                        <div class="row">
                            <label class="form-label">Theme</label>
                            <select id="compTheme" name="comp_theme" class="form-select" required>
                                <option value="">-- Choose a Theme --</option>
                                <option value="<?= htmlspecialchars($comp_info['comp_theme']) ?>" selected><?= htmlspecialchars($comp_info['comp_theme']) ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= htmlspecialchars($comp_info['start_date']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required value="<?= htmlspecialchars($comp_info['end_date']) ?>">
                    </div>
                </div>
                
                <div class="button-container">
                    <button type="submit" name="action" value="Edit" class="btn btn-primary me-3">Update Changes</button>
                    <a href="admin_panel?page=1" class="btn btn-secondary" style="background-color: red !important;">Discard Changes</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function limitWords(input, maxWords) {
            const words = input.value.split(/\s+/).filter(word => word.length > 0);
            if (words.length > maxWords) {
                input.value = words.slice(0, maxWords).join(' ');
            }
        }

        const themeData = {
            cuisine: ['Any' ,'Chinese', 'Indian', 'Japanese', 'Malay', 'Thai', 'Western'],
            cooking_time: ['Under 15 Minutes', 'Under 30 Minutes', 'Under 1 Hour', 'Slow Cooked'],
            difficulty: ['Beginner-Friendly', 'Easy', 'Moderate', 'Challenging', 'Expert-Level']
        };

        const themeCategory = document.getElementById('themeCategory');
        const compTheme = document.getElementById('compTheme');

        themeCategory.addEventListener('change', function () {
            const selectedCategory = this.value;
            const themes = themeData[selectedCategory] || [];

            compTheme.innerHTML = '<option value="">-- Choose a Theme --</option>';

            themes.forEach(function (theme) {
                const option = document.createElement('option');
                option.value = theme;
                option.textContent = theme;
                compTheme.appendChild(option);
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const compTheme = document.querySelector('#compTheme').value;
            let themeCategory;

            Object.keys(themeData).some(key => {
                if (themeData[key].includes(compTheme)) {
                    themeCategory = key;
                    return true;
                }
            });

            if (themeCategory) {
                document.getElementById('themeCategory').value = themeCategory;
            }
        });

        document.querySelector('form').addEventListener('submit', function (e) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (startDate > endDate) {
                alert('Start date cannot be later than end date.');
                e.preventDefault();
            } else if (startDate.toDateString() === endDate.toDateString()) {
                alert('Start date and end date cannot be the same.');
                e.preventDefault();
            } else if ((endDate - startDate) / (1000 * 60 * 60 * 24) < 10) {
                alert('The duration between start date and end date cannot be lesser than 10 days.');
                e.preventDefault();
            } else if (startDate > new Date()) {
                alert('Start date cannot be later than today.');
                e.preventDefault();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>