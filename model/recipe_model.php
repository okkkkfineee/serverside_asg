<?php

class Recipe {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRecipe($recipe_id) {
        $sql = "SELECT * FROM recipe WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getUserRecipes($user_id) {
        $sql = "SELECT * FROM recipe WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOwnRecipeInfo($recipe_id, $user_id) {
        $sql = "SELECT * FROM recipe WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recipe = $result->fetch_assoc();
        if (!$recipe) {
            die("Recipe not found!");
        }
    
        if ($recipe['user_id'] !== $user_id) {
            die("Unauthorized access!");
        }

        $stmt_steps = $this->conn->prepare("SELECT * FROM steps WHERE recipe_id = ? ORDER BY step_number ASC");
        $stmt_steps->bind_param("i", $recipe_id);
        $stmt_steps->execute();
        $steps_result = $stmt_steps->get_result();
        while ($row = $steps_result->fetch_assoc()) {
            $steps[] = $row['instruction'];
        }
    
        $recipe['steps'] = $steps;
        return $recipe;
    }

    public function manageRecipe($action, $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $steps, $created_time) {
        // Validate description (50 words max)
        if (str_word_count($description) > 50) {
            $result = "Description must not exceed 50 words.";
            return $result;
        }
    
        // Handle Image Upload (only jpg, jpeg, png)
        if (!empty($image)) {
            $target_dir = "../uploads/";
            $image_path = $target_dir . $image;
            $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
            if (!in_array($image_file_type, ['jpg', 'jpeg', 'png'])) {
                return "Only JPG, JPEG, and PNG files are allowed.";
            }
        
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                return "Error uploading image.";
            }
        }
    
        if ($action === 'add') {
            $stmt = $this->conn->prepare("INSERT INTO recipe (user_id, title, description, images, cuisine, difficulty, cooking_time, created_time) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssiis", $user_id, $title, $description, $image, $cuisine, $difficulty, $cooking_time, $created_time);
            
            if ($stmt->execute()) {
                $recipe_id = $stmt->insert_id;
        
                $stmt_steps = $this->conn->prepare("INSERT INTO steps (recipe_id, step_number, instruction) VALUES (?, ?, ?)");
                foreach ($steps as $index => $instruction) {
                    $step_number = $index + 1;
                    $stmt_steps->bind_param("iis", $recipe_id, $step_number, $instruction);
                    $stmt_steps->execute();
                }
                return true;
            } else {
                $result = "Error inserting recipe: " . $stmt->error;
                return $result;
            }
        } elseif ($action === 'update') {
            $stmt = $this->conn->prepare("UPDATE recipe SET title = ?, description = ?, images = ?, cuisine = ?, difficulty = ?, cooking_time = ? WHERE recipe_id = ?");
            $stmt->bind_param("sssiiii", $title, $description, $image, $cuisine, $difficulty, $cooking_time, $recipe_id);
        
            if ($stmt->execute()) {
                $existing_steps = [];
                $stmt_steps_select = $this->conn->prepare("SELECT step_number FROM steps WHERE recipe_id = ?");
                $stmt_steps_select->bind_param("i", $recipe_id);
                $stmt_steps_select->execute();
                $result = $stmt_steps_select->get_result();
                while ($row = $result->fetch_assoc()) {
                    $existing_steps[$row['step_number']] = true;
                }
        
                $stmt_update = $this->conn->prepare("UPDATE steps SET instruction = ? WHERE recipe_id = ? AND step_number = ?");
                $stmt_insert = $this->conn->prepare("INSERT INTO steps (recipe_id, step_number, instruction) VALUES (?, ?, ?)");
        
                foreach ($steps as $index => $instruction) {
                    $step_number = $index + 1;
                    $step_text = $instruction;
                
                    if (isset($existing_steps[$step_number])) {
                        $stmt_update->bind_param("sii", $step_text, $recipe_id, $step_number);
                        $stmt_update->execute();
                    } else {
                        $stmt_insert->bind_param("iis", $recipe_id, $step_number, $step_text);
                        $stmt_insert->execute();
                    }
                }
        
                $stmt_delete = $this->conn->prepare("DELETE FROM steps WHERE recipe_id = ? AND step_number > ?");
                $stmt_delete->bind_param("ii", $recipe_id, count($steps));
                $stmt_delete->execute();
        
                return true;
            } else {
                return "Error updating recipe: " . $stmt->error;
            }
        }
    }

    public function deleteRecipe($recipe_id) {
        $stmt = $this->conn->prepare("DELETE FROM recipe WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt = $this->conn->prepare("DELETE FROM steps WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        return true;
    }

    /**
     * Search recipes by title or description
     * @param string $search Search query
     * @param int|null $userId Optional user ID to filter by
     * @return array Array of recipe data
     */
    public function searchRecipes($search, $userId = null) {
        $search = '%' . $search . '%';
        
        $sql = "SELECT r.recipe_id, r.title, r.images, r.user_id, u.username 
                FROM recipe r 
                JOIN user u ON r.user_id = u.user_id 
                WHERE r.title LIKE ?";
        
        // Add user filter if specified
        $params = [$search];
        if ($userId !== null) {
            $sql .= " AND r.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY r.title ASC LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt) {
            // Bind parameters dynamically
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $recipes = [];
            while ($row = $result->fetch_assoc()) {
                $recipes[] = $row;
            }
            
            $stmt->close();
            return $recipes;
        } else {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
    }
}
?>