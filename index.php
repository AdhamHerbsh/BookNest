<?php

declare(strict_types=1);

// Include authentication system
require_once 'core/auth/security.php';
require_once 'core/auth/session.php';

// Initialize session
initSession();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'core/auth/auth_handler.php';
    exit;
}

// Routing logic
$auth = filter_input(INPUT_GET, 'auth', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$admin = filter_input(INPUT_GET, 'admin', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/aos.css" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css" />
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/custom.rtl.css" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/favicon-96x96.png" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/android-icon-192x192.png" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/android-icon-144x144.png" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/android-icon-96x96.png" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/android-icon-72x72.png" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/android-icon-48x48.png" type="image/x-icon" />
    <link rel="shortcut icon" href="assets/images/BookNest Logo/favicon/android-icon-36x36.png" type="image/x-icon" />

    <title>Book Nest</title>
</head>

<body>


    <!-- Loading Overlay -->
    <div id="preloader" class="loading-overlay">
        <div class="loading-content text-center">
            <img width="100" height="100" src="assets/images/BookNest Logo/Logo Icon Square RBG.png"
                alt="BookNest Logo" />
            <div class="spinner-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden" data-i18n="index.loading_spinner">Loading...</span>
                </div>
            </div>
            <p class="loading-text mt-3" data-i18n="index.loading_text">Loading your reading adventure...</p>
        </div>
    </div>


    <?php
    if ($auth) {
        switch ($auth) {
            case 'login':
                include "core/auth/login.php";
                break;
            case 'register':
                include "core/auth/register.php";
                break;
            case 'forgot-password':
                include "core/auth/forgot-password.php";
                break;
            default:
                include "core/pages/404.php";
                break;
        }
    } elseif ($admin) {
        if (isAdmin()) {
            // Admin routing
            require "core/layout/header.php";
            switch ($admin) {
                case 'dashboard':
                    include "core/admin/dashboard.php";
                    break;
                case 'users':
                    include "core/admin/users.php";
                    break;
                case 'children':
                    include "core/admin/children.php";
                    break;
                case 'books':
                    include "core/admin/books.php";
                    break;
                case 'quizzes':
                    include "core/admin/quizzes.php";
                    break;
                default:
                    include "core/pages/404.php";
                    break;
            }
            require "core/layout/footer.php";
        } else {
            include "core/pages/401.php";
        }
    } elseif ($page) {

        // Whitelist allowed pages to prevent LFI (Local File Inclusion)
        $allowedPages = [
            'landing',
            'library',
            'about',
            'contact',
            'book',
            'licenses',
            'privacy'
        ];
        $unallowedPages = [
            'account',
            'read',
            'quiz',
            'edu',
            'books',
            'upload',
            'quizzes',
            'create-quiz'
        ];

        require "core/layout/header.php";
        if (in_array($page, $allowedPages)) {
            include "core/pages/{$page}.php";
        } elseif (in_array($page, $unallowedPages)) {
            if (isLoggedIn()) {
                include "core/pages/{$page}.php";
            } else {
                include "core/pages/401.php";
            }
        } else {
            include "core/pages/404.php";
        }
        require "core/layout/footer.php";
    } else {
        require "core/layout/header.php";
        include "core/pages/landing.php";
        require "core/layout/footer.php";
    }
    ?>

    <script src="assets/js/aos.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>