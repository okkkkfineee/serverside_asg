<nav class="navbar navbar-expand-lg custom-header">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto">
            <li class="nav-item ps-2"><a class="nav-link p-0" href="homepage"><img src="../assets/images/icon.png" alt="Logo" width="40" height="40">Recipe.com</a></li>
            <li class="nav-item"><a class="nav-link" href="#">&nbsp;|</a></li>
            <li class="nav-item"><a class="nav-link" href="homepage">Homepage</a></li>
            <li class="nav-item"><a class="nav-link" href="recipe_list?page=1">Recipes</a></li>
            <li class="nav-item"><a class="nav-link" href="competitions">Events</a></li>
            <li class="nav-item"><a class="nav-link" href="meal_planner">Meal Planner</a></li>
            <li class="nav-item"><a class="nav-link" href="forums">Forums</a></li>
        </ul>
        <div class="pe-2">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['roles']): ?>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $_SESSION['username']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="profile">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout">Logout</a></li>
                    </ul>
                </div>
            <?php else : ?>
                <a href="entry?type=login"><button class="btn btn-primary me-2">Get Started!</button></a>
            <?php endif; ?>
        </div>
    </div>
</nav>