<?php

require_once '../model/user_model.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    // Handles login logic
    public function login($email, $password, $remember) {
        $user = $this->userModel->login($email, $password, $remember);
        if ($user) {
            // Successful login
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['roles'] = $user['roles'];  
            return true;
        } else {
            // Login failed
            return false;
        }
    }

    // Handles registration logic
    public function register($username, $email, $password, $confirmPassword, $roles, $bio, $createdTime) {
        return $this->userModel->register($username, $email, $password, $confirmPassword, $roles, $bio, $createdTime);
    }

    // Handles logout logic
    public function logout() {
        return $this->userModel->logout();
    }

    // Check if user is Superadmin
    public function isSuperadmin() {
        return $this->userModel->isSuperadmin();
    }

    // Check if user is Admin
    public function isAdmin() {
        return $this->userModel->isAdmin();
    }

    // Check if user is Mod
    public function isMod() {
        return $this->userModel->isMod();
    }

    //================ Admin Panel ================

    // Getting all user info
    public function getUserListPagination($filters, $offset, $limit) {
        return $this->userModel->getUserListPagination($filters, $offset, $limit);
    }

    //================ Profile ================

    // Getting user info by ID
    public function getUserInfo($id) {
        return $this->userModel->getUserInfo($id);
    }

}
?>