<?php

// Forum Category Model
class ForumCategoryModel {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM forum_category";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoryById($category_id) {
        $sql = "SELECT * FROM forum_category WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // admin can create Category 
    public function createCategory($category_name, $category_description) {
        if (empty($category_name) || empty($category_description)) {
            return "Category name and description cannot be empty.";
        }
        if (strlen($category_name) > 50) {
            return "Category name cannot exceed 50 characters.";
        }
        $sql = "INSERT INTO forum_category (name, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $category_name, $category_description);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return "Error creating category: " . $stmt->error;
        }
    }
}

// Forum Thread Model
class ForumThreadModel {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function createThread($user_id, $category_id, $title, $content) {
        if (empty($title) || empty($content)) {
            return "Title and content cannot be empty.";
        }

        $sql = "INSERT INTO forum_thread (user_id, category_id, title, content, created_time) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $user_id, $category_id, $title, $content);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return "Error creating thread: " . $stmt->error;
        }
    }

    public function getThreadsByCategory($category_id) {
        $sql = "SELECT * FROM forum_thread WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getThreadById($thread_id) {
        $sql = "SELECT * FROM forum_thread WHERE thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null; // No thread found
        }

        return $result->fetch_assoc(); // Return the thread details
    }

    public function getThreadUserNameByThreadId($thread_id) {
        $sql = "SELECT forum_thread.*, user.username FROM forum_thread 
                INNER JOIN user ON forum_thread.user_id = user.user_id
                WHERE forum_thread.thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return null; // No thread found
        }

        return $result->fetch_assoc(); // Return the thread details along with username
    }

    public function updateThread($thread_id, $title, $content, $updated_at) {
        if (empty($title) || empty($content)) {
            return "Title and content cannot be empty.";
        }

        $sql = "UPDATE forum_thread SET title = ?, content = ?, updated_at = ? WHERE thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return "Error preparing statement: " . $this->conn->error;
        $stmt->bind_param("sssi", $title, $content, $updated_at, $thread_id);
        return $stmt->execute() ? true : "Error updating thread: " . $stmt->error;
    }

    public function deleteThread($thread_id) {
        $sql = "DELETE FROM forum_thread WHERE thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        return $stmt->execute() ? true : "Error deleting thread: " . $stmt->error;
    }
    // for checking if thread exists for the post creation
    public function threadExists($thread_id) {
        $sql = "SELECT COUNT(*) FROM forum_thread WHERE thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0; // Returns true if the thread exists
    }
}

// Forum Post Model
class ForumPostModel {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function createPost($thread_id, $user_id, $content) {
        if (empty($content)) return "Content cannot be empty.";

        $sql = "INSERT INTO forum_post (thread_id, user_id, content, created_time) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $thread_id, $user_id, $content);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return "Error creating post: " . $stmt->error;
        }
    }

    public function getPostsByThread($thread_id) {
        $sql = "SELECT * FROM forum_post WHERE thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updatePost($post_id, $content) {
        if (empty($content)) return "Content cannot be empty.";

        $sql = "UPDATE forum_post SET content = ? WHERE post_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $content, $post_id);
        return $stmt->execute() ? true : "Error updating post: " . $stmt->error;
    }

    public function handleCreatePost($user_id, $thread_id, $content) {
        if (!$user_id || !$thread_id || empty($content)) {
            return ["error" => "Failed to post. Please make sure all fields are filled."];
        }

        $result = $this->createPost($thread_id, $user_id, $content);
        if (is_numeric($result)) {
            return ["success" => "Post created successfully!", "post_id" => $result];
        } else {
            return ["error" => $result];
        }
    }
}
?>
