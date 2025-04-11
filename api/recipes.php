<?php
session_start();
require_once '../config/db_connection.php';
require_once '../model/recipe_model.php';

header('Content-Type: application/json');

// Handle CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Create recipe model instance
$recipeModel = new Recipe($conn);

// Handle GET request for recipe search
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    
    try {
        $recipes = $recipeModel->searchRecipes($search, $userId);
        echo json_encode([
            'success' => true,
            'data' => $recipes
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error searching recipes: ' . $e->getMessage()
        ]);
    }
}
else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>