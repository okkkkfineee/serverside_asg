<?php

require_once '../includes/phpmailer_load.php';
require_once __DIR__ . '/../src/env_load.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    // Change/reset Password
    function changePassword($action, $user_id, $old_password, $new_password, $confirm_password, $token) {
        if ($action === "change") {
            $sql = "SELECT password FROM user WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
    
            if (password_verify($old_password, $user['password'])) {
                if ($new_password === $confirm_password) {
                    $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
                    $sql = "UPDATE user SET password = ? WHERE user_id = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ss", $hashedPassword, $user_id);
                    if ($stmt->execute()) {
                        return true;
                    } else {
                        return "Error: Please try again later. ";
                    }
                } else {
                    return "Passwords does not match.";
                }
            } else{
                return "Old password does not match.";
            }
        } else if ($action === "reset") {
            $query = mysqli_query($this->conn, "SELECT * FROM forget_pass_token WHERE token='$token' LIMIT 1");
            if (mysqli_num_rows($query) == 1) {
                $row = mysqli_fetch_assoc($query);
                $user_id = $row['user_id'];
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    mysqli_query($this->conn, "UPDATE user SET password='$hashed_password' WHERE user_id='$user_id'");
                    mysqli_query($this->conn, "DELETE FROM forget_pass_token WHERE user_id='$user_id'");
                    return true;
                } else {
                    return "Your new password and confirm password do not match.";
                }
            } else {
                return "Invalid or expired token.";
            }
        }
    }

    // Forgot Password Request
    public function requestForgotPassword($email) {
        $check_user = mysqli_query($this->conn, "SELECT * FROM user WHERE email='$email'");

        if (mysqli_num_rows($check_user) > 0) {
            $env = loadEnv(__DIR__ . '/../src/.env');
            
            $token = bin2hex(random_bytes(50));
            $expires = time() + 1800;  //30 minutes
            $row = mysqli_fetch_assoc($check_user);
            $user_id = $row['user_id'];
            mysqli_query($this->conn, "INSERT INTO forget_pass_token (user_id, email, token) VALUES ('$user_id', '$email', '$token')");
            
            $encodedToken = urlencode($token);
            $encodedExpires = urlencode($expires);

            $resetUrl = "http://localhost/serverside_asg/view/change_password?token={$encodedToken}&expires={$encodedExpires}";
            
            $mail = new PHPMailer(true);

            $mail->isSMTP(); 
            $mail->Host = $env['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $env['MAIL_USERNAME']; // SMTP username
            $mail->Password = $env['MAIL_PASSWORD']; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = $env['MAIL_PORT']; 
        
            //Recipients
            $mail->setFrom($env['MAIL_USERNAME'], 'Admin'); // Sender's email and name
            $mail->addAddress($email, 'User'); 
        
            //Content
            $mail->isHTML(true); 
            $htmlContent = file_get_contents('forget_pass_html.html');

            $htmlContent = str_replace('{{resetLink}}', $resetUrl, $htmlContent);
        
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = $htmlContent;
            $mail->AltBody = 'To reset your password, click the link below: ' . $resetUrl; 
            
            if($mail->send()) {
                return true;
            } else {
                return "Failed to send email.";
            }
        } else {
            echo "<p>Email not found.</p>";
        }
    }

    // Check if user is Superadmin
    public function isSuperadmin() {
        return isset($_SESSION['roles']) && $_SESSION['roles'] === 'Superadmin';
    }

    // Check if user is Admin
    public function isAdmin() {
        return isset($_SESSION['roles']) && $_SESSION['roles'] === 'Admin';
    }

    // Check if user is Mod
    public function isMod() {
        return isset($_SESSION['roles']) && $_SESSION['roles'] === 'Mod';
    }

    //================ Admin Panel ================

    // Get user list with pagination
    public function getUserListPagination($filters, $offset, $limit) {
        $query = "SELECT * FROM user WHERE 1=1";
        $totalCountQuery = "SELECT COUNT(*) AS total FROM user WHERE 1=1";
        $params = [];
        $types = ''; 
        
        // Apply filters
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'ss';
            $query .= " AND (user_id LIKE ? OR username LIKE ?)";
            $totalCountQuery .= " AND (user_id LIKE ? OR username LIKE ?)";
        }
        
        $typeFilters = [];
        foreach (['isSuperadmin' => 'Superadmin', 'isAdmin' => 'Admin', 'isMod' => 'Mod', 'isUser' => 'User'] as $key => $value) {
            if (!empty($filters[$key])) {
                $typeFilters[] = $value;
            }
        }
        
        if (!empty($typeFilters)) {
            $query .= " AND roles IN (" . str_repeat('?,', count($typeFilters) - 1) . '?)';
            $totalCountQuery .= " AND roles IN (" . str_repeat('?,', count($typeFilters) - 1) . '?)';
            $params = array_merge($params, $typeFilters);
            $types .= str_repeat('s', count($typeFilters));
        }
        
        $stmtTotalCount = $this->conn->prepare($totalCountQuery);
        if ($stmtTotalCount === false) {
            die('Prepare failed: ' . $this->conn->error);
        }
        if (!empty($params)) {
            $stmtTotalCount->bind_param($types, ...$params);
        }
        $stmtTotalCount->execute();
        $totalResult = $stmtTotalCount->get_result();
        $totalRecords = $totalResult->fetch_assoc()['total'];
        
        $query .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die('Prepare failed: ' . $this->conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        return ['data' => $data, 'total' => $totalRecords];
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