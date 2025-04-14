<?php

if (session_status() == PHP_SESSION_NONE){
    session_start();
}

require '../config/db_connection.php';
require '../controller/comp_controller.php';

$compController = new CompetitionController($conn);

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['user_id'];
    $checkEntry = $compController->checkEntry($_GET['comp_id'], $user_id);
}

$comp = $compController->getComp($_GET['comp_id']);
$allEntries = $compController->getAllEntries($_GET['comp_id']);

usort($allEntries, function ($a, $b) {
    return $b['vote_count'] - $a['vote_count'];
});
$top_entries = array_slice($allEntries, 0, 5);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_id = $_POST['entry_id'];
    $result = $compController->voteRecipe($entry_id, $_SESSION['user_id']);
    if ($result === true) {
        echo "<script>alert('You have successfully voted for this recipe.');
        window.history.back();</script>";
        exit();
    } else {
        echo "<script>alert('$result');
        window.history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competitions #<?php echo $_GET['comp_id']; ?></title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #comp-image-section {
            background-color: black; 
            height: 300px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .info-box {
            border-radius: 10px;
            border: 1px solid white; 
            margin: 20px;       
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div id="comp-image-section">
        <img src="<?= (!empty($comp['comp_image']) ? '../uploads/comp/' . $comp['comp_image'] : '../assets/images/default_comp.png'); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Competition Image">
    </div>

    
    <div class="container mb-5 mt-3">
        <div class="row">
            <div class="col-12 text-center">
                <h1><?php echo $comp['comp_title']; ?></h1>
                <p><?php echo $comp['comp_desc']; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="#info" class="btn btn-secondary me-2" style="width: 200px;" id="info-btn">Competition Info</a>
                <a href="#entries" class="btn btn-secondary me-2" style="width: 200px;" id="entries-btn">All Entries</a>
                <?php if (strtotime(date('Y-m-d')) >= strtotime($comp['end_date'] . ' + 11 days')) : ?>
                    <a href="#announcement" class="btn btn-primary" style="width: 200px;" id="announcement-btn">Winner Announcement</a>
                <?php elseif (strtotime($comp['end_date']) > time()) : ?>
                    <?php if ($checkEntry) : ?>
                        <a href="#" class="btn btn-success disabled" style="width: 200px;" id="entries-btn" aria-disabled="true">Joined</a>
                    <?php else : ?>
                        <a href="competition_entry?comp_id=<?php echo $_GET['comp_id']; ?>" class="btn btn-success" style="width: 200px;" id="entries-btn">Join Competition</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Entries list -->
    <div class="content-section" id="entries">
        <div class="container">
            <div class="row row-cols-4">
                <?php if (empty($allEntries)) : ?>
                    <div class="col-12 text-center">
                        <p class="lead">No entries yet.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ($allEntries as $i => $entry) : ?>
                        <div class="col">
                            <form action="view_comp?comp_id=<?php echo $_GET['comp_id'] ?>#entries" method="post" onsubmit="return confirmVote();">
                                <input type="hidden" name="entry_id" value="<?= htmlspecialchars($entry['entry_id']) ?>">

                                <div class="card border shadow-sm" style="width: 100%;">
                                    <img src="<?= (!empty($entry['images']) ? '../uploads/recipes/' . $entry['images'] : '../assets/images/default_recipe.png'); ?>" class="card-img-top" alt="Recipe Image" style="height: 200px; object-fit: cover;">
                                    <div class="d-flex flex-column card-body justify-content-between p-3 text-start">
                                        <h5 class="card-title"><?= htmlspecialchars($entry['title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars(substr($entry['description'], 0, 50)) . '...'; ?></p>
                                        <p class="card-text text-end"><b><?= htmlspecialchars($entry['vote_count']) ?></b> Votes</p>
                                        <?php if (strtotime($comp['end_date'] . ' + 11 days') > time()) : ?>
                                            <button type="submit" class="btn btn-success mt-2">Vote</button>
                                        <?php else : ?>
                                            <button type="submit" class="btn btn-secondary mt-2 disabled" disabled>Voting Closed</button>
                                        <?php endif; ?>
                                        <a href="view_recipe?recipe_id=<?= $entry['recipe_id'] ?>" class="btn btn-secondary mt-2 text-white">View Recipe</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Competition Info -->
    <div class="content-section" id="info" style="display: none;">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="info-section bg-dark text-light p-3 rounded">
                        <h4 class="text-center">Competition Info</h4>
                        <div class="row">
                            <div class="col info-box">
                                <h5 class="text-center text-decoration-underline">Theme</h5>
                                <p class="text-center"><?php echo $comp['comp_theme']; ?></p>
                            </div>
                            <div class="col info-box">
                                <h5 class="text-center text-decoration-underline">Prizes</h5>
                                <?php foreach ($comp['prizes'] as $index => $prize) : ?>
                                    <p class="text-center">
                                        <?php
                                            $number = $index + 1;
                                            if ($number == 1) {
                                                $suffix = 'st';
                                            } elseif ($number == 2) {
                                                $suffix = 'nd';
                                            } elseif ($number == 3) {
                                                $suffix = 'rd';
                                            } elseif ($number == 4) {
                                                $suffix = 'th';
                                                $number = '4-5';
                                            }
                                        ?>
                                        <?= $number . $suffix ?> Prize: <?= htmlspecialchars($prize) ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <hr>
                        <h5 class="text-center text-decoration-underline">Competition Schedule</h5>
                        <div class="row">
                            <div class="col info-box">
                                <h6 class="text-center text-decoration-underline">Competition Period</h6>
                                <p class="text-center"><?php echo date('d/m/Y', strtotime($comp['start_date'])) . "<br>-<br>" . date('d/m/Y', strtotime($comp['end_date'])); ?></p>
                            </div>
                            <div class="col info-box">
                                <h6 class="text-center text-decoration-underline">Final Voting Period</h6>
                                <p class="text-center"><?php echo date('d/m/Y', strtotime($comp['end_date'] . ' + 1 days')) . "<br>-<br>" . date('d/m/Y', strtotime($comp['end_date'] . ' + 10 days')); ?></p>
                            </div>
                            <div class="col info-box">
                                <h6 class="text-center text-decoration-underline">Announcement Date</h6>
                                <p class="text-center"><?php echo date('d/m/Y', strtotime($comp['end_date'] . ' + 11 days')); ?></p>
                            </div>
                        </div>
                        <hr>
                        <h5 class="text-center text-decoration-underline mt-5">Terms and Conditions</h5>
                        <div class="col info-box">
                            <ol class="mt-3">
                                <li>
                                    <strong>Competition Period:</strong><br>
                                    The recipe competition runs from <strong><?php echo date('d/m/Y', strtotime($comp['start_date'])); ?></strong> to <strong><?php echo date('d/m/Y', strtotime($comp['end_date'])); ?></strong> (GMT+8).
                                </li>
                                <li class="pt-2">
                                    <strong>Eligibility:</strong><br>
                                    Participants must submit an original recipe created by themselves. Entries must be submitted within the competition period to be considered.
                                </li>
                                <li class="pt-2">
                                    <strong>Submission Guidelines:</strong>
                                    <ul>
                                        <li>Recipes must include a list of ingredients and step-by-step instructions.</li>
                                        <li>Clear images of the finished dish are required.</li>
                                    </ul>
                                </li>
                                <li class="pt-2">
                                    <strong>Originality:</strong><br>
                                    All recipes must be original. Plagiarized or copied content will lead to immediate disqualification.
                                </li>
                                <li class="pt-2">
                                    <strong>Voting:</strong><br>
                                    Entries will be evaluated based on creativity, presentation, clarity of instructions, and overall appeal. Others votes are final.
                                </li>
                                <li class="pt-2">
                                    <strong>Winner Announcement:</strong><br>
                                    Winners will be announced on <strong><?php echo date('d/m/Y', strtotime($comp['end_date'] . ' + 11 days')); ?></strong> through the official channels.
                                </li>
                                <li class="pt-2">
                                    <strong>Prizes:</strong><br>
                                    Prizes are non-transferable and cannot be exchanged for cash or other goods.
                                </li>
                                <li class="pt-2">
                                    <strong>Recipe Retention:</strong><br>
                                    By submitting, you grant permission for your recipe and images to be featured and shared by the organizer on their platform(s).
                                </li>
                                <li class="pt-2">
                                    <strong>Disqualification:</strong><br>
                                    Any violation of the rules or suspected fraud may result in disqualification from the current and future contests.
                                </li>
                                <li class="pt-2">
                                    <strong>Disclaimer:</strong><br>
                                    This competition is not sponsored by or affiliated with any third-party brands unless explicitly stated.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Winner Announcement -->
    <div class="content-section" id="announcement" style="display: none;">
        <div class="container">
            <div class="row justify-content-center">
                <?php for ($index = 0; $index < 3; $index++) : ?>
                    <?php
                        $entry = $top_entries[$index] ?? null;
                        $background_color = ($index == 0) ? 'gold' : (($index == 1) ? '#C0C0C0' : '#CD7F32');
                    ?>
                    <div class="col-md-4">
                        <div class="card border shadow-sm" style="width: 100%; background-color: <?= $background_color ?>;">
                            <h5 class="card-header text-center">Top Entry #<?= $index + 1; ?></h5>
                            <img src="<?= (!empty($entry['images']) ? '../uploads/recipes/' . $entry['images'] : '../assets/images/default_recipe.png'); ?>" class="card-img-top" alt="Recipe Image" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($entry['title'] ?? 'No Entry'); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($entry['description'] ?? 'No description available.', 0, 50)) . '...'; ?></p>
                                <p class="card-text"><?= '<b>Submitted by:</b> ' . htmlspecialchars($entry['username'] ?? 'No user.'); ?></p>
                                <p class="text-muted">Votes: <?= $entry['vote_count'] ?? 0; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="row justify-content-center mt-4">
                <?php for ($index = 0; $index < 2; $index++) : ?>
                    <?php
                        $entry = $top_entries[$index + 3] ?? null;
                    ?>
                    <div class="col-md-4">
                        <div class="card border shadow-sm" style="width: 100%; background-color: darkgray;">
                            <h5 class="card-header text-center">Top Entry #<?= $index + 4; ?></h5>
                            <img src="<?= (!empty($entry['images']) ? '../uploads/recipes/' . $entry['images'] : '../assets/images/default_recipe.png'); ?>" class="card-img-top" alt="Recipe Image" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($entry['title'] ?? 'No Entry'); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($entry['description'] ?? 'No description available.', 0, 50)) . '...'; ?></p>
                                <p class="text-muted">Votes: <?= $entry['vote_count'] ?? 0; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const entriesBtn = document.getElementById('entries-btn');
            const infoBtn = document.getElementById('info-btn');
            const announcementBtn = document.getElementById('announcement-btn');
            const entriesSection = document.getElementById('entries');
            const infoSection = document.getElementById('info');
            const announcementSection = document.getElementById('announcement');

            entriesBtn.addEventListener('click', () => {
                entriesSection.style.display = 'block';
                infoSection.style.display = 'none';
                announcementSection.style.display = 'none';
            });

            infoBtn.addEventListener('click', () => {
                entriesSection.style.display = 'none';
                infoSection.style.display = 'block';
                announcementSection.style.display = 'none';
            });

            announcementBtn.addEventListener('click', () => {
                entriesSection.style.display = 'none';
                infoSection.style.display = 'none';
                announcementSection.style.display = 'block';
            });
        });

        function confirmVote() {
            return confirm("Are you sure you want to vote for this recipe?");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>