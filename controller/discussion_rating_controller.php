<?php
require_once '../model/discussions_rating_model.php';

class DiscussionRatingController {
    private $ratingModel;

    public function __construct($db) {
        $this->ratingModel = new DiscussionRating($db);
    }
    
    public function getRatingDistribution($discussionId) {
        return $this->ratingModel->getRatingDistribution($discussionId);
    }
    
    public function getUserRating($userId, $discussionId) {
        return $this->ratingModel->getUserRating($userId, $discussionId);
    }
    
    public function addRating($discussionId, $userId, $ratingValue) {
        if (!in_array($ratingValue, ['relevant', 'slightly_relevant', 'totally_irrelevant'])) {
            return "Rating must be 'relevant', 'slightly_relevant', or 'totally_irrelevant'";
        }
        $createdTime = date('Y-m-d H:i:s');
        $result = $this->ratingModel->addRating($userId, $discussionId, $ratingValue, $createdTime);
        return $result ? true : "Failed to add rating";
    }
    
    public function deleteRating($userId, $discussionId) {
        $result = $this->ratingModel->deleteRating($userId, $discussionId);
        return $result ? true : "Failed to delete rating";
    }
}
?>