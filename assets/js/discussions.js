$(document).ready(function() {
    // Handle discussion deletion
    $('#confirmDelete').on('click', function() {
        const discussionId = $(this).data('id');
        
        // Send AJAX request to delete discussion
        $.ajax({
            url: '../api/discussions.php?id=' + discussionId,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    // Show success message and redirect to discussions page
                    showAlert('success', response.message);
                    setTimeout(function() {
                        window.location.href = 'discussions.php';
                    }, 1500);
                } else {
                    // Show error message and close modal
                    showAlert('danger', response.message);
                    $('#deleteModal').modal('hide');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while deleting the discussion.');
                $('#deleteModal').modal('hide');
            }
        });
    });
    
    // Handle search form submission with validation
    $('.search-form').on('submit', function(e) {
        const searchInput = $(this).find('input[name="search"]');
        const searchTerm = searchInput.val().trim();
        
        // If search term is empty and not clearing a previous search, prevent empty searches
        if (searchTerm === '' && !window.location.search.includes('search=')) {
            e.preventDefault();
            searchInput.focus();
        }
    });
    
    // Handle pagination links for maintaining search terms
    $('.pagination .page-link').on('click', function(e) {
        const currentSearch = new URLSearchParams(window.location.search).get('search');
        if (currentSearch) {
            const href = $(this).attr('href');
            if (!href.includes('search=')) {
                e.preventDefault();
                window.location.href = href + '&search=' + encodeURIComponent(currentSearch);
            }
        }
    });
    
    // Create New Discussion Form Handling (for create_discussion.php)
    $('#discussionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const discussionId = $('#discussionId').val();
        const title = $('#title').val().trim();
        const content = $('#content').val().trim();
        
        // Validate form
        if (title === '') {
            showAlert('danger', 'Title is required.');
            $('#title').focus();
            return;
        }
        
        if (content === '') {
            showAlert('danger', 'Content is required.');
            $('#content').focus();
            return;
        }
        
        // Determine if we're creating or updating
        const isUpdate = discussionId !== '' && discussionId !== '0';
        const method = isUpdate ? 'PUT' : 'POST';
        const data = {
            title: title,
            content: content
        };
        
        // Add ID for updates
        if (isUpdate) {
            data.id = discussionId;
        }
        
        // Send AJAX request
        $.ajax({
            url: '../api/discussions.php',
            type: method,
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    // Show success message and redirect
                    showAlert('success', response.message);
                    setTimeout(function() {
                        if (isUpdate) {
                            window.location.href = 'view_discussion.php?id=' + discussionId;
                        } else {
                            window.location.href = 'view_discussion.php?id=' + response.discussionId;
                        }
                    }, 1500);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while saving the discussion.');
            }
        });
    });
});

// Helper function to show alerts
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove any existing alerts
    $('.alert').remove();
    
    // Add the new alert at the top of the container
    $('.container').prepend(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}