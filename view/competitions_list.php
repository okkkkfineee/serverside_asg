<?php

if (session_status() == PHP_SESSION_NONE){
    session_start();
}

require '../config/db_connection.php';
require '../controller/comp_controller.php';

$compController = new CompetitionController($conn);

$comps = $compController->getAllComp();

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
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="d-flex">
        <div class="p-4 w-100">
            <div class="container mt-4">
                <h4 class="mb-3 py-2">Competitions</h4>
                <div class="row justify-content-center">
                <?php if (empty($comps)) : ?>
                    <div class="col-12 text-center">
                        <p class="lead">No competitions available.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ($comps as $i => $comp) : ?>
                        <?php if ($i % 3 == 0 && $i !== 0) : ?>
                            </div><div class="row mt-4">
                        <?php endif; ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 col-xl-4 d-flex justify-content-center mb-4">
                        <div class="card border shadow-sm" style="width: 100%;">
                            <img src="../uploads/comp/<?php echo $comp['comp_image'] ?? 'default_comp.png'; ?>" class="card-img-top" alt="Competition Image" style="height: 200px; object-fit: cover;">
                            <div class="d-flex flex-column card-body justify-content-between p-3 text-start">
                                <h5 class="card-title"><?php echo htmlspecialchars($comp['comp_title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(substr($comp['comp_desc'], 0, 50)) . '...'; ?></p>
                                <?php 
                                    $end_date = new DateTime($comp['end_date']);
                                    $today = new DateTime('today');
                                    $diff = $today->diff($end_date);
                                    $days_after_end = $diff->invert ? $diff->days : -$diff->days;
                                    
                                    if ($today < $end_date) {
                                        $time_left_text = $diff->format('%d days left');
                                        $badge_class = 'bg-success';
                                    } elseif ($days_after_end <= 10) {
                                        $time_left_text = 'Voting Period';
                                        $badge_class = 'bg-warning';
                                    } else {
                                        $time_left_text = 'Ended';
                                        $badge_class = 'bg-danger';
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-8">
                                        <p class="card-text"><b>Theme: </b><?php echo htmlspecialchars($comp['comp_theme']); ?></p>
                                    </div>
                                    <div class="col-4">
                                        <p>
                                            <span class="badge <?php echo $badge_class; ?> px-3 py-2 float-end">
                                                <?php echo $time_left_text; ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <a href="view_comp?comp_id=<?php echo $comp['comp_id'] . "#entries"; ?>" class="btn btn-secondary mt-2" style="color: white;">View Competition</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>