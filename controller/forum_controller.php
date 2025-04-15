<?php

require_once '../model/forum_model.php';
require_once '../model/user_model.php';
require_once '../config/db_connection.php'; 

class ForumController {
    private $categoryModel;
    private $threadModel;
    private $postModel;
    private $userModel;
    private $ratingModel;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->categoryModel = new ForumCategoryModel($db);
        $this->threadModel = new ForumThreadModel($db);
        $this->postModel = new ForumPostModel($db);
        $this->ratingModel = new ForumRatingModel($db);
        $this->userModel = new User($db);
    }

    public function handleRequest($method, $action, $data) {
        if ($method !== 'POST' || !isset($action)) {
            http_response_code(400);
            echo "Invalid request.";
            return;
        }

        switch ($action) {
            case 'createThreadAction':
                $this->createThread($data);
                break;
            case 'createPost':
                $this->createPost($data);
                break;
            case 'updateThread':
                $this->updateThread($data);
                break;
            case 'createCategoryAction':
                $this->createCategory($data);
                break;
            case 'editCategory':
                $this->editCategory();
                break;
            case 'deleteCategory':
                $this->deleteCategory($data['category_id']);
                break;
            case 'rateThread':
                $this->rateThread($data);
                break;
            default:
                http_response_code(404);
                echo "Action not found.";
                break;
        }
    }

    public function viewCategory($category_id) {
        $category = $this->categoryModel->getCategoryById($category_id);
        $threads = $this->categoryModel->getThreadsByCategory($category_id);
        include '../view/category_threads.php';
    }

    public function viewThread($thread_id) {
        $thread = $this->threadModel->getThreadById($thread_id);
        $posts = $this->threadModel->getPostsByThread($thread_id);
        include '../view/thread.php';
    }

    private function createThread($data) {
        $title = trim($data['title']);
        $user_id = (int)$data['user_id'];
        $category_id = (int)$data['category_id'];
        $content = trim($data['content']);
        $created_time = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("
            INSERT INTO forum_thread (user_id, category_id, title, content, created_time) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $user_id, $category_id, $title, $content, $created_time);
        $stmt->execute();
        $stmt->close();

        header("Location: ../view/category_threads.php?id=$category_id");
        exit;
    }

    public function createPost($data) {
        $user_id = (int)$data['user_id'];
        $thread_id = (int)$data['thread_id'];
        $content = trim($data['content']);

        // Validate inputs
        if (empty($content)) {
            return ["error" => "Content cannot be empty."];
        }

        // Check if the thread exists
        if (!$this->threadModel->threadExists($thread_id)) {
            return ["error" => "Specified thread does not exist."];
        }

        // Call the model's createPost method
        $result = $this->postModel->createPost($thread_id, $user_id, $content);

        if (is_numeric($result)) {
            // Redirect to the thread page after successful post creation
            header("Location: ../view/thread.php?id=" . $thread_id);
            exit(); // Make sure to exit after the redirect
        } else {
            return ["error" => $result];
        }
    }

    private function updateThread($data) {
        $thread_id = (int)$data['thread_id'];
        $title = trim($data['title']);
        $content = trim($data['content']);
        $updated_at = date('Y-m-d H:i:s');

        // Fetch the thread to get the category ID
        $thread = $this->threadModel->getThreadById($thread_id); 

        if (!$thread) {
            echo "Thread not found.";
            exit;
        }

        $result = $this->threadModel->updateThread($thread_id, $title, $content, $updated_at);

        if ($result === true) {
            // Redirect with category ID
            header("Location: ../view/category_threads.php?id=" . $thread['category_id']);
            exit;
        } else {
            echo "Error updating thread: " . htmlspecialchars($result);
        }
    }

   private function createCategory($data) {
    // Check if keys exist in the array
        if (!isset($data['category_name']) || !isset($data['category_description'])) {
            $_SESSION['error'] = "Category name and description must be provided.";
            header("Location: ../view/create_category.php");
            exit;
        }

        $category_name = trim($data['category_name']);
        $category_description = trim($data['category_description']);

        $result = $this->categoryModel->createCategory($category_name, $category_description);

        // Handle the result
        if (is_numeric($result)) {
            // Successfully created category, redirect to the forums with a success message
            $_SESSION['message'] = "Category created successfully.";
            header("Location: ../view/forums.php"); // Redirect to the forums page
            exit;
        } else {
            // An error occurred, store the error message in the session
            $_SESSION['error'] = $result;
            header("Location: ../view/create_category.php"); // Redirect back to the create category form
            exit;
        }
    }

    private function deleteCategory($category_id) {
        $result = $this->categoryModel->deleteCategory($category_id);
        if ($result) {
            header("Location: ../view/forums.php");
            exit;
        } else {
            echo "Error deleting category.";
        }
    }

    public function editCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['category_id'];
            $category_name = $_POST['title']; 
            $category_description = $_POST['content']; 

            // Call the model's editCategory method
            $result = $this->categoryModel->editCategory($category_id, $category_name, $category_description);
            
            // Check the result and redirect or display an error message
            if ($result === true) {
                header("Location: ../view/forums.php"); // Redirect to the forums page
                exit();
            } else {
                // Handle error message
                echo $result; // You might want to handle this more gracefully in production
            }
        }
    }

    public function getAverageRating($thread_id, $category_id) {
        return $this->ratingModel->getAverageRating($thread_id, $category_id);
    }

    public function getThreadDetails($thread_id) {
        return $this->threadModel->getThreadUserNameByThreadId($thread_id);
    }
    
    public function getThreadUser($thread_id) {
        $sql = "SELECT user_id FROM forum_thread WHERE thread_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['user_id'] : null;
    }

    public function getPostUserName($post_id) {
        $sql = "SELECT u.username 
                FROM forum_post p
                JOIN user u ON p.user_id = u.user_id
                WHERE p.post_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['username'] : null;
    }
    
    public function deleteThread($thread_id) {
        return $this->threadModel->deleteThread($thread_id);
    }

    public function deletePost($post_id) {
        return $this->postModel->deletePost($post_id);
    }

    public function getAllCategories() {
        return $this->categoryModel->getAllCategories();
    }

    public function getThreadsByCategory($category_id) {
        return $this->threadModel->getThreadsByCategory($category_id);
    }

    public function getCategoryById($categoryId) {
        return $this->categoryModel->getCategoryById($categoryId);
    }

    public function getThreadById($threadId) {
        return $this->threadModel->getThreadById($threadId);
    }

    public function getPostsByThread($thread_id) {
        return $this->postModel->getPostsByThread($thread_id);
    }

    // rating thread 
    public function rateThread($data) {
        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $thread_id = isset($data['thread_id']) ? (int)$data['thread_id'] : null;
        $rating = isset($data['rating']) ? (int)$data['rating'] : null;
        $category_id = isset($data['category_id']) ? (int)$data['category_id'] : null;

        // Validate input
        if (!$user_id || !$thread_id || $rating === null) {
            http_response_code(400);
            echo "Invalid input. Please provide a rating.";
            exit;
        }

        // Check if the user has already rated the thread
        if ($this->getUserRating($thread_id, $user_id,$category_id)) {
            // Return a JavaScript snippet for alert and redirect
            echo "<script>
                    alert('You have already rated this thread. Don\'t rate it again!');
                    window.location.href = '../view/category_threads.php?id=" . $category_id . "';
                </script>";
            exit;
        }

        // Call the model to rate the thread
        $result = $this->ratingModel->rateThread($thread_id, $user_id, $rating, $category_id);

        // Handle errors
        if (isset($result['error'])) {
            echo "<script>
                    alert('" . addslashes($result['error']) . "');
                    window.location.href = '../view/thread.php?id=" . $thread_id . "';
                </script>";
            exit;
        }

        // Redirect back to the category_threads page with a success message
        header("Location: ../view/category_threads.php?id=" . $category_id . "&message=Thanks for submitting your rating! Your rating is: " . $rating);
        exit;
    }

    public function getUserRating($thread_id, $user_id, $category_id) {
        return $this->ratingModel->getUserRating($thread_id, $user_id, $category_id);
    }
}

// Entry point
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $controller = new ForumController($conn);
    $controller->handleRequest($_SERVER['REQUEST_METHOD'], $_GET['action'], $_POST);
}
?>
