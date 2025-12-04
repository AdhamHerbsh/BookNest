<section class="my-5">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-12 col-md-6 text-center">
                <img src="assets/images/unauthorized-removebg-preview.png" alt="Unauthorized access" class="img-fluid"
                    style="max-width: 260px;">
            </div>
            <div class="col-12 col-md-6">
                <div class="bg-white rounded-4 shadow-sm p-4">
                    <h1 class="display-5 fw-bold text-danger mb-3" data-i18n="error_401.title">Unauthorized</h1>
                    <p class="text-muted mb-4" data-i18n="error_401.message">
                        You don’t have permission to access this page. Please log in or create an account to continue.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="index.php?auth=login" class="btn btn-primary btn-lg" data-i18n="error_401.btn_login">
                            Login
                        </a>
                        <a href="index.php?auth=register" class="btn btn-outline-primary btn-lg" data-i18n="error_401.btn_register">
                            Register
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>