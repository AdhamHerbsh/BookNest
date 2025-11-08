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
                    <a href="?auth=login" class="btn btn-light rounded-circle">
                        <i class="bi fs-4 bi-person"></i>
                    </a>
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