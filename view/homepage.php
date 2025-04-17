<?php

session_start();
require '../config/db_connection.php';
require '../controller/recipe_controller.php';
require '../controller/comp_controller.php';

$recipeController = new RecipeController($conn);
$competitionController = new CompetitionController($conn);

$recipes = $recipeController->getAllRecipes();
$comps = $competitionController->getAllComp();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #recipeCarousel>button {
            margin: auto;
            width: 60px;
            background-color: transparent !important;
            transition: background-color 0.3s ease;
        }

        #recipeCarousel>button:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        #recipe-list>div>button, #comp-list>div>button{
            background-color: white !important;
            color: black !important;
            border-radius: 15px !important;
        }

        #recipe-list>div>button:hover, #comp-list>div>button:hover{
            filter: brightness(0.95) !important;
        }

        #recipe-list>div>button>img, #comp-list>div>img{
            border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1>Welcome to Recipe.com!</h1>
            <p class="lead">Discover, create, and share your favorite recipes.</p>
        </div>

        <div id="recipeCarousel" class="carousel slide mx-auto mb-5" data-bs-ride="carousel" style="width : 100%;">
            <div class="carousel-inner bg-dark text-white rounded overflow-hidden" style="height: 350px; display: flex; align-items: center;">
                
            <div class="carousel-item active">
                <a href="recipe_list?page=1" class="text-decoration-none text-dark w-100 h-100 d-block">
                    <div class="d-flex align-items-center h-100">
                        <div class="col-1"></div>
                        <div class="col-6 text-center">
                            <img src="../assets/images/homepage/recipe.jpg" class="img-fluid" alt="Recipes" style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="col-4">
                            <h3 class="text-light">Explore Delicious Recipes</h3>
                            <p class="text-light">Discover, create, and share your favorite recipes with the community!</p>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </a>
            </div>

            <div class="carousel-item">
                <a href="competitions_list" class="text-decoration-none text-dark w-100 h-100 d-block">
                    <div class="d-flex align-items-center h-100">
                        <div class="col-1"></div>
                        <div class="col-6 text-center">
                            <img src="../assets/images/homepage/cooking_comp.jpg" class="img-fluid" alt="Competitions" style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="col-4">
                            <h3 class="text-light">Join Fun Competitions</h3>
                            <p class="text-light">Compete with others, show off your cooking skills, and win exciting rewards!</p>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </a>
            </div>

            <div class="carousel-item">
                <a href="meal_planner" class="text-decoration-none text-dark w-100 h-100 d-block">
                    <div class="d-flex align-items-center h-100">
                        <div class="col-1"></div>
                        <div class="col-6 text-center">
                            <img src="../assets/images/homepage/meal_plan.jpg" class="img-fluid" alt="Meal Plans" style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="col-4">
                            <h3 class="text-light">Create Your Meal Plan</h3>
                            <p class="text-light">Organize your weekly meals effortlessly with our customizable meal planner.</p>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </a>
            </div>

            <div class="carousel-item">
                <a href="forums" class="text-decoration-none text-dark w-100 h-100 d-block">
                    <div class="d-flex align-items-center h-100">
                        <div class="col-1"></div>
                        <div class="col-6 text-center">
                            <img src="../assets/images/homepage/forum.png" class="img-fluid" alt="Forums" style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="col-4">
                            <h3 class="text-light">Join the Conversation</h3>
                            <p class="text-light">Chat with fellow food lovers, get cooking tips, and share your thoughts in the forum.</p>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </a>
            </div>

            <div class="carousel-item">
                <a href="entry?type=login" class="text-decoration-none text-dark w-100 h-100 d-block">
                    <div class="d-flex align-items-center h-100">
                        <div class="col-1"></div>
                        <div class="col-6 text-center">
                            <img src="../assets/images/homepage/join_us.jpg" class="img-fluid" alt="Register" style="max-height: 300px; object-fit: contain;">
                        </div>
                        <div class="col-4">
                            <h3 class="text-light">Join Us Today!</h3>
                            <p class="text-light">Create an account to unlock all features and start your cooking adventure!</p>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </a>
            </div>
        </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#recipeCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#recipeCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <hr class="mt-5 d-flex align-items-center" >

        <h4 class="text-start mt-5">Featured Recipes</h4>
        <?php
        if (!empty($recipes)) : ?>
            <div class="row mt-4" id="recipe-list">
                <?php foreach (array_slice($recipes, 0, 5) as $recipe) : ?>
                    <div class="col mb-4">
                        <button class="card shadow-sm btn p-0" style="width: 100%; height: 350px; border: none;" onclick="window.location.href='view_recipe?recipe_id=<?= $recipe['recipe_id'] ?>'">
                            <img src="../uploads/recipes/<?= $recipe['images'] ?>" class="card-img-top" alt="<?= $recipe['title'] ?>" style="width: 100%; height: 150px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">
                                <div class="d-flex align-items-top justify-content-start" style="flex: 1; padding: 0;">
                                    <h5 class="card-title text-start mb-0"><?= htmlspecialchars(substr($recipe['title'], 0, 50)) ?></h5>
                                </div>
                                <div class="d-flex justify-content-start pt-1" style="flex: 2;">
                                    <p class="card-text text-start mb-0"><?php echo htmlspecialchars(substr($recipe['description'], 0, 50)) . '...'; ?></p>
                                </div>
                                <div class="d-flex align-items-center justify-content-start" style="flex: 1; padding: 0;">
                                    <div class="w-100" style="border-radius: 15px; border: 1px solid #ccc; height: 30px;">View</div>
                                </div>
                            </div>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h4 class="text-start mt-5">Featured Competitions</h4>
        <?php
        if (!empty($comps)) : ?>
            <div class="row mt-4" id="comp-list">
                <?php foreach (array_slice($comps, 0, 4) as $comp) : ?>
                    <div class="col mb-4">
                        <button class="card shadow-sm btn p-0" style="width: 100%; height: 350px; border: none;" onclick="window.location.href='view_comp?comp_id=<?= $comp['comp_id'] ?>'">
                            <div style="height: 300px; overflow: hidden;">
                                <img src="../uploads/comp/<?= $comp['comp_image'] ?>" class="card-img-top" alt="<?= $comp['comp_title'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>    
                            <div class="card-body d-flex flex-column justify-content-between" style="height: 100%;">
                                <div class="d-flex align-items-top justify-content-start" style="flex: 1; padding: 0;">
                                    <h5 class="card-title text-start mb-0"><?= htmlspecialchars(substr($comp['comp_title'], 0, 50)) ?></h5>
                                </div>
                                <div class="d-flex justify-content-start pt-1" style="flex: 2;">
                                    <p class="card-text text-start mb-0"><?php echo htmlspecialchars(substr($comp['comp_desc'], 0, 50)) . '...'; ?></p>
                                </div>
                                <div class="d-flex align-items-center justify-content-start" style="flex: 1; padding: 0;">
                                    <div class="w-100" style="border-radius: 15px; border: 1px solid #ccc; height: 30px;">View</div>
                                </div>
                            </div>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row text-center py-5">
            <div class="col-md-6 mb-6">
                <div class="card shadow-sm h-100 py-4 rounded-4">
                    <div class="card-body">
                        <h5 class="card-title"><strong>Make Your Own Custom Meal Plans</strong></h5>
                        <p class="card-text">Create personalized meal plans based on your dietary preferences.</p>
                        <a href="meal_planner" class="btn btn-success px-5">Get Started!</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-6">
                <div class="card shadow-sm h-100 py-4 rounded-4">
                    <div class="card-body">
                        <h5 class="card-title "><strong>Join Our Forums</strong></h5>
                        <p class="card-text">Connect with other food enthusiasts and share your culinary adventures.</p>
                        <a href="forums" class="btn btn-success px-5">Join Now!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>