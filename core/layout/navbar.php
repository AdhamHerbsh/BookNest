<div class="col d-none d-lg-flex justify-content-end">

    <nav>
        <a href="?page=landing"
            class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'landing' ? 'text-primary' : 'text-black' ?>">Home</a>

        <?php if (isLoggedIn()) : ?>
            <?php if (isParent()) : ?>

                <a href="?page=library"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'library' ? 'text-primary' : 'text-black' ?>">Library</a>
                <a href="?page=about"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'about' ? 'text-primary' : 'text-black' ?>">About</a>

            <?php elseif (isEducator()) : ?>

                <a href="?page=books"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'books' ? 'text-primary' : 'text-black' ?>">Books</a>
                <a href="?page=quizzes"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'quizzes' ? 'text-primary' : 'text-black' ?>">Quizzes</a>

            <?php elseif (isAdmin()) : ?>

                <a href="?admin=dashboard"
                    class="nav-link d-inline-block px-3 <?= ($_GET['admin'] ?? '') == 'dashboard' ? 'text-primary' : 'text-black' ?>">Dashoard</a>
                <a href="?admin=users"
                    class="nav-link d-inline-block px-3 <?= ($_GET['admin'] ?? '') == 'users' ? 'text-primary' : 'text-black' ?>">Users</a>
                <a href="?admin=books"
                    class="nav-link d-inline-block px-3 <?= ($_GET['admin'] ?? '') == 'books' ? 'text-primary' : 'text-black' ?>">Books</a>
                <a href="?admin=quizzes"
                    class="nav-link d-inline-block px-3 <?= ($_GET['admin'] ?? '') == 'quizzes' ? 'text-primary' : 'text-black' ?>">Quizzes</a>

            <?php endif; ?>
        <?php else: ?>

            <a href="?page=library"
                class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'library' ? 'text-primary' : 'text-black' ?>">Library</a>
            <a href="?page=about"
                class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'about' ? 'text-primary' : 'text-black' ?>">About</a>
            <a href="?page=library" class="btn btn-light rounded-pill py-2 px-3 text-black">
                Start Reading
            </a>
            <a href="?page=account" class="btn btn-light rounded-pill py-2 px-3 text-black">
                For Parents
            </a>

        <?php endif; ?>
        <button id="globeBtn" class="btn btn-light rounded-circle">
            <i class="bi fs-4 bi-globe"></i>
        </button>
        <?php if (isLoggedIn()): ?>
            <!-- User is logged in -->
            <div class="dropdown d-inline">
                <button class="btn btn-light rounded-pill py-2 px-3 dropdown-toggle" type="button" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <h6 class="dropdown-header"><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </h6>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="?page=account">
                            <i class="bi bi-person me-2"></i>My Account
                        </a>
                    </li>
                    <?php if (isParent() || isAdmin()): ?>
                        <li>
                            <a class="dropdown-item" href="?page=account&tab=children">
                                <i class="bi bi-people me-2"></i>Manage Children
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (isAdmin() || isEducator()): ?>
                        <li>
                            <a class="dropdown-item" href="?page=edu">
                                <i class="bi bi-mortarboard me-2"></i>Education Tools
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="./" method="POST" class="d-inline">
                            <input type="hidden" name="action" value="logout">
                            <input type="hidden" name="csrf_token" value="<?php  // echo htmlspecialchars(generateCsrfToken()); 
                                                                            ?>">
                            <button type="submit" class="dropdown-item"
                                style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1rem; cursor: pointer;">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <!-- User is not logged in -->
            <a href="?auth=login" class="btn btn-light rounded-circle">
                <i class="bi fs-4 bi-person"></i>
            </a>
        <?php endif; ?>
    </nav>

</div>


<!-- Mobile Navbar Button -->
<div class="col-auto d-flex align-items-center">
    <button class="btn btn-light rounded-circle fw-bold d-lg-none" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarContent">
        <i class="bi bi-list"></i>
    </button>
</div>