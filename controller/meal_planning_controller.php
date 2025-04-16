<?php

require_once '../model/meal_planning_model.php';

class MealPlanningController {
    private $model;
    private $categoryTimeRanges = [
        'Breakfast' => ['start' => '05:00', 'end' => '11:30'],
        'Lunch' => ['start' => '11:30', 'end' => '16:30'],
        'Dinner' => ['start' => '15:30', 'end' => '22:00']
    ];
    private $adminRoles = ['Superadmin', 'Admin', 'Mod'];

    public function __construct($db) {
        $this->model = new MealPlanning($db);
    }

    // Validate meal time based on category
    private function validateMealTime($meal_category, $meal_time) {
        // Allow Snacks at any time
        if ($meal_category === 'Snacks') {
            return true;
        }

        if (!isset($this->categoryTimeRanges[$meal_category])) {
            return true;
        }

        $range = $this->categoryTimeRanges[$meal_category];
        list($hours, $minutes) = explode(':', $meal_time);
        $timeInMinutes = ($hours * 60) + $minutes;

        list($startHours, $startMinutes) = explode(':', $range['start']);
        $startInMinutes = ($startHours * 60) + $startMinutes;

        list($endHours, $endMinutes) = explode(':', $range['end']);
        $endInMinutes = ($endHours * 60) + $endMinutes;

        return $timeInMinutes >= $startInMinutes && $timeInMinutes <= $endInMinutes;
    }

    // Create a new meal plan
    public function createMealPlan($recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date) {
        // Validate meal date is not in the past
        $current_date = date('Y-m-d');
        if ($meal_date < $current_date) {
            $_SESSION['error_message'] = "Cannot create meal plan for past dates.";
            return false;
        }

        // Validate meal time based on category
        if (!$this->validateMealTime($meal_category, $meal_time)) {
            $timeRangeMsg = isset($this->categoryTimeRanges[$meal_category])
                            ? " must be between " . $this->categoryTimeRanges[$meal_category]['start'] . " and " . $this->categoryTimeRanges[$meal_category]['end']
                            : ".";
            $_SESSION['error_message'] = "Invalid meal time for selected category. " . $meal_category . $timeRangeMsg;
            return false;
        }

        // Convert time HH:MM to total minutes from midnight for storage
        list($hours, $minutes) = explode(':', $meal_time);
        $timeInMinutes = ($hours * 60) + $minutes;

        return $this->model->createMealPlan($recipe_id, $user_id, $plan_name, $meal_category, $timeInMinutes, $meal_date);
    }

    // Update a meal plan
    public function updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $meal_time, $meal_date) {
        // Basic permission check: Is the user the owner or an admin?
        $plan = $this->getMealPlanById($plan_id);
        if (!$plan) {
            return ['success' => false, 'message' => 'Meal plan not found.'];
        }

        $isOwner = isset($_SESSION['user_id']) && $plan['user_id'] == $_SESSION['user_id'];
        $isAdmin = isset($_SESSION['roles']) && in_array($_SESSION['roles'], $this->adminRoles);

        if (!$isOwner && !$isAdmin) {
            return ['success' => false, 'message' => 'You do not have permission to update this meal plan.'];
        }

        // Validate meal date is not in the past
        $current_date = date('Y-m-d');
        if ($meal_date < $current_date) {
            return ['success' => false, 'message' => 'Cannot update meal plan to past dates.'];
        }

        // Validate meal time based on category
        if (!$this->validateMealTime($meal_category, $meal_time)) {
            $timeRangeMsg = isset($this->categoryTimeRanges[$meal_category])
                            ? " must be between " . $this->categoryTimeRanges[$meal_category]['start'] . " and " . $this->categoryTimeRanges[$meal_category]['end']
                            : ".";
            return ['success' => false, 'message' => "Invalid meal time for selected category. " . $meal_category . $timeRangeMsg];
        }

        // Convert time HH:MM to total minutes from midnight for storage
        list($hours, $minutes) = explode(':', $meal_time);
        $timeInMinutes = ($hours * 60) + $minutes;

        $result = $this->model->updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $timeInMinutes, $meal_date);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Meal plan updated successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update meal plan.'
            ];
        }
    }

    // Get category time ranges
    public function getCategoryTimeRanges() {
        return $this->categoryTimeRanges;
    }

    public function getUserMealPlans($user_id) {
        return $this->model->getUserMealPlans($user_id);
    }

    public function getMealPlanById($plan_id) {
        $result = $this->model->getMealPlanById($plan_id);
        if (!$result) {
            return null;
        }
        return $result;
    }
    
    public function deleteMealPlan($plan_id) {
        $plan = $this->getMealPlanById($plan_id);
        if (!$plan) {
            $_SESSION['error_message'] = "Meal plan not found.";
            return false;
        }

        $isOwner = isset($_SESSION['user_id']) && $plan['user_id'] == $_SESSION['user_id'];
        $isAdmin = isset($_SESSION['roles']) && in_array($_SESSION['roles'], $this->adminRoles);

        if ($isOwner || $isAdmin) {
            return $this->model->deleteMealPlan($plan_id);
        } else {
            $_SESSION['error_message'] = "You do not have permission to delete this meal plan.";
            return false;
        }
    }
    
    public function getMealPlansByCategory($user_id, $meal_category) {
        return $this->model->getMealPlansByCategory($user_id, $meal_category);
    }

    // Get all meal plans (for admins)
    public function getAllMealPlans() {
        return $this->model->getAllMealPlans();
    }

    // Get all meal plans by category (for admins)
    public function getAllMealPlansByCategory($meal_category) {
        return $this->model->getAllMealPlansByCategory($meal_category);
    }

    // Get user meal plans with alarms
    public function getUserMealPlansWithAlarms($user_id) {
        if (!isset($_SESSION['user_id']) || $user_id != $_SESSION['user_id']) {
            return false;
        }
        return $this->model->getUserMealPlansWithAlarms($user_id);
    }
}
?>