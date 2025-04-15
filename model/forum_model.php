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

    public function deleteCategory($category_id) {
        $sql = "DELETE FROM forum_category WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return "Error preparing statement: " . $this->conn->error;
        }
        $stmt->bind_param("i", $category_id);
        return $stmt->execute() ? true : "Error deleting category: " . $stmt->error;
    }

    public function editCategory($category_id, $category_name, $category_description) {
        if (empty($category_name) || empty($category_description)) {
            return "Name and description cannot be empty.";
        }

        $sql = "UPDATE forum_category SET name = ?, description = ? WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return "Error preparing statement: " . $this->conn->error;

        $stmt->bind_param("ssi", $category_name, $category_description, $category_id);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("SQL Error: " . $stmt->error);
        }
        
        return $result ? true : "Error updating category: " . $stmt->error;
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

class ForumRatingModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Rate a thread
    public function rateThread($thread_id, $user_id, $rating, $category_id) {
        // Check if the user has already rated the thread
        if ($this->getUserRating($thread_id, $user_id, $category_id)) {
            return ["error" => "You have already rated this thread."];
        }

        // Insert new rating
        $sql = "INSERT INTO forum_thread_rating (category_id, thread_id, user_id, rating) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $category_id, $thread_id, $user_id, $rating);
        $stmt->execute();

        // Fetch the updated average rating
        return (float) $this->getAverageRating($thread_id, $category_id);
    }

    // Get average rating for a thread
    public function getAverageRating($thread_id, $category_id) {
        $sql = "SELECT AVG(rating) as average_rating FROM forum_thread_rating WHERE thread_id = ? AND category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $thread_id, $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['average_rating'] ?: 0; // Return 0 if no ratings
    }

    // Get user's rating for a specific thread
    public function getUserRating($thread_id, $user_id, $category_id) {
        $sql = "SELECT rating FROM forum_thread_rating WHERE thread_id = ? AND user_id = ? AND category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $thread_id, $user_id, $category_id);
        $stmt->execute();
        $stmt->bind_result($rating);
        $stmt->fetch();
        return $rating ?? null; // Returns the rating or null if not rated
    }
}
?>
