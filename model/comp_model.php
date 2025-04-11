<?php

class Competition {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get the competition info
    public function getComp($comp_id) {
        $sql = "SELECT * FROM competition WHERE comp_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get all competition info
    public function getAllComp() {
        $sql = "SELECT * FROM competition ORDER BY comp_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all competition info user joined
    public function getUserComp($user_id) {
        $sql = "SELECT comp_entry.*, comp.* FROM competition_entry comp_entry 
               JOIN competition comp ON comp_entry.comp_id = comp.comp_id 
               WHERE comp_entry.user_id = ? 
               ORDER BY comp_entry.comp_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function manageComp($action, $comp_id, $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date) {
        // Validate description (100 words max)
        if (str_word_count($comp_desc) > 100) {
            $result = "Description must not exceed 100 words.";
            return $result;
        }

        // Validate description (50 words max)
        if (str_word_count($comp_prize) > 50) {
            $result = "Description must not exceed 50 words.";
            return $result;
        }

        $target_dir = "../uploads/comp/";
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB
    
        if ($action === 'host') {
            // Handle Image Upload
            if (!empty($comp_image)) {
                $image_path = $target_dir . $comp_image;
                $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
                if (!in_array($image_file_type, $allowedExtensions)) {
                    return "Only JPG, JPEG, and PNG files are allowed.";
                }

                if ($_FILES['image']['size'] > $maxFileSize) {
                    return "File size exceeds the maximum limit of 2MB.";
                }

                if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                    return "File upload error: " . $_FILES["image"]["error"];
                }
            
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                    return "Error uploading image.";
                }
            }

            $stmt = $this->conn->prepare("INSERT INTO competition (comp_title, comp_image, comp_desc, comp_prize, comp_theme, start_date, end_date) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date);
            if ($stmt->execute()) {
                return true;
            } else {
                $result = "Error hosting competition: " . $stmt->error;
                return $result;
            }
        } elseif ($action === 'update') {
            //Retrieve old image before update
            $oldRecipe = $this->getComp($comp_id);
            $old_image = $oldRecipe ? $oldRecipe['comp_image'] : null;
            // Handle Image Upload (only jpg, jpeg, png)
            if (!empty($_FILES["image"]["name"])) {
                $comp_image = $user_id . '_' . basename($_FILES["image"]["name"]);
                $image_path = $target_dir . $comp_image;
                $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

                if (!in_array($image_file_type, ['jpg', 'jpeg', 'png'])) {
                    return "Only JPG, JPEG, and PNG files are allowed.";
                }

                if ($_FILES['image']['size'] > $maxFileSize) {
                    return "File size exceeds the maximum limit of 2MB.";
                }

                if (!empty($old_image)) {
                    $oldFilePath = $target_dir . $old_image;
                    if (file_exists($oldFilePath) && !is_dir($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            
                // Check for upload errors
                if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
                    return "File upload error: " . $_FILES["image"]["error"];
                }

                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                    return "Error uploading image.";
                }
            }

            $comp_desc = stripslashes($comp_desc);
            $stmt = $this->conn->prepare("UPDATE competition SET comp_title = ?, comp_image = ?, comp_desc = ?, comp_prize = ?, comp_theme = ?, start_date = ?, end_date = ? WHERE comp_id = ?");
            $stmt->bind_param("sssssssi", $comp_title, $comp_image, $comp_desc, $comp_prize, $comp_theme, $start_date, $end_date, $comp_id);
        
            if ($stmt->execute()) {
                return true;
            } else {
                return "Error updating recipe: " . $stmt->error;
            }
        }
    }

    // Delete competition
    public function deleteComp($comp_id) {
        $stmt = $this->conn->prepare("SELECT comp_image FROM competition WHERE comp_id = ?");
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        $stmt->bind_result($images);
        $stmt->fetch();
        $stmt->close();
    
        if (!empty($images)) {
            $image_path = "../uploads/comp/" . $images;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM competition WHERE comp_id = ?");
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        return true;
    }

    //================ Competition Entries ================

    public function getAllEntries($comp_id) {
        $sql = "SELECT r.* FROM competition_entry ce
                INNER JOIN recipe r ON ce.recipe_id = r.recipe_id
                WHERE ce.comp_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>