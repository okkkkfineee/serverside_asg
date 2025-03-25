<?php
class Comment {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getCommentsByDiscussion($discussionId, $limit = 10, $offset = 0) {
        $sql = "SELECT c.*, u.username FROM comments c 
                JOIN user u ON c.user_id = u.user_id 
                WHERE discussion_id = ? 
                ORDER BY c.created_time DESC 
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $discussionId, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addDiscussionComment($userId, $discussionId, $text, $createdTime) {
        $sql = "INSERT INTO comments (user_id, discussion_id, comment_text, created_time) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $userId, $discussionId, $text, $createdTime);
        return $stmt->execute();
    }

    public function updateComment($commentId, $text) {
        $sql = "UPDATE comments SET comment_text = ? WHERE comments_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $text, $commentId);
        return $stmt->execute();
    }

    public function deleteComment($commentId) {
        $sql = "DELETE FROM comments WHERE comments_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $commentId);
        return $stmt->execute();
    }

    public function isOwner($commentId, $userId) {
        $sql = "SELECT user_id FROM comments WHERE comments_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result && $result['user_id'] == $userId;
    }
}
?>