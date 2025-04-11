<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

require_once '../controller/rating_controller.php';
$conn = require_once '../config/db_connection.php';
$ratingController = new RatingController($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['discussion_id'])) {
            $discussionId = intval($_GET['discussion_id']);
            $rating = $ratingController->getAverageRating($discussionId);
            if (isset($_GET['user']) && $_GET['user'] === 'true') {
                $userId = $_SESSION['user_id'];
                $rating['user_rating'] = $ratingController->getUserRating($userId, $discussionId);
            }
            echo json_encode(['success' => true, 'data' => $rating]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing discussion_id.']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['discussion_id']) || !isset($data['rating'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $discussionId = intval($data['discussion_id']);
        $ratingValue = intval($data['rating']);
        $result = $ratingController->addRating($discussionId, $userId, $ratingValue);
        echo json_encode($result === true ? ['success' => true, 'message' => 'Rating added'] : ['success' => false, 'message' => $result]);
        break;

    case 'DELETE':
        if (!isset($_GET['discussion_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing discussion_id.']);
            exit;
        }
        $discussionId = intval($_GET['discussion_id']);
        $userId = $_SESSION['user_id'];
        $result = $ratingController->deleteRating($userId, $discussionId);
        echo json_encode($result === true ? ['success' => true, 'message' => 'Rating deleted'] : ['success' => false, 'message' => $result]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>