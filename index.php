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
    <title>Book Nest</title>
</head>

<body>
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="loading-content">
            <img src="assets/images/BookNest Logo/Logo Icon Square RBG.png" alt="BookNest" class="loading-logo mb-4" />
            <div class="spinner-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <p class="loading-text mt-3">Loading your reading adventure...</p>
        </div>
    </div>

    <!-- Main Content Container -->
    <div id="main-content" class="content-blur">
        <?php
        // Routing logic
        $page = isset($_GET['page']) ? $_GET['page'] : null;
        $auth = isset($_GET['auth']) ? $_GET['auth'] : null;


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
        } elseif ($page) {
            require("core/layout/navbar.php");
            switch ($page) {
                case 'landing':
                    include("core/pages/landing.php");
                    break;
                case 'about':
                    include("core/pages/about.php");
                    break;
                case 'services':
                    include("core/pages/services.php");
                    break;
                case 'contact':
                    include("core/pages/contact.php");
                    break;
                default:
                    include("core/pages/404.php");
                    break;
            }
            require("core/layout/footer.php");
        } else {
            include("core/pages/404.php");
        }



        ?>
    </div>

    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.98);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-in-out;
        }

        .loading-content {
            text-align: center;
        }

        .loading-logo {
            width: 100px;
            height: 100px;
            animation: pulse 2s infinite;
        }

        .loading-text {
            color: #4e73df;
            font-size: 1.2rem;
            margin-top: 1rem;
            font-weight: 500;
        }

        .spinner-container {
            margin: 1rem 0;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .content-blur {
            transition: filter 0.5s ease-in-out;
        }

        .content-blur.loading {
            filter: blur(5px);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .loading-logo {
                width: 80px;
                height: 80px;
            }

            .loading-text {
                font-size: 1rem;
            }

            .spinner-border {
                width: 2rem;
                height: 2rem;
            }
        }
    </style>

</body>

<script src="assets/js/aos.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading class to main content
        const mainContent = document.getElementById('main-content');
        const loadingOverlay = document.getElementById('loading-overlay');

        mainContent.classList.add('loading');

        // Function to hide loading overlay
        function hideLoading() {
            loadingOverlay.style.opacity = '0';
            mainContent.classList.remove('loading');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500);
        }

        // Wait for all images to load
        Promise.all(
                Array.from(document.images)
                .filter(img => !img.complete)
                .map(img => new Promise(resolve => {
                    img.onload = img.onerror = resolve;
                }))
            )
            .then(() => {
                // Add a minimum delay of 1 second for better UX
                setTimeout(hideLoading, 1000);
            });

        // Fallback: Hide loading after 5 seconds maximum
        setTimeout(hideLoading, 5000);
    });
</script>

</html>