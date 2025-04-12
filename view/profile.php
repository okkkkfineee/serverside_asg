<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/user_controller.php';
require '../controller/recipe_controller.php';
require '../controller/comp_controller.php';

$userController = new UserController($conn);
$recipeController = new RecipeController($conn);
$compController = new CompetitionController($conn);

$user = $userController->getUserInfo($_SESSION['user_id']);
$recipes = $recipeController->getUserRecipes($_SESSION['user_id']);
$comps = $compController->getUserComp($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
    <style>
        .sidebar { 
            width: 250px;
            min-height: 100vh; 
            background: #f8f9fa; 
            padding: 20px; 
        }

        .sidebar a { 
            display: block; 
            padding: 10px; 
            text-decoration: none; 
            color: #333; 
            margin-bottom: 10px; 
        }

        .sidebar a:hover { 
            background: grey; 
            color: white !important; 
            border-radius: 5px; 
        }

        .sidebar .text-danger:hover {
            background: red;
        }

        .content-section { 
            display: none; 
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="d-flex">
        <div class="sidebar">
            <a href="#" onclick="showSection('info')" id="infoBtn">Info</a>
            <a href="#" onclick="showSection('compEntries')" id="compBtn">Competitions Entries</a>
            <a href="#" onclick="showSection('editRecipes')" id="recipesBtn">Edit Recipes</a>
            <a href="#" onclick="showSection('changePassword')" id="passwordBtn">Change Password</a>
            <a href="logout.php" class="text-danger">Logout</a>
        </div>

        <div class="p-4 w-100">
            <!-- Info Section -->
            <div id="info" class="content-section">
                <div class="container mt-4">
                    <div class="d-flex align-items-center mb-4">
                        <img src="../assets/images/profile.png" class="rounded-circle me-5" width="100" height="100" alt="Profile Picture">
                        <div>
                            <h3 class="mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                            <p class="text-muted"><?php echo htmlspecialchars($user['bio'] ?: "No bio yet."); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>

                    <hr>

                    <h4 class="mb-3 py-2">My Recipes</h4>
                    <div class="row">
                        <?php if (empty($recipes)) : ?>
                            <div class="text-center mt-5">
                                <p>No recipes yet.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($recipes as $recipe) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xl-3 justify-content-center mb-4">
                                    <div class="card border shadow-sm" style="width: 100%; max-width: 20rem; height: 100%;">
                                        <img src="../uploads/recipes/<?php echo $recipe['images'] ?? 'default_recipe.png'; ?>" class="card-img-top" alt="Recipe Image" width="50" height="230">
                                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start" >
                                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'], 0, 80)) . '...'; ?></p>
                                            <a href="view_own_recipe?recipe_id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-primary">View Recipe</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Competitions Entries Section -->
            <div id="compEntries" class="content-section">
                <div class="container mt-4">
                    <div class="text-center mb-4">
                        <a href="competitions_list"><button class="btn btn-primary btn-lg w-100 py-3">Join Competitions</button></a>
                    </div>
                    
                    <hr>

                    <h4 class="mb-3">My Competitions Entries</h4>
                    <div class="row">
                        <?php if (empty($comps)) : ?>
                            <div class="text-center mt-5">
                                <p>You have not entered any competitions yet.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($comps as $comp) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xl-3 justify-content-center mb-4">
                                    <div class="card border shadow-sm" style="width: 100%; max-width: 20rem; height: 100%;">
                                        <img src="<?= (!empty($comp['comp_image']) ? '../uploads/comp/' . $comp['comp_image'] : '../assets/images/default_comp.png'); ?>" class="card-img-top" alt="Competition Image" width="50" height="230">
                                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start" style=" flex-grow: 1;">
                                            <h5 class="card-title"><?php echo htmlspecialchars($comp['comp_title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($comp['comp_desc'], 0, 80)) . '...'; ?></p>
                                            <div class="d-flex justify-content-between">
                                                <a href="view_comp?comp_id=<?php echo $comp['comp_id']; ?>" class="btn btn-warning">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Edit Recipes Section -->
            <div id="editRecipes" class="content-section">
                <div class="container mt-4">
                    <div class="text-center mb-4">
                        <a href="manage_recipe?type=add"><button class="btn btn-primary btn-lg w-100 py-3">+ Add Recipe</button></a>
                    </div>
                    
                    <hr>

                    <h4 class="mb-3">My Recipes</h4>
                    <div class="row">
                        <?php if (empty($recipes)) : ?>
                            <div class="text-center mt-5">
                                <p>No recipes yet.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($recipes as $recipe) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xl-3 justify-content-center mb-4">
                                    <div class="card border shadow-sm" style="width: 100%; max-width: 20rem; height: 100%;">
                                        <img src="<?= (!empty($recipe['images']) ? '../uploads/recipes/' . $recipe['images'] : '../assets/images/default_recipe.png'); ?>" class="card-img-top" alt="Recipe Image" width="50" height="230">
                                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start" style=" flex-grow: 1;">
                                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'], 0, 80)) . '...'; ?></p>
                                            <div class="d-flex justify-content-between">
                                                <a href="manage_recipe?type=edit&recipe_id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-warning">Edit</a>
                                                <a href="manage_recipe?type=delete&recipe_id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div id="changePassword" class="content-section">
                <h3>Change Password</h3>
                <form method="POST" action="change_password">
                    <div class="mb-3">
                        <input type="password" class="form-control" name="old_password" placeholder="Current Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="new_password" placeholder="New Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" value="change" name="action">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("info").style.display = "block";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
