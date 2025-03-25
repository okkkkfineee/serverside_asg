<?php

if (isset($_SESSION['user_id']) && isset($_SESSION['roles'])) {
    return;
} elseif (isset($_COOKIE['login_token'])) {
    $token = $_COOKIE['login_token'];

    if (strlen($token) === 64) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['roles'] = $_COOKIE['roles'];
        return;
    } else {
        header("Location: ../view/entry?type=login");
        exit();
    }
} else {
    header("Location: ../view/entry?type=login");
    exit();
}
?>
