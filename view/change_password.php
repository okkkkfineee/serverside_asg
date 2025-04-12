<?php
session_start();

require '../config/db_connection.php';
require '../controller/user_controller.php';

$userController = new UserController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'change') {
        $user_id = $_SESSION['user_id'];
        $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
        $result = $userController->changePassword("change", $user_id, $old_password, $new_password, $confirm_password, "");
        if ($res = true) {
            echo "<script>alert('You have successfully changed your password.');
            window.location.href = 'profile';</script>";
            exit();
        } else {
            echo "<script>alert('$result');
            window.location.href = 'profile';</script>";
            exit();
        }
    } else if (isset($_POST['action']) && $_POST['action'] === 'Reset') {
        $token = mysqli_real_escape_string($conn, $_GET['token']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
        $result = $userController->changePassword("reset", "", "", $new_password, $confirm_password, $token);
        if ($res = true) {
            echo "<script>alert('You have successfully reset your password.');
            window.location.href = 'entry';</script>";
            exit();
        } else {
            echo "<script>alert('$result');
            window.location.href = 'entry';</script>";
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Management</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center min-vh-100 align-items-center">
            <div class="col-md-7 col-lg-4 col-10 col-xxl-3">
                <div class="bg-white px-4 py-3 px-md-5 shadow">
                    <?php if (isset($_GET['token'])) :?>
                    <form method="POST" action="change_password?token=<?php echo $_GET['token']; ?>">
                        <h3 class="text-center mb-3 mt-2">Reset Password</h3>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="new_password" placeholder="Password" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <div class="pt-3 d-grid gap-2 justify-content-center">
                            <input type="submit" name="action" value="Reset" class="btn btn-primary">
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>