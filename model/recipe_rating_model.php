<?php
class RecipeRating {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAverageRating($recipeId) {
        $sql = "SELECT AVG(rating_value) as avg_rating, COUNT(*) as rating_count 
                FROM recipe_ratings WHERE recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return ['avg_rating' => null, 'rating_count' => 0];
        }
        $stmt->bind_param("i", $recipeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: ['avg_rating' => null, 'rating_count' => 0];
    }

    public function getUserRating($userId, $recipeId) {
        $sql = "SELECT rating_value FROM recipe_ratings WHERE user_id = ? AND recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("ii", $userId, $recipeId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['rating_value'] : null;
    }

    public function addRating($userId, $recipeId, $ratingValue, $createdTime) {
        $sql = "INSERT INTO recipe_ratings (user_id, recipe_id, rating_value, created_time) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE rating_value = ?, created_time = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("iiisis", $userId, $recipeId, $ratingValue, $createdTime, $ratingValue, $createdTime);
        return $stmt->execute();
    }

    public function deleteRating($userId, $recipeId) {
        $sql = "DELETE FROM recipe_ratings WHERE user_id = ? AND recipe_id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ii", $userId, $recipeId);
        return $stmt->execute();
    }
}
?>