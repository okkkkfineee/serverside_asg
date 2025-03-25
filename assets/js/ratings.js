$(document).ready(function() {
    // Variables
    let currentUserRating = 0;
    const recipeId = $('#recipeId').val(); // Assuming there's a hidden input with recipe ID
    
    // Initialize star rating display
    function initRating() {
        // Get current user rating if available
        if (recipeId) {
            $.ajax({
                url: '../api/ratings.php',
                type: 'GET',
                data: { recipe_id: recipeId },
                success: function(response) {
                    if (response.success) {
                        // Update average rating display
                        updateAverageRating(response.data.average_rating, response.data.total_ratings);
                        
                        // Set user's current rating if they have rated
                        if (response.data.user_rating) {
                            currentUserRating = response.data.user_rating;
                            highlightStars(currentUserRating);
                            $('#deleteRating').removeClass('d-none');
                        }
                    }
                }
            });
        }
    }
    
    // Handle star hover (preview rating)
    $('.rating-stars .star').on('mouseenter', function() {
        const hoverRating = $(this).data('rating');
        highlightStars(hoverRating, true);
    });
    
    // Handle mouse leave (restore current rating)
    $('.rating-stars').on('mouseleave', function() {
        highlightStars(currentUserRating);
    });
    
    // Handle star click (submit rating)
    $('.rating-stars .star').on('click', function() {
        if (!isLoggedIn()) {
            showRatingAlert('warning', 'You must be logged in to rate recipes.');
            return;
        }
        
        const newRating = $(this).data('rating');
        
        // Send AJAX request to rate recipe
        $.ajax({
            url: '../api/ratings.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                recipe_id: recipeId,
                rating_value: newRating
            }),
            success: function(response) {
                if (response.success) {
                    // Update current user rating
                    currentUserRating = newRating;
                    highlightStars(currentUserRating);
                    
                    // Update average rating display
                    updateAverageRating(response.data.average_rating, response.data.total_ratings);
                    
                    // Show delete button
                    $('#deleteRating').removeClass('d-none');
                    
                    // Show success message
                    showRatingAlert('success', response.message);
                } else {
                    showRatingAlert('danger', response.message);
                }
            },
            error: function() {
                showRatingAlert('danger', 'An error occurred while submitting your rating.');
            }
        });
    });
    
    // Handle delete rating button click
    $('#deleteRating').on('click', function() {
        // Send AJAX request to delete rating
        $.ajax({
            url: '../api/ratings.php?recipe_id=' + recipeId,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    // Reset current user rating
                    currentUserRating = 0;
                    highlightStars(0);
                    
                    // Update average rating display
                    updateAverageRating(response.data.average_rating, response.data.total_ratings);
                    
                    // Hide delete button
                    $('#deleteRating').addClass('d-none');
                    
                    // Show success message
                    showRatingAlert('success', response.message);
                } else {
                    showRatingAlert('danger', response.message);
                }
            },
            error: function() {
                showRatingAlert('danger', 'An error occurred while deleting your rating.');
            }
        });
    });
    
    // Helper function to highlight stars based on rating
    function highlightStars(rating, isHover = false) {
        $('.rating-stars .star').each(function() {
            const starRating = $(this).data('rating');
            const starClass = isHover ? 'hover' : 'active';
            
            if (starRating <= rating) {
                $(this).addClass(starClass);
            } else {
                $(this).removeClass(starClass);
            }
        });
    }
    
    // Helper function to update average rating display
    function updateAverageRating(average, total) {
        // Format average to one decimal place
        const formattedAverage = parseFloat(average).toFixed(1);
        
        // Update elements
        $('#averageRating').text(formattedAverage);
        $('#totalRatings').text(total + (total === 1 ? ' rating' : ' ratings'));
        
        // Update visual representation (e.g., stars)
        $('.average-rating-stars').css('width', (average / 5 * 100) + '%');
    }
    
    // Helper function to check if user is logged in
    function isLoggedIn() {
        return document.body.classList.contains('logged-in');
    }
    
    // Helper function to show rating alerts
    function showRatingAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        // Remove any existing rating alerts
        $('.rating-alert').remove();
        
        // Add the alert after the rating container
        $('.rating-container').after(alertHtml).next('.alert').addClass('rating-alert');
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.rating-alert').alert('close');
        }, 5000);
    }
    
    // Initialize rating on page load
    initRating();
});