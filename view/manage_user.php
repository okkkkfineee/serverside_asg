<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/user_controller.php';

$userController = new UserController($conn);

if (!$userController->isSuperadmin() && !$userController->isAdmin()) {
    header("Location: ../view/homepage.php");
    exit();
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $user = $userController->getUserInfo($user_id);
    if (!$user) {
        header("Location: ../view/admin_panel?page=1");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_GET['user_id'];
    $current_user_id = $_SESSION['user_id'];
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $roles = $_POST['role'];
        $result = $userController->updateUser("update", $user_id, $roles, $current_user_id);
        if ($result === true) {
            echo "<script>alert('User updated successfully!');
            window.location.href = 'admin_panel';</script>";
        } else {
            $error = $result;
            echo "<script>alert('$error');
            window.location.href = 'admin_panel';</script>";
        }
    } else if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $result = $userController->updateUser("delete", $user_id, "", $current_user_id);
        if ($result === true) {
            echo "<script>alert('User deleted successfully!');
            window.location.href = 'admin_panel';</script>";
        } else {
            $error = $result;
            echo "<script>alert('$error');
            window.location.href = 'admin_panel';</script>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User <?php echo $user_id;?></title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="bg-white rounded-3 p-5" style="width: 50%;">
            <form action="manage_user?user_id=<?php echo $user['user_id']; ?>" method="POST">
                <table class="table table-borderless">
                    <tr>
                        <td><label for="user_id" class="form-label">User ID</label></td>
                        <td><input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo $user['user_id']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td><label for="username" class="form-label">Username</label></td>
                        <td><input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td><label for="email" class="form-label">Email</label></td>
                        <td><input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td><label for="role" class="form-label">Role</label></td>
                        <td>
                            <select class="form-select" id="role" name="role">
                                <option <?php echo $user['roles'] === 'Superadmin' ? 'selected' : ''; ?> value="Superadmin">Superadmin</option>
                                <option <?php echo $user['roles'] === 'Admin' ? 'selected' : ''; ?> value="Admin">Admin</option>
                                <option <?php echo $user['roles'] === 'Mod' ? 'selected' : ''; ?> value="Mod">Mod</option>
                                <option <?php echo $user['roles'] === 'User' ? 'selected' : ''; ?> value="User">User</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn" style="background-color: red !important;" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</button>
                    <div>
                        <button type="submit" class="btn me-2" name="action" value="update">Update</button>
                        <a href="admin_panel?page=1" class="btn btn-secondary">Discard</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
