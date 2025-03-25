$(document).ready(function() {
    // Variables to store comment state
    let commentIdToDelete = null;
    
    // Add Comment Form Submission
    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        
        const discussionId = $('#discussionId').val();
        const commentText = $('#commentText').val().trim();
        
        // Validate comment text
        if (commentText === '') {
            showCommentAlert('danger', 'Comment text is required.');
            $('#commentText').focus();
            return;
        }
        
        // Send AJAX request to add comment
        $.ajax({
            url: '../api/comments.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                discussion_id: discussionId,
                comment_text: commentText
            }),
            success: function(response) {
                if (response.success) {
                    // Clear the form
                    $('#commentText').val('');
                    
                    // Add the new comment to the list
                    addCommentToList(response.data);
                    
                    // Show success message
                    showCommentAlert('success', 'Comment added successfully.');
                } else {
                    showCommentAlert('danger', response.message);
                }
            },
            error: function() {
                showCommentAlert('danger', 'An error occurred while adding the comment.');
            }
        });
    });
    
    // Edit Comment Button Click
    $(document).on('click', '.edit-comment', function() {
        const commentId = $(this).data('id');
        const commentCard = $(`#comment-${commentId}`);
        
        // Hide comment text and show edit form
        commentCard.find('.comment-text').addClass('d-none');
        commentCard.find('.edit-form').removeClass('d-none');
        
        // Focus on the textarea
        commentCard.find('.edit-comment-text').focus();
    });
    
    // Cancel Edit Button Click
    $(document).on('click', '.cancel-edit', function() {
        const commentCard = $(this).closest('.comment');
        
        // Show comment text and hide edit form
        commentCard.find('.comment-text').removeClass('d-none');
        commentCard.find('.edit-form').addClass('d-none');
    });
    
    // Save Edit Button Click
    $(document).on('click', '.save-edit', function() {
        const commentId = $(this).data('id');
        const commentCard = $(`#comment-${commentId}`);
        const updatedText = commentCard.find('.edit-comment-text').val().trim();
        
        // Validate comment text
        if (updatedText === '') {
            showCommentAlert('danger', 'Comment text cannot be empty.');
            return;
        }
        
        // Send AJAX request to update comment
        $.ajax({
            url: '../api/comments.php',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({
                comment_id: commentId,
                comment_text: updatedText
            }),
            success: function(response) {
                if (response.success) {
                    // Update comment text
                    commentCard.find('.comment-text').html(response.data.comment_text.replace(/\n/g, '<br>'));
                    
                    // Show comment text and hide edit form
                    commentCard.find('.comment-text').removeClass('d-none');
                    commentCard.find('.edit-form').addClass('d-none');
                    
                    // Show success message
                    showCommentAlert('success', 'Comment updated successfully.');
                } else {
                    showCommentAlert('danger', response.message);
                }
            },
            error: function() {
                showCommentAlert('danger', 'An error occurred while updating the comment.');
            }
        });
    });
    
    // Delete Comment Button Click
    $(document).on('click', '.delete-comment', function() {
        const commentId = $(this).data('id');
        commentIdToDelete = commentId;
        
        // Show confirmation modal
        $('#deleteCommentModal').modal('show');
    });
    
    // Confirm Delete Comment Button Click
    $('#confirmDeleteComment').on('click', function() {
        if (!commentIdToDelete) return;
        
        // Send AJAX request to delete comment
        $.ajax({
            url: '../api/comments.php?id=' + commentIdToDelete,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    // Remove comment from the list
                    $(`#comment-${commentIdToDelete}`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Show empty message if no comments left
                        if ($('#commentsList .comment').length === 0) {
                            $('#commentsList').html('<div class="alert alert-info">No comments yet. Be the first to comment!</div>');
                        }
                    });
                    
                    // Show success message
                    showCommentAlert('success', 'Comment deleted successfully.');
                } else {
                    showCommentAlert('danger', response.message);
                }
                
                // Close modal and reset commentIdToDelete
                $('#deleteCommentModal').modal('hide');
                commentIdToDelete = null;
            },
            error: function() {
                showCommentAlert('danger', 'An error occurred while deleting the comment.');
                $('#deleteCommentModal').modal('hide');
                commentIdToDelete = null;
            }
        });
    });
    
    // Function to add new comment to the list
    function addCommentToList(comment) {
        // Format date
        const createdDate = new Date(comment.created_time);
        const formattedDate = createdDate.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Create comment HTML
        const commentHtml = `
            <div class="card mb-3 comment" id="comment-${comment.comments_id}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle text-muted">
                            ${comment.username} - ${formattedDate}
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link dropdown-toggle" type="button" id="commentMenu${comment.comments_id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="commentMenu${comment.comments_id}">
                                <button class="dropdown-item edit-comment" data-id="${comment.comments_id}">Edit</button>
                                <button class="dropdown-item delete-comment" data-id="${comment.comments_id}">Delete</button>
                            </div>
                        </div>
                    </div>
                    <p class="card-text comment-text">${comment.comment_text.replace(/\n/g, '<br>')}</p>
                    <div class="edit-form d-none">
                        <textarea class="form-control edit-comment-text mb-2">${comment.comment_text}</textarea>
                        <button class="btn btn-sm btn-primary save-edit" data-id="${comment.comments_id}">Save</button>
                        <button class="btn btn-sm btn-secondary cancel-edit">Cancel</button>
                    </div>
                </div>
            </div>
        `;
        
        // Remove empty message if present
        if ($('#commentsList .alert').length > 0) {
            $('#commentsList').empty();
        }
        
        // Add comment to the beginning of the list
        $('#commentsList').prepend(commentHtml);
    }
    
    // Helper function to show comment alerts
    function showCommentAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove any existing comment alerts
        $('.comment-alert').remove();
        
        // Add the alert before the comments list
        $('#commentForm').after(alertHtml).next('.alert').addClass('comment-alert');
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.comment-alert').alert('close');
        }, 5000);
    }
});