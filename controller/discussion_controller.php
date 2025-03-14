<?php
require_once '../model/discussion_model.php';
require_once '../model/comment_model.php';

class DiscussionController {
    private $discussion;
    private $comment;
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->discussion = new Discussion($this->conn);
        $this->comment = new Comment($this->conn);
    }
    
    public function listDiscussions() {
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get search parameter
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Get discussions with pagination
        $discussions = $this->discussion->getAllDiscussions($limit, $offset, $search);
        $totalDiscussions = $this->discussion->getDiscussionCount($search);
        $totalPages = ceil($totalDiscussions / $limit);
        
        // Include view
        include '../view/discussions.php';
    }
    
    public function viewDiscussion() {
        // Check if discussion ID is provided
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "Discussion ID is required";
            header("Location: discussions.php");
            exit();
        }
        
        $discussionId = (int)$_GET['id'];
        
        // Get discussion details
        $discussion = $this->discussion->getDiscussion($discussionId);
        
        if (!$discussion) {
            $_SESSION['error'] = "Discussion not found";
            header("Location: discussions.php");
            exit();
        }
        
        // Get comments for this discussion
        $comments = $this->comment->getCommentsByDiscussion($discussionId);
        
        // Include view
        include '../view/view_discussion.php';
    }
    
    public function showCreateForm() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to create a discussion";
            header("Location: login.php");
            exit();
        }
        
        // Include view
        include '../view/discussion.php';
    }
    
    public function showEditForm() {
        // Check if user is logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to edit a discussion";
            header("Location: login.php");
            exit();
        }
        
        // Check if discussion ID is provided
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $_SESSION['error'] = "Discussion ID is required";
            header("Location: discussions.php");
            exit();
        }
        
        $discussionId = (int)$_GET['id'];
        $userId = $_SESSION['user_id'];
        
        // Get discussion details
        $discussion = $this->discussion->getDiscussion($discussionId);
        
        if (!$discussion) {
            $_SESSION['error'] = "Discussion not found";
            header("Location: discussions.php");
            exit();
        }
        
        // Check if user is the owner
        if (!$this->discussion->isOwner($discussionId, $userId) && !isAdmin()) {
            $_SESSION['error'] = "You do not have permission to edit this discussion";
            header("Location: view_discussion.php?id=" . $discussionId);
            exit();
        }
        
        // Include view
        include '../view/discussion.php';
    }
    
    public function createDiscussion($userId, $title, $content, $createdTime, $recipe_id = null) {
        // Check if user is logged in (for non-API calls)
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to create a discussion";
            return false;
        }
        
        // Validate form data
        if (empty($title) || empty($content)) {
            $_SESSION['error'] = "Title and content are required";
            return false;
        }
        
        // Create discussion
        $result = $this->discussion->createDiscussion($userId, $title, $content, $createdTime, $recipe_id);
        
        if ($result) {
            $_SESSION['success'] = "Discussion created successfully";
            return $result;
        } else {
            $_SESSION['error'] = "Failed to create discussion";
            return false;
        }
    }
    
    public function updateDiscussion($discussionId, $userId, $title, $content, $recipe_id = null) {
        // Check if user is logged in
        if (!isLoggedIn()) {
            return "You must be logged in to edit a discussion";
        }
        
        // Check if user is the owner
        if (!$this->discussion->isOwner($discussionId, $userId) && !isAdmin()) {
            return "You do not have permission to edit this discussion";
        }
        
        // Validate form data
        if (empty($title) || empty($content)) {
            return "Title and content are required";
        }
        
        // Update discussion
        $result = $this->discussion->updateDiscussion($discussionId, $title, $content, $recipe_id);
        if ($result) {
            $_SESSION['success'] = "Discussion updated successfully";
            return true;
        } else {
            $_SESSION['error'] = "Failed to update discussion";
            return false;
        }
    }
    
    public function deleteDiscussion($discussionId) {
        // Check if user is logged in
        if (!isLoggedIn()) {
            $_SESSION['error'] = "You must be logged in to delete a discussion";
            return false;
        }
        
        // Delete discussion
        $result = $this->discussion->deleteDiscussion($discussionId);
        
        if ($result) {
            $_SESSION['success'] = "Discussion deleted successfully";
            return true;
        } else {
            $_SESSION['error'] = "Failed to delete discussion";
            return false;
        }
    }

    // Methods for API
    public function getAllDiscussions($limit, $offset, $search = '') {
        return $this->discussion->getAllDiscussions($limit, $offset, $search);
    }

    public function getDiscussionCount($search = '') {
        return $this->discussion->getDiscussionCount($search);
    }

    public function getDiscussion($discussionId) {
        return $this->discussion->getDiscussion($discussionId);
    }

    public function isOwner($discussionId, $userId) {
        return $this->discussion->isOwner($discussionId, $userId);
    }

    public function isAdmin() {
        return isAdmin();
    }
}

// Helper function for the controller
function isAdmin() {
    return isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin';
}

// Helper function for the controller
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>