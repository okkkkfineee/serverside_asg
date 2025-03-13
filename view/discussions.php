<?php
session_start();

require_once '../includes/header.php';
require_once '../includes/auth.php';
require '../controller/discussion_controller.php';
require '../controller/recipe_rating_controller.php';
require '../controller/discussion_rating_controller.php';
require '../config/db_connection.php';

$discussionController = new DiscussionController($conn);
$recipeRatingController = new RecipeRatingController($conn);
$discussionRatingController = new DiscussionRatingController($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        .user-btn { position: absolute; top: 10px; right: 10px; }
        .recipe-image { max-width: 200px; margin-top: 10px; }
        #recipeSearchResults { position: absolute; z-index: 1000; background: white; border: 1px solid #ccc; max-height: 200px; overflow-y: auto; display: none; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div id="alertContainer"></div>
        <!-- Removed empty if block for clarity -->
        <nav aria-label="breadcrumb" id="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Discussions</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-4" id="discussionListColumn">
                <div class="d-flex justify-content-between mb-3">
                    <h1>Discussions</h1>
                    <?php if (isLoggedIn()): ?>
                        <button class="btn btn-primary" id="showCreateForm">Create New</button>
                    <?php endif; ?>
                </div>
                <form class="search-form mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search discussions...">
                        <button type="submit" class="btn btn-outline-secondary">Search</button>
                    </div>
                </form>
                <div class="list-group" id="discussionList"></div>
                <nav aria-label="pagination" class="mt-3">
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>

            <div class="col-md-8" id="discussionDetailColumn">
                <div id="defaultMessage" class="card">
                    <div class="card-body">
                        <p>Select a discussion or create a new one.</p>
                    </div>
                </div>

                <div id="discussionFormContainer" class="card" style="display: none;">
                    <div class="card-header">
                        <h3 class="mb-0" id="formTitle">Create New Discussion</h3>
                    </div>
                    <div class="card-body">
                        <form id="discussionForm">
                            <input type="hidden" id="discussionId" name="discussion_id">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                            </div>
                            <div class="mb-3 position-relative">
                                <label for="recipe_id" class="form-label">Select Recipe (Search)</label>
                                <input type="text" class="form-control" id="recipeSearch" placeholder="Search recipes...">
                                <div id="recipeSearchResults"></div>
                                <input type="hidden" id="recipe_id" name="recipe_id">
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitButton">Create</button>
                            <button type="button" class="btn btn-secondary" id="cancelForm">Cancel</button>
                        </form>
                    </div>
                </div>

                <div id="discussionDetail" class="card" style="display: none;">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="mb-0" id="discussionTitle"></h3>
                        <div id="discussionActions"></div>
                    </div>
                    <div class="card-body">
                        <small class="text-muted" id="discussionMeta"></small>
                        <div class="mt-3" id="discussionContent"></div>
                        <div id="recipeImage" class="mt-3"></div>
                        <div class="mt-3">
                            <h5>Recipe Rating (if applicable)</h5>
                            <p id="recipeAverageRating">Recipe Average: Loading...</p>
                            <?php if (isLoggedIn()): ?>
                                <form id="recipeRatingForm" class="d-inline">
                                    <input type="hidden" name="recipe_id" id="recipeRatingRecipeId">
                                    <label for="recipeRatingSelect" class="me-2">Rate Recipe (1-5):</label>
                                    <select name="rating" class="form-select d-inline w-auto" id="recipeRatingSelect">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm ms-2">Rate Recipe</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <h5>Discussion Rating</h5>
                            <p id="discussionRatingDistribution">Relevance: Loading...</p>
                            <?php if (isLoggedIn() && isset($_SESSION['user_id'])): ?>
                                <!-- Fixed dynamic check for isOwner -->
                                <form id="discussionRatingForm" class="d-inline" style="display: none;" data-owner-check="false">
                                    <input type="hidden" name="discussion_id" id="discussionRatingDiscussionId">
                                    <label for="discussionRatingSelect" class="me-2">Rate Relevance:</label>
                                    <select name="rating" class="form-select d-inline w-auto" id="discussionRatingSelect">
                                        <option value="relevant">Relevant</option>
                                        <option value="slightly_relevant">Slightly Relevant</option>
                                        <option value="totally_irrelevant">Totally Irrelevant</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm ms-2">Rate Discussion</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4">
                            <h5>Comments</h5>
                            <?php if (isLoggedIn()): ?>
                                <form id="commentForm" class="mb-3">
                                    <input type="hidden" name="discussion_id" id="commentDiscussionId">
                                    <textarea class="form-control" name="comment_text" rows="3" required></textarea>
                                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                </form>
                            <?php endif; ?>
                            <div id="commentsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this discussion?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        let currentDiscussionId = null;

        // Check if jQuery loaded
        if (typeof $ === 'undefined') {
            console.error('jQuery failed to load.');
            return;
        }

        function loadDiscussions(page = 1, search = '') {
            $.ajax({
                url: '/serverside/api/discussions.php',
                type: 'GET',
                data: { limit: 10, offset: (page - 1) * 10, search: search },
                success: function(response) {
                    if (response.success) {
                        $('#discussionList').html(response.data.map(d => `
                            <a href="#" class="list-group-item list-group-item-action discussion-item" data-id="${d.discussion_id}">
                                <h5>${d.title}</h5>
                                <p>${d.content.substring(0, 100)}${d.content.length > 100 ? '...' : ''}</p>
                                <small>By: ${d.username} | ${d.comment_count} comments</small>
                            </a>`).join(''));
                        let pagination = '';
                        const totalPages = Math.ceil(response.total / 10);
                        if (page > 1) pagination += `<li class="page-item"><a class="page-link" href="#" data-page="${page - 1}">Previous</a></li>`;
                        for (let i = 1; i <= totalPages; i++) pagination += `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                        if (page < totalPages) pagination += `<li class="page-item"><a class="page-link" href="#" data-page="${page + 1}">Next</a></li>`;
                        $('#pagination').html(pagination);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in loadDiscussions: ' + error);
                }
            });
        }

        function loadDiscussion(id) {
            $.ajax({
                url: '/serverside/api/discussions.php?id=' + id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const d = response.data;
                        currentDiscussionId = d.discussion_id;
                        $('#breadcrumb').html(`<ol class="breadcrumb"><li class="breadcrumb-item"><a href="#" id="backToList">Discussions</a></li><li class="breadcrumb-item active">${d.title}</li></ol>`);
                        $('#discussionTitle').text(d.title);
                        $('#discussionMeta').text(`By ${d.username} on ${new Date(d.created_time).toLocaleString()}`);
                        $('#discussionContent').html(d.content.replace(/\n/g, '<br>'));
                        $('#recipeRatingRecipeId').val(d.recipe_id || '');
                        $('#discussionRatingDiscussionId, #commentDiscussionId').val(id);
                        $('#discussionDetail').show();
                        $('#defaultMessage, #discussionFormContainer').hide();
                        const isOwner = <?php echo isLoggedIn() ? 'd.user_id === ' . ($_SESSION['user_id'] ?? 'null') : 'false'; ?>;
                        const isAdmin = <?php echo isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin' ? 'true' : 'false'; ?>;
                        $('#discussionActions').html((isOwner || isAdmin) ? `
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${id}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${id}">Delete</button>` : '');
                        $('#recipeImage').html(d.recipe_id ? `<img src="${d.images}" class="recipe-image" alt="Recipe Image">` : '');
                        // Show/hide discussion rating form based on ownership
                        if (!isOwner && <?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
                            $('#discussionRatingForm').show();
                        } else {
                            $('#discussionRatingForm').hide();
                        }
                        loadComments(id);
                        loadRecipeRating(d.recipe_id);
                        loadDiscussionRating(id);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in loadDiscussion: ' + error);
                }
            });
        }

        function loadComments(id) {
            $.ajax({
                url: '../api/comments.php?discussion_id=' + id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#commentsList').html(response.data.map(c => `
                            <div class="card mb-2">
                                <div class="card-body">
                                    <small>${c.username} - ${new Date(c.created_time).toLocaleString()}</small>
                                    <p>${c.comment_text.replace(/\n/g, '<br>')}</p>
                                </div>
                            </div>`).join('') || '<div class="alert alert-info">No comments yet.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in loadComments: ' + error);
                }
            });
        }

        function loadRecipeRating(recipeId) {
            if (recipeId) {
                $.ajax({
                    url: '../api/recipe_ratings.php?recipe_id=' + recipeId + '&user=true',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const avgRating = response.data.avg_rating ? parseFloat(response.data.avg_rating).toFixed(1) : 'N/A';
                            $('#recipeAverageRating').text(`Recipe Average: ${avgRating} (based on ${response.data.rating_count || 0} ratings)`);
                            if (response.data.user_rating) $('#recipeRatingSelect').val(response.data.user_rating);
                        } else {
                            $('#recipeAverageRating').text('Recipe Average: N/A (0 ratings)');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error in loadRecipeRating: ' + error);
                    }
                });
            } else {
                $('#recipeAverageRating').text('No recipe associated');
                $('#recipeRatingForm').hide();
            }
        }

        function loadDiscussionRating(id) {
            $.ajax({
                url: '../api/discussion_ratings.php?discussion_id=' + id + '&user=true',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const distribution = response.data.reduce((acc, curr) => {
                            acc[curr.rating_value] = curr.count;
                            return acc;
                        }, { relevant: 0, slightly_relevant: 0, totally_irrelevant: 0 });
                        $('#discussionRatingDistribution').text(`Relevance: Relevant(${distribution.relevant}), Slightly Relevant(${distribution.slightly_relevant}), Totally Irrelevant(${distribution.totally_irrelevant})`);
                        if (response.data.user_rating) $('#discussionRatingSelect').val(response.data.user_rating);
                    } else {
                        $('#discussionRatingDistribution').text('Relevance: No ratings yet');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in loadDiscussionRating: ' + error);
                }
            });
        }

        function showForm(id = null) {
            console.log('showForm called with id:', id);
            $('#discussionFormContainer').show();
            $('#defaultMessage, #discussionDetail').hide();
            $('#breadcrumb').html(`<ol class="breadcrumb"><li class="breadcrumb-item"><a href="#" id="backToList">Discussions</a></li><li class="breadcrumb-item active">${id ? 'Edit Discussion' : 'Create New'}</li></ol>`);
            if (id) {
                $.ajax({
                    url: '../api/discussions.php?id=' + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#discussionId').val(id);
                            $('#title').val(response.data.title);
                            $('#content').val(response.data.content);
                            $('#recipe_id').val(response.data.recipe_id || '');
                            $('#formTitle').text('Edit Discussion');
                            $('#submitButton').text('Update');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error in showForm: ' + error);
                    }
                });
            } else {
                $('#discussionId').val('');
                $('#title').val('');
                $('#content').val('');
                $('#recipe_id').val('');
                $('#formTitle').text('Create New Discussion');
                $('#submitButton').text('Create');
            }
            $('#recipeSearch').val('');
            $('#recipeSearchResults').hide();
        }

        $('#recipeSearch').on('input', function() {
            const query = $(this).val();
            if (query.length < 2) {
                $('#recipeSearchResults').hide();
                return;
            }
            $.ajax({
                url: '/serverside/api/recipes.php',
                type: 'GET',
                data: { search: query, user_id: <?php echo $_SESSION['user_id'] ?? 'null'; ?> },
                success: function(response) {
                    if (response.success) {
                        $('#recipeSearchResults').html(response.data.map(r => `
                            <div class="p-2 cursor-pointer" data-id="${r.recipe_id}" data-image="${r.images}">
                                ${r.title} (Image: ${r.images})
                            </div>`).join('')).show();
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in recipeSearch: ' + error);
                }
            });
        });

        $(document).on('click', '#recipeSearchResults div', function() {
            const id = $(this).data('id');
            const image = $(this).data('image');
            $('#recipe_id').val(id);
            $('#recipeSearch').val($(this).text());
            $('#recipeSearchResults').hide();
        });

        loadDiscussions();

        $(document).on('click', '.discussion-item', function(e) {
            e.preventDefault();
            loadDiscussion($(this).data('id'));
        });

        $('#showCreateForm').on('click', function(e) {
            e.preventDefault(); // Prevent any default behavior
            console.log('Create New button clicked');
            showForm();
        });

        $(document).on('click', '#cancelForm, #backToList', function(e) {
            e.preventDefault();
            $('#discussionDetail, #discussionFormContainer').hide();
            $('#defaultMessage').show();
            $('#breadcrumb').html('<ol class="breadcrumb"><li class="breadcrumb-item active">Discussions</li></ol>');
            currentDiscussionId = null;
            loadDiscussions();
        });

        $('.search-form').on('submit', function(e) {
            e.preventDefault();
            loadDiscussions(1, $('input[name="search"]').val().trim());
        });

        $(document).on('click', '#pagination .page-link', function(e) {
            e.preventDefault();
            loadDiscussions($(this).data('page'), $('input[name="search"]').val());
        });

        $('#discussionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#discussionId').val();
            const method = id ? 'PUT' : 'POST';
            const data = {
                title: $('#title').val(),
                content: $('#content').val(),
                recipe_id: $('#recipe_id').val() || null
            };
            if (id) data.id = id;
            $.ajax({
                url: '/serverside/api/discussions.php',
                type: method,
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        loadDiscussions();
                        if (id) loadDiscussion(id);
                        else loadDiscussion(response.discussionId);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in discussionForm: ' + error);
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            $('#confirmDelete').data('id', $(this).data('id'));
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').on('click', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '../api/discussions.php?id=' + id,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        loadDiscussions();
                        $('#discussionDetail, #discussionFormContainer').hide();
                        $('#defaultMessage').show();
                        $('#deleteModal').modal('hide');
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in confirmDelete: ' + error);
                }
            });
        });

        $(document).on('click', '.edit-btn', function() {
            showForm($(this).data('id'));
        });

        $('#commentForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '../api/comments.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    discussion_id: currentDiscussionId,
                    text: $(this).find('textarea').val()
                }),
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Comment added');
                        loadComments(currentDiscussionId);
                        $(this).find('textarea').val('');
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in commentForm: ' + error);
                }
            });
        });

        $('#recipeRatingForm').on('submit', function(e) {
            e.preventDefault();
            const recipeId = $('#recipeRatingRecipeId').val();
            if (!recipeId) {
                showAlert('warning', 'No recipe associated to rate');
                return;
            }
            $.ajax({
                url: '../api/recipe_ratings.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    recipe_id: recipeId,
                    rating: $('#recipeRatingSelect').val()
                }),
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Recipe rating submitted');
                        loadRecipeRating(recipeId);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in recipeRatingForm: ' + error);
                }
            });
        });

        $('#discussionRatingForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '../api/discussion_ratings.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    discussion_id: currentDiscussionId,
                    rating: $('#discussionRatingSelect').val()
                }),
                success: function(response) {
                    if (response.success) {
                        showAlert('success', 'Discussion rating submitted');
                        loadDiscussionRating(currentDiscussionId);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error in discussionRatingForm: ' + error);
                }
            });
        });

        function showAlert(type, message) {
            $('#alertContainer').html(`<div class="alert alert-${type} alert-dismissible fade show">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
            setTimeout(() => $('.alert').alert('close'), 3000);
        }
    });
    </script>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>