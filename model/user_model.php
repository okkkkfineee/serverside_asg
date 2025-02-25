<?php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login
    public function login($email, $password, $remember) {
        $sql = "SELECT * FROM user WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($remember) {
                $token = bin2hex(random_bytes(32));

                //Set cookies for 30 days ( 30 days * 24 hours * 60 minutes * 60 seconds )
                setcookie("login_token", $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
                setcookie("user_id", $user['user_id'], time() + (30 * 24 * 60 * 60), "/", "", false, true);
                setcookie("username", $user['username'], time() + (30 * 24 * 60 * 60), "/", "", false, true);
                setcookie("roles", $user['roles'], time() + (30 * 24 * 60 * 60), "/", "", false, true);
            }
            return $user; 
        } else {
            return false; 
        }
    }

    // Register
    public function register($username, $email, $password, $confirmPassword, $roles, $bio, $createdTime) {
        $sql = "SELECT * FROM user WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "Email or username already exists.";
        } else {
            if ($password !== $confirmPassword) {
                return "Passwords do not match.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO user (username, email, password, roles, bio, created_time) VALUES (?, ?, ?, ? ,? ,?)";
                $stmt = $this->conn->prepare($sql); 
                $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $roles, $bio, $createdTime);
                if ($stmt->execute()) {
                    return true;
                } else {
                    return "Error: " . $stmt->error;
                }
            }
        }
    }

    // Logout
    public function logout() {
        session_start();
        session_unset();
        session_destroy();

        //Delete Cookies
        setcookie("login_token", "", time() - 3600, "/", "", false, true);
        setcookie("user_id", "", time() - 3600, "/", "", false, true);
        setcookie("fullname", "", time() - 3600, "/", "", false, true);
        setcookie("username", "", time() - 3600, "/", "", false, true);
        setcookie("user_type", "", time() - 3600, "/", "", false, true);
        return true;
    }

    //================ Profile ================

    // Get user info by ID
    public function getUserInfo($id) {
        $sql = "SELECT * FROM user WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>