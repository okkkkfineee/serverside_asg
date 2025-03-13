<?php

class Discussion {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllDiscussions($limit = 10, $offset = 0, $search = '') {
        $searchCondition = '';
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $searchCondition = "WHERE d.title LIKE ? OR d.content LIKE ?";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types = 'ss';
        }
        
        $sql = "SELECT d.*, u.username, 
                (SELECT COUNT(*) FROM comments WHERE discussion_id = d.discussion_id) as comment_count 
                FROM discussions d 
                JOIN user u ON d.user_id = u.user_id 
                $searchCondition
                ORDER BY d.created_time DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        
        // Add limit and offset to params
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        // Bind parameters dynamically
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDiscussionCount($search = '') {
        $searchCondition = '';
        $params = [];
        $types = '';
        
        if (!empty($search)) {
            $searchCondition = "WHERE title LIKE ? OR content LIKE ?";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types = 'ss';
        }
        
        $sql = "SELECT COUNT(*) as count FROM discussions $searchCondition";
        $stmt = $this->conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    public function getDiscussion($id) {
        $sql = "SELECT d.*, u.username 
                FROM discussions d 
                JOIN user u ON d.user_id = u.user_id 
                WHERE discussion_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function createDiscussion($userId, $title, $content, $createdTime) {
        $sql = "INSERT INTO discussions (user_id, title, content, created_time) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $userId, $title, $content, $createdTime);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }
    
    public function updateDiscussion($id, $title, $content) {
        $sql = "UPDATE discussions SET title = ?, content = ? WHERE discussion_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $id);
        return $stmt->execute();
    }
    
    public function deleteDiscussion($id) {
        $sql = "DELETE FROM discussions WHERE discussion_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function isOwner($discussionId, $userId) {
        $sql = "SELECT user_id FROM discussions WHERE discussion_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $discussionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $discussion = $result->fetch_assoc();
        
        return $discussion && $discussion['user_id'] == $userId;
    }
}
?>