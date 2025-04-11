<?php

if (session_status() == PHP_SESSION_NONE){
    session_start();
}

require '../config/db_connection.php';
require '../controller/comp_controller.php';

$compController = new CompetitionController($conn);

$comp = $compController->getComp($_GET['comp_id']);
$allEntries = $compController->getAllEntries($_GET['comp_id']);

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
        <img src="../uploads/comp/<?php echo $comp['comp_image'] ?? 'default_comp.png'; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Competition Image">
    </div>

    
    <div class="container mb-5">
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
                <?php if (strtotime($comp['end_date']) > time()) : ?>
                    <a href="competition_entry?comp_id=<?php echo $_GET['comp_id']; ?>" class="btn btn-success" style="width: 200px;" id="entries-btn">Join Competition</a>
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
                    <div class="card border shadow-sm" style="width: 100%;">
                        <img src="../uploads/<?php echo $entry['images'] ?? 'default_comp.png'; ?>" class="card-img-top" alt="Competition Image" style="height: 200px; object-fit: cover;">
                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start">
                            <h5 class="card-title"><?php echo htmlspecialchars($entry['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($entry['description'], 0, 50)) . '...'; ?></p>
                            <!-- vote -->
                            <a href="view_comp?comp_id=<?php echo $entry['comp_id'] . "#entries"; ?>" class="btn btn-secondary mt-2" style="color: white;">View Competition</a>
                        </div>
                    </div>
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
                                <p class="text-center"><?php echo $comp['comp_prize']; ?></p>
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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const entriesBtn = document.getElementById('entries-btn');
            const infoBtn = document.getElementById('info-btn');
            const entriesSection = document.getElementById('entries');
            const infoSection = document.getElementById('info');

            entriesBtn.addEventListener('click', () => {
                entriesSection.style.display = 'block';
                infoSection.style.display = 'none';
            });

            infoBtn.addEventListener('click', () => {
                entriesSection.style.display = 'none';
                infoSection.style.display = 'block';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>