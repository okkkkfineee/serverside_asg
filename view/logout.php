<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/user_controller.php';

$userController = new UserController($conn);

if ($userController->logout()) {
    echo "<script>alert('Logout Successfully!');
    window.location.href = 'homepage';</script>";
    exit(); 
}
?>
