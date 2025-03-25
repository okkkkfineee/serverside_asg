<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to perform this action.']);
    exit;
}

require_once '../controller/discussion_controller.php';

$conn = require_once '../config/db_connection.php';
$discussionController = new DiscussionController($conn);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $discussionId = intval($_GET['id']);
            $discussion = $discussionController->getDiscussion($discussionId);
            echo json_encode($discussion ? ['success' => true, 'data' => $discussion] : ['success' => false, 'message' => 'Discussion not found.']);
        } else {
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $discussions = $discussionController->getAllDiscussions($limit, $offset, $search);
            $totalCount = $discussionController->getDiscussionCount($search);
            echo json_encode([
                'success' => true,
                'data' => $discussions,
                'total' => $totalCount,
                'limit' => $limit,
                'offset' => $offset
            ]);
        }
        break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['title']) || !isset($data['content'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
                exit;
            }
        
            // Validate title and content
            $title = trim($data['title']);
            $content = trim($data['content']);
            $recipe_id = isset($data['recipe_id']) ? ($data['recipe_id'] ? intval($data['recipe_id']) : null) : null;
            if (empty($title) || empty($content)) {
                echo json_encode(['success' => false, 'message' => 'Title and content are required.']);
                exit;
            }
        
            $userId = $_SESSION['user_id'];
            $createdTime = date('Y-m-d H:i:s');
            $result = $discussionController->createDiscussion($userId, $title, $content, $createdTime, $recipe_id);
            echo json_encode(is_numeric($result) ? ['success' => true, 'message' => 'Discussion created successfully.', 'discussionId' => $result] : ['success' => false, 'message' => 'Failed to create discussion']);
            break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data or missing discussion ID.']);
            exit;
        }
        $discussionId = intval($data['id']);
        $userId = $_SESSION['user_id'];
        $title = isset($data['title']) ? trim($data['title']) : '';
        $content = isset($data['content']) ? trim($data['content']) : '';
        // Handle null or invalid recipe_id
        $recipe_id = isset($data['recipe_id']) ? ($data['recipe_id'] ? intval($data['recipe_id']) : null) : null; 
        $result = $discussionController->updateDiscussion($discussionId, $userId, $title, $content, $recipe_id);
        echo json_encode($result === true ? ['success' => true, 'message' => 'Discussion updated successfully.'] : ['success' => false, 'message' => $result]);
        break;

    case 'DELETE':
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'Discussion ID is required']);
            exit;
        }
        $discussionId = (int)$_GET['id'];
        $userId = $_SESSION['user_id'];
        if (!$discussionController->isOwner($discussionId, $userId) && !$discussionController->isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this discussion']);
            exit;
        }
        $result = $discussionController->deleteDiscussion($discussionId);
        echo json_encode($result ? ['success' => true, 'message' => 'Discussion deleted successfully'] : ['success' => false, 'message' => 'Failed to delete discussion']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
exit;
?>