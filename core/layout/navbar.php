<?php
// Include authentication system
require_once 'session.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}
?>
<header class="border-bottom fixed-top bg-white shadow-sm" data-aos="slide-down">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-auto d-flex align-items-center">
                <div class="logo">
                    <img src="assets/images/BookNest Logo/Logo Icon Square RBG.png" alt="Book Nest Logo" />
                </div>
                <div>
                    <h4 class="m-0 fw-bold">BookNest</h4>
                    <small>Digital Library For Children</small>
                </div>
            </div>
            <div class="col d-none d-lg-flex justify-content-end">
                <nav>
                    <a href="?page=landing"
                        class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'landing' ? 'text-primary' : 'text-black' ?>">Home</a>
                    <a href="?page=library"
                        class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'library' ? 'text-primary' : 'text-black' ?>">Library</a>
                    <a href="?page=about"
                        class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'about' ? 'text-primary' : 'text-black' ?>">About</a>
                    <button class="btn btn-light rounded-pill py-2 px-3 text-black">
                        Start Reading
                    </button>
                    <button class="btn btn-light rounded-pill py-2 px-3 text-black">
                        For Parents
                    </button>
                    <button id="globeBtn" class="btn btn-light rounded-circle">
                        <i class="bi fs-4 bi-globe"></i>
                    </button>

                    <?php if (isLoggedIn()): ?>
                        <!-- User is logged in -->
                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill py-2 px-3 dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                <small class="text-muted">(<?php echo htmlspecialchars(ucfirst(strtolower($_SESSION['role']))); ?>)</small>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><h6 class="dropdown-header"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
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
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="logout">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
                                        <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1rem; cursor: pointer;">
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
            <div class="col-auto d-flex align-items-center">
                <button class="btn btn-light rounded-circle fw-bold d-lg-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarContent">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>
    </div>
    <!-- Wrap nav and buttons in collapsible div -->
    <div class="collapse navbar-collapse" id="navbarContent">
        <div class="col d-lg-flex justify-content-center d-block">
            <nav class="text-center">
                <a href="?page=landing"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'landing' ? 'text-primary' : 'text-black' ?>">Home</a>
                <a href="?page=library"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'library' ? 'text-primary' : 'text-black' ?>">Library</a>
                <a href="?page=about"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'about' ? 'text-primary' : 'text-black' ?>">About</a>
            </nav>
        </div>
        <div class="col-auto d-lg-flex align-items-center gap-3 text-center">
            <button class="btn btn-light rounded-pill py-2 px-3 text-black mb-2 w-auto">
                Start Reading
            </button>
            <button class="btn btn-light rounded-pill py-2 px-3 text-black mb-2 w-auto">
                For Parents
            </button>
            <button id="globeBtn" class="btn btn-light rounded-circle d-inline-block mb-2">
                <i class="bi fs-4 bi-globe"></i>
            </button>
            <a href="?auth=login" class="btn btn-light rounded-circle d-inline-block mb-2">
                <i class="bi fs-4 bi-person"></i>
            </a>
        </div>
    </div>
</header>