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
        $comp = $result->fetch_assoc();
        if (!$comp) {
            die("Competition not found!");
        }

        $stmt_prizes = $this->conn->prepare("SELECT * FROM competition_prize WHERE comp_id = ? ORDER BY prize_num ASC");
        $stmt_prizes->bind_param("i", $comp_id);
        $stmt_prizes->execute();
        $prizes_result = $stmt_prizes->get_result();
        while ($row = $prizes_result->fetch_assoc()) {
            $prize[] = $row['prize_desc'];
        }

        $comp['prizes'] = $prize;
        return $comp;
    }

    // Get all competition info
    public function getAllComp() {
        $sql = "SELECT * FROM competition ORDER BY comp_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all competition info with fileters
    public function getAllCompWithFilters($filters) {
        $sql = "SELECT * FROM competition WHERE 1";
        $params = [];
    
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND comp_title LIKE ?";
            $params[] = "%" . $filters['search'] . "%";
        }
    
        // Theme filter
        if (!empty($filters['theme'])) {
            $sql .= " AND comp_theme = ?";
            $params[] = $filters['theme'];
        }
    
        // Status filter
        if (!empty($filters['status'])) {
            $statuses = $filters['status'];
            $statusConditions = [];
    
            $today = date('Y-m-d');
    
            foreach ($statuses as $i => $status) {
                if ($status === 'ongoing') {
                    $statusConditions[] = "(end_date > ?)";
                    $params[] = $today;
                } elseif ($status === 'voting') {
                    $statusConditions[] = "(end_date <= ? AND DATEDIFF(?, end_date) <= 10)";
                    $params[] = $today;
                    $params[] = $today;
                } elseif ($status === 'ended') {
                    $statusConditions[] = "(DATEDIFF(?, end_date) > 10)";
                    $params[] = $today;
                }
            }
    
            if (!empty($statusConditions)) {
                $sql .= " AND (" . implode(" OR ", $statusConditions) . ")";
            }
        }
    
        $sql .= " ORDER BY comp_id DESC";
    
        $stmt = $this->conn->prepare($sql);
        if (count($params) > 0) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
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

        $target_dir = "../uploads/comp/";
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
    
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

            $stmt = $this->conn->prepare("INSERT INTO competition (comp_title, comp_image, comp_desc, comp_theme, start_date, end_date) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $comp_title, $comp_image, $comp_desc, $comp_theme, $start_date, $end_date);
            if ($stmt->execute()) {
                $comp_id = $stmt->insert_id;
                $prizes = array_values($comp_prize);
                $stmt_prize = $this->conn->prepare("INSERT INTO competition_prize (comp_id, prize_num, prize_desc) VALUES (?, ?, ?)");
                foreach ($prizes as $index => $prize_desc) {
                    if (empty(trim($prize_desc))) {
                        continue;
                    }
                    $prize_num = $index + 1;
                    $stmt_prize->bind_param("iis", $comp_id, $prize_num, $prize_desc);
                    $stmt_prize->execute();
                }
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
            $stmt = $this->conn->prepare("UPDATE competition SET comp_title = ?, comp_image = ?, comp_desc = ?, comp_theme = ?, start_date = ?, end_date = ? WHERE comp_id = ?");
            $stmt->bind_param("ssssssi", $comp_title, $comp_image, $comp_desc, $comp_theme, $start_date, $end_date, $comp_id);
        
            if ($stmt->execute()) {
                $existing_prize = [];
                $stmt_prize_select = $this->conn->prepare("SELECT prize_num FROM competition_prize WHERE comp_id = ?");
                $stmt_prize_select->bind_param("i", $comp_id);
                $stmt_prize_select->execute();
                $result = $stmt_prize_select->get_result();
                while ($row = $result->fetch_assoc()) {
                    $existing_prize[$row['prize_num']] = true;
                }
            
                $prizes = array_values($comp_prize);
                $stmt_update = $this->conn->prepare("UPDATE competition_prize SET prize_desc = ? WHERE comp_id = ? AND prize_num = ?");
                $stmt_insert = $this->conn->prepare("INSERT INTO competition_prize (comp_id, prize_num, prize_desc) VALUES (?, ?, ?)");
            
                foreach ($prizes as $index => $prize_desc) {
                    if (empty(trim($prize_desc))) continue;
            
                    $prize_num = $index + 1;
            
                    if (isset($existing_prize[$prize_num])) {
                        $stmt_update->bind_param("sii", $prize_desc, $comp_id, $prize_num);
                        $stmt_update->execute();
                    } else {
                        $stmt_insert->bind_param("iis", $comp_id, $prize_num, $prize_desc);
                        $stmt_insert->execute();
                    }
                }
            
                $prizes_count = count($prizes);
                $stmt_delete = $this->conn->prepare("DELETE FROM competition_prize WHERE comp_id = ? AND prize_num > ?");
                $stmt_delete->bind_param("ii", $comp_id, $prizes_count);
                $stmt_delete->execute();
            
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
        $sql = "SELECT r.*, ce.*, u.username, COUNT(cv.vote_id) AS vote_count
                FROM competition_entry ce
                JOIN recipe r ON ce.recipe_id = r.recipe_id
                JOIN user u ON r.user_id = u.user_id
                LEFT JOIN competition_vote cv ON ce.entry_id = cv.entry_id
                WHERE ce.comp_id = ?
                GROUP BY ce.entry_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    public function checkEntry($comp_id, $user_id) {
        $sql = "SELECT * FROM competition_entry WHERE comp_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $comp_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function submitEntry($comp_id, $user_id, $selected_recipe_id){
        $sql = "INSERT INTO competition_entry (comp_id, user_id, recipe_id) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $_GET['comp_id'], $_SESSION['user_id'], $selected_recipe_id);
        $stmt->execute();
        $stmt->close();
        return true; 
    }

    //================ Competition Voting ================
    public function voteRecipe($entry_id, $user_id){
        $sql = "SELECT * FROM competition_entry WHERE entry_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $entry_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return "You cannot vote for your own entry.";
        }
        $sql = "SELECT * FROM competition_vote WHERE entry_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $entry_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return "You have already voted for this entry.";
        }
        $sql = "INSERT INTO competition_vote (entry_id, user_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $entry_id, $user_id);
        $stmt->execute();
        $stmt->close();
        return true;
    }
}
?>