<?php

require_once '../model/recipe_model.php';

class RecipeController {
    private $recipeModel;

    public function __construct($db) {
        $this->recipeModel = new Recipe($db);
    }

    public function getRecipe($recipe_id) {
        return $this->recipeModel->getRecipe($recipe_id);
    }

    public function getAllRecipes() {
        return $this->recipeModel->getAllRecipes();
    }

    public function filterRecipes($title, $cuisine, $difficulty) {
        return $this->recipeModel->filterRecipes($title, $cuisine, $difficulty);
    }

    public function getUserRecipes($user_id) {
        return $this->recipeModel->getUserRecipes($user_id);
    }

    public function getOwnRecipeInfo($recipe_id, $user_id) {
        return $this->recipeModel->getOwnRecipeInfo($recipe_id, $user_id);
    }

    public function getRecipeInfo($recipe_id) {
        return $this->recipeModel->getRecipeInfo($recipe_id);
    }

    public function manageRecipe($action, $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $ingredients, $steps, $created_time) {
        return $this->recipeModel->manageRecipe($action, $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $ingredients, $steps, $created_time);
    }

    public function deleteRecipe($recipe_id) {
        return $this->recipeModel->deleteRecipe($recipe_id);
    }

    public function getMatchedRecipes($comp_theme, $user_id) {
        return $this->recipeModel->getMatchedRecipes($comp_theme, $user_id);
    }
}
?>