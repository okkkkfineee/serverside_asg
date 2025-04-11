<?php

require_once '../model/meal_planning_model.php';

class MealPlanningController {
    private $mealPlanningModel;

    public function __construct($db) {
        $this->mealPlanningModel = new MealPlannig($db);
    }

}
?>