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

    public function getAllRecipes() {
        $sql = "SELECT * FROM recipe";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function filterRecipes($title, $cuisine, $difficulty) {
        $query = "SELECT * FROM recipe WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($title)) {
            $query .= " AND title LIKE ?";
            $params[] = "%$title%";
            $types .= "s";
        }
        if (!empty($cuisine)) {
            $query .= " AND cuisine = ?";
            $params[] = $cuisine;
            $types .= "s"; 
        }
        if (!empty($difficulty)) {
            $query .= " AND difficulty = ?";
            $params[] = $difficulty;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $recipes = [];
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }
        
        return $recipes;
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

        $stmt_ingredients = $this->conn->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
        $stmt_ingredients->bind_param("i", $recipe_id);
        $stmt_ingredients->execute();
        $ingredients_result = $stmt_ingredients->get_result();
        while ($row = $ingredients_result->fetch_assoc()) {
            $ingredients[] = $row['material'];
        }

        $stmt_steps = $this->conn->prepare("SELECT * FROM steps WHERE recipe_id = ? ORDER BY step_number ASC");
        $stmt_steps->bind_param("i", $recipe_id);
        $stmt_steps->execute();
        $steps_result = $stmt_steps->get_result();
        while ($row = $steps_result->fetch_assoc()) {
            $steps[] = $row['instruction'];
        }

        $recipe['ingredients'] = $ingredients;
        $recipe['steps'] = $steps;
        return $recipe;
    }


    public function getRecipeInfo($recipe_id) {
        $sql = "SELECT * FROM recipe WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recipe = $result->fetch_assoc();
        if (!$recipe) {
            die("Recipe not found!");
        }
    

        $stmt_ingredients = $this->conn->prepare("SELECT * FROM ingredients WHERE recipe_id = ?");
        $stmt_ingredients->bind_param("i", $recipe_id);
        $stmt_ingredients->execute();
        $ingredients_result = $stmt_ingredients->get_result();
        while ($row = $ingredients_result->fetch_assoc()) {
            $ingredients[] = $row['material'];
        }

        $stmt_steps = $this->conn->prepare("SELECT * FROM steps WHERE recipe_id = ? ORDER BY step_number ASC");
        $stmt_steps->bind_param("i", $recipe_id);
        $stmt_steps->execute();
        $steps_result = $stmt_steps->get_result();
        while ($row = $steps_result->fetch_assoc()) {
            $steps[] = $row['instruction'];
        }

        $recipe['ingredients'] = $ingredients;
        $recipe['steps'] = $steps;
        return $recipe;
    }

    public function manageRecipe($action, $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $ingredients, $steps, $created_time) {
        // Validate description (50 words max)
        if (str_word_count($description) > 50) {
            $result = "Description must not exceed 50 words.";
            return $result;
        }


        $target_dir = "../uploads/recipes/";

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
    
        if ($action === 'add') {
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadError = $_FILES['image']['error'];
        
            if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE || $_FILES['image']['size'] > $maxFileSize) {
                return "File size exceeds the maximum limit of 2MB.";
            }
        
            if ($uploadError !== UPLOAD_ERR_OK) {
                return "File upload error: " . $uploadError;
            }
        
            $image = $user_id . '_' . basename($_FILES["image"]["name"]);
            $image_file_type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $max_filename_length = 20;
            $image_name_without_extension = pathinfo($image, PATHINFO_FILENAME);
            if (strlen($image_name_without_extension) > $max_filename_length) {
                $image_name_without_extension = substr($image_name_without_extension, 0, $max_filename_length);
            }
            $image = $image_name_without_extension . '.' . $image_file_type;
            $image_path = $target_dir . $image;
        
            if (!in_array($image_file_type, $allowedExtensions)) {
                return "Only JPG, JPEG, and PNG files are allowed.";
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
                $uploadError = $_FILES['image']['error'];
            
                if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE || $_FILES['image']['size'] > $maxFileSize) {
                    return "File size exceeds the maximum limit of 2MB.";
                }
            
                if ($uploadError !== UPLOAD_ERR_OK) {
                    return "File upload error: " . $uploadError;
                }

                $image = $user_id . '_' . basename($_FILES["image"]["name"]);
                $image_file_type = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                $max_filename_length = 20;
                $image_name_without_extension = pathinfo($image, PATHINFO_FILENAME);
                if (strlen($image_name_without_extension) > $max_filename_length) {
                    $image_name_without_extension = substr($image_name_without_extension, 0, $max_filename_length);
                }
                $image = $image_name_without_extension . '.' . $image_file_type;
                $image_path = $target_dir . $image;
            
                if (!in_array($image_file_type, $allowedExtensions)) {
                    return "Only JPG, JPEG, and PNG files are allowed.";
                }
            
                // Delete old image
                if (!empty($old_image)) {
                    $oldFilePath = $target_dir . $old_image;
                    if (file_exists($oldFilePath) && !is_dir($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                    return "Error uploading image.";
                }
            } else {
                $image = $old_image;
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

    public function deleteRecipe($recipe_id) {
        $stmt = $this->conn->prepare("SELECT images FROM recipe WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->bind_result($images);
        $stmt->fetch();
        $stmt->close();
    
        if (!empty($images)) {
            $image_path = "uploads/recipes/" . $images;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM recipe WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        return true;
    }

    public function getMatchedRecipes($comp_theme, $user_id) {
        $cuisine = ['Any', 'Chinese', 'Indian', 'Japanese', 'Malay', 'Thai', 'Western'];
        $cooking_time = ['Under 15 Minutes', 'Under 30 Minutes', 'Under 1 Hour', 'Slow Cooked'];
        $difficulty = [
            'Beginner-Friendly' => 1,
            'Easy' => 2,
            'Moderate' => 3,
            'Challenging' => 4,
            'Expert-Level' => 5
        ];
    
        if (in_array($comp_theme, $cuisine)) {
            if ($comp_theme === 'Any') {
                $sql = "SELECT * FROM recipe WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
            } else {
                $sql = "SELECT * FROM recipe WHERE cuisine = ? AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $comp_theme, $user_id);
            }
        } elseif (in_array($comp_theme, $cooking_time)) {
            if ($comp_theme === 'Under 15 Minutes') {
                $sql = "SELECT * FROM recipe WHERE cooking_time < 15 AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
            } elseif ($comp_theme === 'Under 30 Minutes') {
                $sql = "SELECT * FROM recipe WHERE cooking_time < 30 AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
            } elseif ($comp_theme === 'Under 1 Hour') {
                $sql = "SELECT * FROM recipe WHERE cooking_time < 60 AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
            } elseif ($comp_theme === 'Slow Cooked') {
                $sql = "SELECT * FROM recipe WHERE cooking_time >= 60 AND user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
            }
        } elseif (array_key_exists($comp_theme, $difficulty)) {
            $level = $difficulty[$comp_theme];
            $sql = "SELECT * FROM recipe WHERE difficulty = ? AND user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $level, $user_id);
        } else {
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
}
?>