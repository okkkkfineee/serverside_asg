<?php
class DiscussionRating {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRatingDistribution($discussionId) {
        $sql = "SELECT rating_value, COUNT(*) as count 
                FROM discussion_ratings WHERE discussion_id = ? 
                GROUP BY rating_value";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $discussionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    }

    public function getUserRating($userId, $discussionId) {
        $sql = "SELECT rating_value FROM discussion_ratings WHERE user_id = ? AND discussion_id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("ii", $userId, $discussionId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['rating_value'] : null;
    }

    public function addRating($userId, $discussionId, $ratingValue, $createdTime) {
        $sql = "INSERT INTO discussion_ratings (user_id, discussion_id, rating_value, created_time) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE rating_value = ?, created_time = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("issss", $userId, $discussionId, $ratingValue, $createdTime, $ratingValue, $createdTime);
        return $stmt->execute();
    }

    public function deleteRating($userId, $discussionId) {
        $sql = "DELETE FROM discussion_ratings WHERE user_id = ? AND discussion_id = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ii", $userId, $discussionId);
        return $stmt->execute();
    }
}
?>