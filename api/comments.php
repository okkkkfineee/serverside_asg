<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

require_once '../controller/comment_controller.php';
$conn = require_once '../config/db_connection.php';
$commentController = new CommentController($conn);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['discussion_id'])) {
            $discussionId = intval($_GET['discussion_id']);
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            $comments = $commentController->getCommentsByDiscussion($discussionId, $limit, $offset);
            echo json_encode(['success' => true, 'data' => $comments]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing discussion_id.']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['discussion_id']) || !isset($data['text'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $discussionId = intval($data['discussion_id']);
        $text = trim($data['text']);
        $result = $commentController->createComment($discussionId, $userId, $text);
        echo json_encode($result === true ? ['success' => true, 'message' => 'Comment added'] : ['success' => false, 'message' => $result]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id']) || !isset($data['text'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit;
        }
        $commentId = intval($data['id']);
        $userId = $_SESSION['user_id'];
        $text = trim($data['text']);
        $result = $commentController->updateComment($commentId, $userId, $text);
        echo json_encode($result === true ? ['success' => true, 'message' => 'Comment updated'] : ['success' => false, 'message' => $result]);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing comment ID.']);
            exit;
        }
        $commentId = intval($_GET['id']);
        $userId = $_SESSION['user_id'];
        $result = $commentController->deleteComment($commentId, $userId);
        echo json_encode($result === true ? ['success' => true, 'message' => 'Comment deleted'] : ['success' => false, 'message' => $result]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>