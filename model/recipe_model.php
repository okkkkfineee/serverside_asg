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

                $existing_ingredients = [];
                $stmt_ingredients_select = $this->conn->prepare("SELECT ingredient_num FROM ingredients WHERE recipe_id = ?");
                $stmt_ingredients_select->bind_param("i", $recipe_id);
                $stmt_ingredients_select->execute();
                $result = $stmt_ingredients_select->get_result();
                while ($row = $result->fetch_assoc()) {
                    $existing_ingredients[$row['ingredient_num']] = true;
                }

                $stmt_update_ingredients = $this->conn->prepare("UPDATE ingredients SET material = ? WHERE recipe_id = ? AND ingredientl_num = ?");
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
            
                $stmt_delete = $this->conn->prepare("DELETE FROM steps WHERE recipe_id = ? AND step_number > ?");
                $stmt_delete->bind_param("ii", $recipe_id, count($steps));
                $stmt_delete->execute();

                $stmt_delete_ingredients = $this->conn->prepare("DELETE FROM ingredients WHERE recipe_id = ? AND ingredient_num > ?");
                $stmt_delete_ingredients->bind_param("ii", $recipe_id, count($ingredients));
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
            $image_path = "uploads/" . $images;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM recipe WHERE recipe_id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        return true;
    }
}
?>