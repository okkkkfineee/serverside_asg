<?php

class MealPlanning {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new meal plan
    public function createMealPlan($recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date) {
        $query = "INSERT INTO meal_plans (recipe_id, user_id, plan_name, meal_category, meal_time, meal_date, created_date) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Convert time format if needed
        if (strpos($meal_time, ':') !== false) {
            list($hours, $minutes) = explode(':', $meal_time);
            $meal_time = ($hours * 60) + $minutes; // Store as minutes since midnight
        }
        
        // Bind parameters
        $stmt->bind_param("iissis", $recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date);
        
        return $stmt->execute();
    }

    // Get all meal plans for a user
    public function getUserMealPlans($user_id) {
        $query = "SELECT 
                    *,
                    LPAD(FLOOR(meal_time/60), 2, '0') as hours,
                    LPAD(MOD(meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans 
                 WHERE user_id = ? 
                 ORDER BY meal_date ASC, meal_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    // Get a specific meal plan by ID
    public function getMealPlanById($plan_id) {
        $query = "SELECT 
                    *,
                    LPAD(FLOOR(meal_time/60), 2, '0') as hours,
                    LPAD(MOD(meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans 
                 WHERE plan_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $plan_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    // Update a meal plan
    public function updateMealPlan($plan_id, $recipe_id, $plan_name, $meal_category, $meal_time, $meal_date) {
        $query = "UPDATE meal_plans 
                 SET recipe_id = ?, 
                     plan_name = ?, 
                     meal_category = ?,
                     meal_time = ?,
                     meal_date = ? 
                 WHERE plan_id = ?";
        
        // Convert time format if needed
        if (strpos($meal_time, ':') !== false) {
            list($hours, $minutes) = explode(':', $meal_time);
            $meal_time = ($hours * 60) + $minutes; // Store as minutes since midnight
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bind_param("issisi", $recipe_id, $plan_name, $meal_category, $meal_time, $meal_date, $plan_id);
        
        return $stmt->execute();
    }

    // Delete a meal plan
    public function deleteMealPlan($plan_id) {
        $query = "DELETE FROM meal_plans WHERE plan_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $plan_id);
        
        return $stmt->execute();
    }

    // Get meal plans by category
    public function getMealPlansByCategory($user_id, $meal_category) {
        $query = "SELECT 
                    *,
                    LPAD(FLOOR(meal_time/60), 2, '0') as hours,
                    LPAD(MOD(meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans 
                 WHERE user_id = ? 
                 AND meal_category = ? 
                 ORDER BY meal_date ASC, meal_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $user_id, $meal_category);
        $stmt->execute();
        
        return $stmt->get_result();
    }
    
    // Get user meal plans with alarms
    public function getUserMealPlansWithAlarms($user_id) {
        $query = "SELECT * FROM meal_plans 
                 WHERE user_id = ? 
                 AND set_alarm = 1 
                 AND meal_date >= CURDATE() 
                 ORDER BY meal_date ASC, meal_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }
}
?>