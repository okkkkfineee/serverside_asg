<nav class="navbar navbar-expand-lg custom-header">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto">
            <li class="nav-item ps-2"><a class="nav-link p-0 text-light" href="homepage"><img src="../assets/images/icon.png" alt="Logo" width="40" height="40"> &nbsp;&nbsp;Recipe.com</a></li>
            <li class="nav-item"><a class="nav-link text-light" href="#">|</a></li>
            <li class="nav-item nav-item-bg"><a class="nav-link text-light" href="homepage">Homepage</a></li>
            <li class="nav-item nav-item-bg"><a class="nav-link text-light" href="recipe_list?page=1">Recipes</a></li>
            <li class="nav-item nav-item-bg"><a class="nav-link text-light" href="competitions_list">Competitions</a></li>
            <li class="nav-item nav-item-bg"><a class="nav-link text-light" href="meal_planner">Meal Planner</a></li>
            <li class="nav-item nav-item-bg"><a class="nav-link text-light" href="forums">Forums</a></li>
        </ul>
        <div class="pe-2">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['roles']): ?>
                <div class="dropdown name-dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="profile">Profile</a></li>
                        <?php if ($_SESSION['roles'] == "Superadmin" || $_SESSION['roles'] == "Admin" || $_SESSION['roles'] == "Mod"): ?>
                            <li><a class="dropdown-item" href="admin_panel">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout">Logout</a></li>
                    </ul>
                </div>
            <?php else : ?>
                <a href="entry?type=login"><button class="btn btn-primary me-2 get-started">Get Started!</button></a>
            <?php endif; ?>
        </div>
    </div>
</nav>