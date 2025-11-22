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
            <img width="100" hieght="100" src="assets/images/BookNest Logo/Logo Icon Square RBG.png"
                alt="image logo icon not found" />
            <div class="spinner-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <p class="loading-text mt-3">Loading your reading adventure...</p>
        </div>
    </div>

    <?php
    // Include authentication system
    require_once 'core/auth/session.php';

    // Initialize session
    initSession();

    // Process form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once 'core/auth/auth_handler.php';
        exit;
    }

    // Routing logic
    $auth = isset($_GET['auth']) ? $_GET['auth'] : null;
    $page = isset($_GET['page']) ? $_GET['page'] : null;
    $admin = isset($_GET['admin']) ? $_GET['admin'] : null;


    if ($auth) {
        switch ($auth) {
            case 'login':
                include("core/auth/login.php");
                break;
            case 'register':
                include("core/auth/register.php");
                break;
            default:
                include("core/pages/404.php");
                break;
        }
    } elseif ($admin) {
        // Admin routing
        require("core/layout/navbar.php");
        switch ($admin) {
            case 'dashboard':
                include("core/admin/dashboard.php");
                break;
            case 'users':
                include("core/admin/users.php");
                break;
            case 'books':
                include("core/admin/books.php");
                break;
            case 'quizzes':
                include("core/admin/quizzes.php");
                break;
            default:
                include("core/pages/404.php");
                break;
        }
        require("core/layout/footer.php");
    } elseif ($page) {
        require("core/layout/navbar.php");
        switch ($page) {
            case 'landing':
                include("core/pages/landing.php");
                break;
            case 'library':
                include("core/pages/library.php");
                break;
            case 'about':
                include("core/pages/about.php");
                break;
            case 'contact':
                include("core/pages/contact.php");
                break;
            case 'account':
                include("core/pages/account.php");
                break;
            case 'book':
                include("core/pages/book.php");
                break;
            case 'read':
                include("core/pages/read.php");
                break;
            case 'quiz':
                include("core/pages/quiz.php");
                break;
            case 'edu':
                include("core/pages/edu.php");
                break;
            case 'books':
                include("core/pages/books.php");
                break;
            case 'upload':
                include("core/pages/upload.php");
                break;
            case 'quizzes':
                include("core/pages/quizzes.php");
                break;
            case 'create-quiz':
                include("core/pages/create-quiz.php");
                break;
            default:
                include("core/pages/404.php");
                break;
        }
        require("core/layout/footer.php");
    } else {
        require("core/layout/navbar.php");
        include("core/pages/landing.php");
        include("core/layout/footer.php");
    }

    ?>
</body>

<script src="assets/js/main.js"></script>
<script src="assets/js/aos.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/swiper-bundle.min.js"></script>


</html>