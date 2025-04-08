<?php

session_start();
require '../config/db_connection.php';
require '../controller/user_controller.php';

$userController = new UserController($conn);

$type = isset($_GET['type']) ? $_GET['type'] : "login";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'Login') {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $remember = $_POST['rememberMe'] ?? '';
        $result = $userController->login($email, $password, $remember);
        if ($result === true) {
            header("Location: homepage"); 
        } else {
            $error = "Invalid email or password.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'Register') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
        
        // Default Values
        $roles = "User";
        $bio = "No bio yet.";
        $createdTime = date('Y-m-d H:i:s');
        $result = $userController->register($username, $email, $password, $confirmPassword, $roles, $bio, $createdTime);
        if ($result === true) {
            $success = "Registration successful.";
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
    <title>Get started</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        input, p {
            font-size: 14px !important;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row d-flex justify-content-center min-vh-100 align-items-center">
            <div class="col-md-7 col-lg-4 col-10 col-xxl-3">
                <div class="bg-white px-4 py-3 px-md-5 shadow">
                    <?php if ($type == 'login'): ?>
                        <form method="POST" action="entry?type=login">
                            <h3 class="text-center mb-3 mt-2">Login</h3>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div class="mb-2 form-check">
                                <input type="checkbox" class="form-check-input" name="rememberMe" id="rememberMe">
                                <label class="form-check-label" for="rememberMe" style='font-size:14px;'>Remember Me</label>
                                <a href="#" class="text-decoration-none float-end" style='font-size:12px;'> Forgot Password?</a>
                            </div>
                            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?> 
                            
                            <div class="pt-3 d-grid gap-2 justify-content-center">
                                <input type="submit" name="action" value="Login" class="btn btn-primary">
                                <div class="text-center">
                                    <p>Don't have an account? <a href="entry?type=register" class="text-decoration-none">Sign up</a></p>
                                </div>
                            </div>
                        </form>
                    <?php elseif ($type == 'register'): ?>
                        <form method="POST" action="entry?type=register">
                            <h3 class="text-center mb-3 mt-2">Register</h3>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
                            </div>
                            <?php if (isset($success)) echo "<p class='text-success'>$success</p>"; ?> 
                            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?> 

                            <div class="pt-3 d-grid gap-2 justify-content-center">
                                <input type="submit" name="action" value="Register" class="btn btn-primary">
                                <div class="text-center">
                                    <p>Already have an account? <a href="entry?type=login" class="text-decoration-none">Login</a></p>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
