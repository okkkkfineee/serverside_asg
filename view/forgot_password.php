<?php
require '../config/db_connection.php';
require '../controller/user_controller.php';

$userController = new UserController($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'Send') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $result = $userController->requestForgotPassword($email);
        if ($res = true) {
            echo "<script>alert('A password reset link has been sent to your email.');
            window.location.href = 'entry';</script>";
            exit();
        } else {
            echo "<script>alert('$result');
            window.location.href = 'forget_password';</script>";
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
    <title>Forgot Password Request</title>
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
                    <form method="POST" action="forgot_password">
                        <h3 class="text-center mb-3 mt-2">Forgot Password</h3>
                        <div class="mb-3">
                           <p>Enter your email address to receive a link to reset your password.</p>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="pt-3 d-grid gap-2 justify-content-center">
                            <input type="submit" name="action" value="Send" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>