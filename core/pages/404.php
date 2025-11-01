<div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="text-center">
        <div class="mb-4 error-image">
            <img src="assets/images/BookNest Logo/Logo Icon Square RBG.png" alt="404 BookNest" class="img-fluid mb-4"
                style="max-width: 200px;" />
            <h1 class="display-1 fw-bold text-primary mb-3">Oops!</h1>
            <h2 class="display-6 mb-3">Page Not Found</h2>
            <p class="lead text-muted mb-4">
                Looks like this page flew away to another nest! Don't worry, let's help you find your way back to your
                reading adventure.
            </p>
        </div>

        <div class="error-actions d-flex justify-content-center gap-3 flex-wrap">
            <a href="?page=landing" class="btn btn-primary btn-lg px-4 py-2 shadow-sm">
                <i class="bi bi-house-door me-2"></i>
                Return Home
            </a>
            <a href="/library" class="btn btn-outline-primary btn-lg px-4 py-2">
                <i class="bi bi-book me-2"></i>
                Visit Library
            </a>
        </div>
    </div>
</div>

<style>
    .error-image img {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }
</style>