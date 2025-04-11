<?php
require_once '../model/comment_model.php';

class CommentController {
    private $commentModel;

    public function __construct($conn) {
        $this->commentModel = new Comment($conn);
    }
    
    public function getCommentsByDiscussion($discussionId, $limit = 10, $offset = 0) {
        return $this->commentModel->getCommentsByDiscussion($discussionId, $limit, $offset);
    }
    
    public function getCommentCount($discussionId) {
        return $this->commentModel->getCommentCount($discussionId);
    }
    
    public function createComment($discussionId, $userId, $text) {
        // Validate input
        if (empty($text)) {
            return "Comment text is required";
        }
        
        // Set date
        $createdTime = date('Y-m-d H:i:s');
        
        // Add comment
        $result = $this->commentModel->addDiscussionComment($userId, $discussionId, $text, $createdTime);
        
        if ($result) {
            return true;
        } else {
            return "Failed to add comment";
        }
    }
    
    public function updateComment($commentId, $userId, $text) {
        // Check ownership
        if (!$this->commentModel->isOwner($commentId, $userId)) {
            return "You don't have permission to edit this comment";
        }
        
        // Validate input
        if (empty($text)) {
            return "Comment text is required";
        }
        
        // Update comment
        $result = $this->commentModel->updateComment($commentId, $text);
        
        if ($result) {
            return true;
        } else {
            return "Failed to update comment";
        }
    }
    
    public function deleteComment($commentId, $userId) {
        // Check ownership or admin rights
        if (!$this->commentModel->isOwner($commentId, $userId) && $_SESSION['roles'] != 'admin') {
            return "You don't have permission to delete this comment";
        }
        
        // Delete comment
        $result = $this->commentModel->deleteComment($commentId);
        
        if ($result) {
            return true;
        } else {
            return "Failed to delete comment";
        }
    }

    public function getComment($commentId) {
        return $this->commentModel->getComment($commentId);
    }
}
?>