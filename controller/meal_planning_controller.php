<?php

require_once '../model/meal_planning_model.php';

class MealPlanningController {
    private $mealPlanningModel;

    public function __construct($db) {
        $this->mealPlanningModel = new MealPlanning($db);
    }

    public function createMealPlan($recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date) {
        return $this->mealPlanningModel->createMealPlan($recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date);
    }

    public function getUserMealPlans($user_id) {
        return $this->mealPlanningModel->getUserMealPlans($user_id);
    }

    public function getMealPlanById($plan_id) {
        return $this->mealPlanningModel->getMealPlanById($plan_id);
    }
    
    public function updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $meal_time, $meal_date) {
        return $this->mealPlanningModel->updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $meal_time, $meal_date);
    }
    
    public function deleteMealPlan($plan_id) {
        return $this->mealPlanningModel->deleteMealPlan($plan_id);
    }
    
    public function getMealPlansByCategory($user_id, $meal_category) {
        return $this->mealPlanningModel->getMealPlansByCategory($user_id, $meal_category);
    }
}
?>