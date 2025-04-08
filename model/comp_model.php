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

    public function manageComp($action, $comp_id, $comp_title, $comp_image, $comp_desc, $comp_prize, $requirement, $start_date, $end_date) {
        // Validate description (100 words max)
        if (str_word_count($comp_desc) > 100) {
            $result = "Description must not exceed 100 words.";
            return $result;
        }

        $target_dir = "../uploads/comp";
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
    
        if ($action === 'host') {
        // Handle Image Upload
        if (!empty($image)) {
            $image_path = $target_dir . $image;
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

                $stmt_ingredients = $this->conn->prepare("INSERT INTO ingredients (recipe_id, ingredient_num, material) VALUES (?, ?, ?)");
                foreach ($ingredients as $index => $material) {
                    $ingredient_num = $index + 1;
                    $stmt_ingredients->bind_param("iis", $recipe_id, $ingredient_num, $material);
                    $stmt_ingredients->execute();
                }
                return true;
            } else {
                $result = "Error inserting recipe: " . $stmt->error;
                return $result;
            }
        } elseif ($action === 'update') {
            //Retrieve old image before update
            $oldRecipe = $this->getRecipe($recipe_id);
            $old_image = $oldRecipe ? $oldRecipe['images'] : null;
            // Handle Image Upload (only jpg, jpeg, png)
            if (!empty($_FILES["image"]["name"])) {
                $image = $user_id . '_' . basename($_FILES["image"]["name"]);
                $image_path = $target_dir . $image;
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

            $description = stripslashes($description);
            $stmt = $this->conn->prepare("UPDATE recipe SET title = ?, description = ?, images = ?, cuisine = ?, difficulty = ?, cooking_time = ? WHERE recipe_id = ?");
            $stmt->bind_param("ssssiii", $title, $description, $image, $cuisine, $difficulty, $cooking_time, $recipe_id);
        
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

                $existing_ingredients = [];
                $stmt_ingredients_select = $this->conn->prepare("SELECT ingredient_num FROM ingredients WHERE recipe_id = ?");
                $stmt_ingredients_select->bind_param("i", $recipe_id);
                $stmt_ingredients_select->execute();
                $result = $stmt_ingredients_select->get_result();
                while ($row = $result->fetch_assoc()) {
                    $existing_ingredients[$row['ingredient_num']] = true;
                }

                $stmt_update_ingredients = $this->conn->prepare("UPDATE ingredients SET material = ? WHERE recipe_id = ? AND ingredient_num = ?");
                $stmt_insert_ingredients = $this->conn->prepare("INSERT INTO ingredients (recipe_id, ingredient_num, material) VALUES (?, ?, ?)");

                foreach ($ingredients as $index => $material) {
                    $ingredient_num = $index + 1;
                    if (isset($existing_ingredients[$ingredient_num])) {
                        $stmt_update_ingredients->bind_param("sii", $material, $recipe_id, $ingredient_num);
                        $stmt_update_ingredients->execute();
                    } else {
                        $stmt_insert_ingredients->bind_param("iis", $recipe_id, $ingredient_num, $material);
                        $stmt_insert_ingredients->execute();
                    }
                }

                $steps_count = count($steps);
                $ingredients_count = count($ingredients);
            
                $stmt_delete = $this->conn->prepare("DELETE FROM steps WHERE recipe_id = ? AND step_number > ?");
                $stmt_delete->bind_param("ii", $recipe_id, $steps_count);
                $stmt_delete->execute();

                $stmt_delete_ingredients = $this->conn->prepare("DELETE FROM ingredients WHERE recipe_id = ? AND ingredient_num > ?");
                $stmt_delete_ingredients->bind_param("ii", $recipe_id, $ingredients_count);
                $stmt_delete_ingredients->execute();
        
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
            $image_path = "uploads/comp" . $images;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM competition WHERE comp_id = ?");
        $stmt->bind_param("i", $comp_id);
        $stmt->execute();
        return true;
    }
}
?>