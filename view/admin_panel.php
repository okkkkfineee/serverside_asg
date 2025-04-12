<?php

require '../includes/auth.php';
require '../config/db_connection.php';
require '../controller/user_controller.php';
require '../controller/recipe_controller.php';
require '../controller/comp_controller.php';

$userController = new UserController($conn);
$recipeController = new RecipeController($conn);
$compController = new CompetitionController($conn);

if (!$userController->isSuperadmin() && !$userController->isAdmin() && !$userController->isMod()) {
    header("Location: ../view/homepage.php");
    exit();
}

$comps = $compController->getAllComp();
$recipes = $recipeController->getAllRecipes();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'getList') {
        $filters = [];
        $result = [];
        $filters = [
            'search' => $_POST['search'] ?? '',
            'isSuperadmin' => $_POST['superadmin'] ?? 0,
            'isAdmin' => $_POST['admin'] ?? 0,
            'isMod' => $_POST['mod'] ?? 0,
            'isUser' => $_POST['user'] ?? 0
        ];
        $result = $userController->getUserListPagination($filters, $offset, $limit);
        $data = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);            
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'result' => $data, 'totalPages' => $totalPages]);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="icon" href="../assets/images/icon.png">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
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

        .search-bar {
            position: relative;
            margin-bottom: 20px;
        }

        .search-bar input {
            padding-left: 40px; 
        }

        .search-bar .bi-search {
            position: absolute;
            left: 15px; 
            top: 50%;
            transform: translateY(-50%); 
            color: #999; 
        }

        .form-check-label{
            font-size: 14px;
        }

        .form-control{
            font-size: 14px;
        }

        .custom-table {
            margin: 10px 100px;
        }

        @media (max-width: 991.98px) {
            .custom-table {
                margin: 10px 20px;      
            } 
        }

        td a{
            text-decoration: none;
            color: black;
        }

        button:disabled {
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="d-flex">
        <div class="sidebar">
            <?php if ($_SESSION['roles'] == "Superadmin" || $_SESSION['roles'] == "Admin"): ?>
                <a href="admin_panel?page=1" onclick="showSection('userList')" id="userListBtn">User List</a>
                <a href="#" onclick="showSection('recipePanel')" id="recipePanelBtn">Manage Recipes</a>
            <?php endif; ?>
            <a href="#" onclick="showSection('compPanel')" id="compPanelBtn">Manage Competitions</a>
        </div>

        <div class="p-4 w-100">
            <?php if ($_SESSION['roles'] == "Superadmin" || $_SESSION['roles'] == "Admin"): ?>
            <!-- User List Section -->
            <div id="userList" class="content-section">
                <div class="row pt-4 d-flex justify-content-center">
                    <div class="bg-white pt-3 rounded-3 d-flex justify-content-center w-75" style="box-shadow: 0 0 3px grey;">
                        <div class="row">
                            <div class="pb-2 fs-6">
                                <h4 class="fw-bold ps-2"><i class="bi bi-person-lines-fill"></i> User List</h4>
                            </div>
                            <div class="col-6">
                                <div class="search-bar">
                                    <i class="bi bi-search"></i>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search ID or Full Name" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="superadminCheck" value="Superadmin" checked>
                                    <label class="form-check-label" for="superadminCheck">Superadmin</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="adminCheck" value="Admin" checked>
                                    <label class="form-check-label" for="adminCheck">Admin</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="modCheck" value="Mod" checked>
                                    <label class="form-check-label" for="modCheck">Mod</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="userCheck" value="User" checked>
                                    <label class="form-check-label" for="userCheck">User</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br>

                <div class="table-responsive custom-table" id="data-container">
                    <table class="table table-bordered table-striped text-center align-middle" id="data-table">
                        <thead>
                            <tr>
                                <th class="custom-header">ID</th>
                                <th class="custom-header">Username</th>
                                <th class="custom-header">Email</th>
                                <th class="custom-header">Role</th>
                                <th class="custom-header">Edit</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                        </tbody>
                    </table>
                </div>

                <div id="user-pagination-container" class="d-flex justify-content-center">

                </div>
            </div>

            <!-- Manage Recipe Section -->
            <div id="recipePanel" class="content-section">
                <div class="container mt-4">
                    <h4 class="mb-3">Manage Recipes</h4>
                    <div class="row">
                        <?php if (empty($recipes)) : ?>
                            <div class="text-center mt-5">
                                <p class="lead">No recipes yet.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($recipes as $recipe) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xl-3 justify-content-center mb-4">
                                    <div class="card border shadow-sm" style="width: 100%; max-width: 20rem; height: 100%;">
                                        <img src="../uploads/recipes/<?php echo $recipe['images'] ?? 'default_recipe.png'; ?>" class="card-img-top" alt="Recipe Image" width="50" height="230">
                                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start" style=" flex-grow: 1;">
                                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'], 0, 80)) . '...'; ?></p>
                                            <div class="d-flex justify-content-between">
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
            <?php endif; ?>

            <!-- Manage Competitions Section -->
            <div id="compPanel" class="content-section">
                <div class="container mt-4">
                    <div class="text-center mb-4">
                        <a href="manage_comp?type=host"><button class="btn btn-primary btn-lg w-100 py-3">+ Host New Competitions</button></a>
                    </div>
                    
                    <hr>

                    <h4 class="mb-3">Manage Competitions</h4>
                    <div class="row">
                        <?php if (empty($comps)) : ?>
                            <div class="text-center mt-5">
                                <p class="lead">No Competitions yet.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($comps as $comp) : ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xl-3 justify-content-center mb-4">
                                    <div class="card border shadow-sm" style="width: 100%; max-width: 20rem; height: 100%;">
                                        <img src="<?php echo isset($comp['comp_image']) ? "../uploads/comp/" . $comp['comp_image'] : '../assets/images/default_comp.png'; ?>" class="card-img-top" alt="Competition Image" width="50" height="230">
                                        <div class="d-flex flex-column card-body justify-content-between p-3 text-start" style=" flex-grow: 1;">
                                            <h5 class="card-title"><?php echo htmlspecialchars($comp['comp_title']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars(substr(str_replace(['\\r\\n', '\\n', '\\r'], "\n", $comp['comp_desc']), 0, 80)) . '...'; ?></p>
                                            <div class="d-flex justify-content-between">
                                                <a href="manage_comp?type=edit&comp_id=<?php echo $comp['comp_id']; ?>" class="btn btn-warning">Edit</a>
                                                <a href="manage_comp?type=delete&comp_id=<?php echo $comp['comp_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this competition?');">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("userList").style.display = "block";
    </script>
    <script src="../assets/js/admin_panel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
