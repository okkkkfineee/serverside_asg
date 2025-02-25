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

    public function getUserRecipes($user_id) {
        return $this->recipeModel->getUserRecipes($user_id);
    }

    public function getOwnRecipeInfo($recipe_id, $user_id) {
        return $this->recipeModel->getOwnRecipeInfo($recipe_id, $user_id);
    }

    public function manageRecipe($action, $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $steps, $created_time) {
        return $this->recipeModel->manageRecipe($action, $recipe_id, $user_id, $title, $image, $description, $cuisine, $difficulty, $cooking_time, $steps, $created_time);
    }

    public function deleteRecipe($recipe_id) {
        return $this->recipeModel->deleteRecipe($recipe_id);
    }
}
?>