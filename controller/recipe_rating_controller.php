<?php
require_once '../model/recipe_rating_model.php';

class RecipeRatingController {
    private $ratingModel;

    public function __construct($db) {
        $this->ratingModel = new RecipeRating($db);
    }
    
    public function getAverageRating($recipeId) {
        return $this->ratingModel->getAverageRating($recipeId);
    }
    
    public function getUserRating($userId, $recipeId) {
        return $this->ratingModel->getUserRating($userId, $recipeId);
    }
    
    public function addRating($recipeId, $userId, $ratingValue) {
        if ($ratingValue < 1 || $ratingValue > 5) {
            return "Rating must be between 1 and 5";
        }
        $createdTime = date('Y-m-d H:i:s');
        $result = $this->ratingModel->addRating($userId, $recipeId, $ratingValue, $createdTime);
        return $result ? true : "Failed to add rating: ";
    }
    
    public function deleteRating($userId, $recipeId) {
        $result = $this->ratingModel->deleteRating($userId, $recipeId);
        return $result ? true : "Failed to delete rating: ";
    }
}
?>