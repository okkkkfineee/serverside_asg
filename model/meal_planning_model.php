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

        // Ensure meal_time is stored as integer (minutes since midnight)
        // The controller should already handle the conversion from HH:MM string
        if (!is_int($meal_time)) {
             // Log error or handle appropriately if time is not integer
             error_log("Meal time provided to createMealPlan model is not an integer: " . $meal_time);
             // Attempt conversion again as a fallback? Or return error?
             if (strpos($meal_time, ':') !== false) {
                 list($hours, $minutes) = explode(':', $meal_time);
                 $meal_time = ($hours * 60) + $minutes;
             } else {
                 // If it's not HH:MM and not int, it's invalid
                 return false;
             }
        }


        // Bind parameters
        $stmt->bind_param("iissis", $recipe_id, $user_id, $plan_name, $meal_category, $meal_time, $meal_date);

        return $stmt->execute();
    }

    // Get all meal plans for a user
    public function getUserMealPlans($user_id) {
        $query = "SELECT
                    mp.*,
                    u.username, -- Include username
                    LPAD(FLOOR(mp.meal_time/60), 2, '0') as hours,
                    LPAD(MOD(mp.meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(mp.meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(mp.meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans mp
                 JOIN user u ON mp.user_id = u.user_id -- Join with user table
                 WHERE mp.user_id = ?
                 ORDER BY mp.meal_date ASC, mp.meal_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result();
    }

     // Get all meal plans (for Admins/Mods)
    public function getAllMealPlans() {
        $query = "SELECT
                    mp.*,
                    u.username, -- Include username
                    LPAD(FLOOR(mp.meal_time/60), 2, '0') as hours,
                    LPAD(MOD(mp.meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(mp.meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(mp.meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans mp
                 JOIN user u ON mp.user_id = u.user_id -- Join with user table
                 ORDER BY mp.meal_date ASC, mp.meal_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->get_result();
    }


    // Get a specific meal plan by ID
    public function getMealPlanById($plan_id) {
        // Also fetch username here in case it's needed on detail/edit pages accessed by admins
        $query = "SELECT
                    mp.*,
                    u.username, -- Include username
                    LPAD(FLOOR(mp.meal_time/60), 2, '0') as hours,
                    LPAD(MOD(mp.meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(mp.meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(mp.meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans mp
                 JOIN user u ON mp.user_id = u.user_id -- Join with user table
                 WHERE mp.plan_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $plan_id);
        $stmt->execute();

        // Return single row assoc array or null
        return $stmt->get_result()->fetch_assoc();
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

        // Ensure meal_time is stored as integer (minutes since midnight)
        if (!is_int($meal_time)) {
             error_log("Meal time provided to updateMealPlan model is not an integer: " . $meal_time);
             if (strpos($meal_time, ':') !== false) {
                 list($hours, $minutes) = explode(':', $meal_time);
                 $meal_time = ($hours * 60) + $minutes;
             } else {
                 return false; // Invalid time format
             }
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

    // Get meal plans by category for a specific user
    public function getMealPlansByCategory($user_id, $meal_category) {
        $query = "SELECT
                    mp.*,
                    u.username, -- Include username
                    LPAD(FLOOR(mp.meal_time/60), 2, '0') as hours,
                    LPAD(MOD(mp.meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(mp.meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(mp.meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans mp
                 JOIN user u ON mp.user_id = u.user_id -- Join with user table
                 WHERE mp.user_id = ?
                 AND mp.meal_category = ?
                 ORDER BY mp.meal_date ASC, mp.meal_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $user_id, $meal_category);
        $stmt->execute();

        return $stmt->get_result();
    }

     // Get all meal plans by category (for Admins/Mods)
    public function getAllMealPlansByCategory($meal_category) {
        $query = "SELECT
                    mp.*,
                    u.username, -- Include username
                    LPAD(FLOOR(mp.meal_time/60), 2, '0') as hours,
                    LPAD(MOD(mp.meal_time, 60), 2, '0') as minutes,
                    CONCAT(
                        LPAD(FLOOR(mp.meal_time/60), 2, '0'),
                        ':',
                        LPAD(MOD(mp.meal_time, 60), 2, '0')
                    ) as formatted_time
                 FROM meal_plans mp
                 JOIN user u ON mp.user_id = u.user_id -- Join with user table
                 WHERE mp.meal_category = ?
                 ORDER BY mp.meal_date ASC, mp.meal_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $meal_category);
        $stmt->execute();

        return $stmt->get_result();
    }


    // Get user meal plans with alarms
    public function getUserMealPlansWithAlarms($user_id) {
        // Assuming alarms are user-specific, no join needed unless displaying username for admin too
        $query = "SELECT mp.* -- Select specific columns if username isn't needed here
                 FROM meal_plans mp
                 WHERE mp.user_id = ?
                 AND mp.set_alarm = 1 -- Assuming there's a set_alarm column
                 AND mp.meal_date >= CURDATE()
                 ORDER BY mp.meal_date ASC, mp.meal_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result();
    }
}
?>