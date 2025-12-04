    <!-- Wrap nav and buttons in collapsible div -->
    <div class="collapse navbar-collapse" id="navbarContent">
        <div class="col d-lg-flex justify-content-center d-block">
            <nav class="text-center">
                <a href="?page=landing"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'landing' ? 'text-primary' : 'text-black' ?>" data-i18n="navbar.nav_home">Home</a>
                <a href="?page=library"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'library' ? 'text-primary' : 'text-black' ?>" data-i18n="navbar.nav_library">Library</a>
                <a href="?page=about"
                    class="nav-link d-inline-block px-3 <?= ($_GET['page'] ?? '') == 'about' ? 'text-primary' : 'text-black' ?>" data-i18n="navbar.nav_about">About</a>
            </nav>
        </div>
        <div class="col-auto d-lg-flex align-items-center gap-3 text-center">
            <button class="btn btn-light rounded-pill py-2 px-3 text-black mb-2 w-auto" data-i18n="navbar.btn_start_reading">
                Start Reading
            </button>
            <button class="btn btn-light rounded-pill py-2 px-3 text-black mb-2 w-auto" data-i18n="navbar.btn_for_parents">
                For Parents
            </button>
            <button id="globeBtn" class="btn btn-light rounded-circle d-inline-block mb-2">
                <i class="bi fs-4 bi-globe"></i>
            </button>

            <?php if (isLoggedIn()): ?>
                <!-- User is logged in (mobile) -->
                <div class="dropdown mb-2">
                    <button class="btn btn-light rounded-pill py-2 px-3 dropdown-toggle" type="button"
                        id="userDropdownMobile" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMobile">
                        <li>
                            <h6 class="dropdown-header"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="?page=account" data-i18n="navbar.dropdown_my_account">
                                <i class="bi bi-person me-2"></i>My Account
                            </a>
                        </li>
                        <?php if (isParent() || isAdmin()): ?>
                            <li>
                                <a class="dropdown-item" href="?page=account&tab=children" data-i18n="navbar.dropdown_manage_children">
                                    <i class="bi bi-people me-2"></i>Manage Children
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (isAdmin() || isEducator()): ?>
                            <li>
                                <a class="dropdown-item" href="?page=edu" data-i18n="navbar.dropdown_education_tools">
                                    <i class="bi bi-mortarboard me-2"></i>Education Tools
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="logout">
                                <input type="hidden" name="csrf_token" value="<?php generateCsrfToken(); ?>">
                                <button type="submit" class="dropdown-item"
                                    style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1rem; cursor: pointer;" data-i18n="navbar.dropdown_logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <!-- User is not logged in (mobile) -->
                <a href="?auth=login" class="btn btn-light rounded-circle d-inline-block mb-2">
                    <i class="bi fs-4 bi-person"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>