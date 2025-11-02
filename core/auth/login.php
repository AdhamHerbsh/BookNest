<div class="auth container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 text-center" data-aos="zoom-in">
            <img class="auth-image img-fluid" src="assets/images/e-learning-isometric-composition.png"
                alt="E-learning illustration" width="75%" />
        </div>
        <div class="col-12 col-md-6 bg-beige bg-opacity-50" data-aos="slide-right">
            <div class="row">
                <div class="col-6 m-4">
                    <img src="assets/images/Icons/person-square.svg" alt="BookNest" width="70" />
                    <h1 class="d-inline text-primary fw-bold m-1">Login</h1>
                </div>
                <div class="col-6">
                    <img src="assets/images/BookNest Logo/Logo Square RBG.png" alt="BookNest" class="login-logo" />
                </div>
            </div>
            <div class="row w-75 m-auto text-center">
                <div class="container bg-secondary p-4 rounded-4 shadow-lg mb-3">
                    <!-- Auth Type Toggles -->
                    <div class="row mb-3">
                        <div class="btn-group justify-content-center" role="group"
                            aria-label="Basic checkbox toggle button group">
                            <input type="checkbox" class="btn-check" id="check-signup" autocomplete="off">
                            <label class="btn btn-outline-primary" for="check-signup">Sign Up</label>

                            <input type="checkbox" class="btn-check" id="check-signin" autocomplete="off">
                            <label class="btn btn-outline-primary" for="check-signin">Sign In</label>
                        </div>
                    </div>
                    <!-- User Type Selection -->
                    <div class="row mb-3">
                        <div class="btn-group justify-content-center" role="group"
                            aria-label="Basic checkbox toggle button group">
                            <input type="checkbox" class="btn-check" id="check-child" autocomplete="off">
                            <label class="btn btn-outline-primary" for="check-child">CHILD</label>

                            <input type="checkbox" class="btn-check" id="check-parent" autocomplete="off">
                            <label class="btn btn-outline-primary" for="check-parent">PARENT</label>

                            <input type="checkbox" class="btn-check" id="check-edu" autocomplete="off">
                            <label class="btn btn-outline-primary" for="check-edu">EDU</label>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <form class="login-form">

                        <!-- Username with right-side icon using Bootstrap input-group -->
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" id="username" placeholder="Username"
                                aria-label="Username">
                            <span class="btn btn-outline-secondary" tabindex="0"><i class="bi bi-person"></i></span>
                        </div>

                        <!-- Password with toggle button on the right -->
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="password" placeholder="Password"
                                aria-label="Password">
                            <button id="togglePassword" class="btn btn-outline-secondary" type="button" tabindex="-1"
                                aria-label="Toggle password visibility"><i class="bi bi-eye"></i></button>
                        </div>

                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">
                                Remember Me
                            </label>
                        </div>
                    </form>
                </div>
                <div class="container">
                    <div class="form-check mb-3">
                        <button class="btn btn-success" type="submit">Login</button>
                    </div>
                    <div class="form-check">
                        <a class="btn btn-light" href="?auth=register">Register</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="container">
                    <swiper-container class="mySwiper" pagination="true" pagination-clickable="true" autoplay="true"
                        space-between="20" slides-per-view="3">
                        <swiper-slide><img class="rounded-4" src="assets/images/Books/1.jpg"
                                alt="CoverBook image not found" />
                        </swiper-slide>
                        <swiper-slide><img class="rounded-4" src="assets/images/Books/2.jpg"
                                alt="CoverBook image not found" />
                        </swiper-slide>
                        <swiper-slide><img class="rounded-4" src="assets/images/Books/3.jpg"
                                alt="CoverBook image not found" />
                        </swiper-slide>
                        <swiper-slide><img class="rounded-4" src="assets/images/Books/1.jpg"
                                alt="CoverBook image not found" />
                        </swiper-slide>
                        <swiper-slide><img class="rounded-4" src="assets/images/Books/2.jpg"
                                alt="CoverBook image not found" />
                        </swiper-slide>
                        <swiper-slide><img class="rounded-4" src="assets/images/Books/3.jpg"
                                alt="CoverBook image not found" />
                        </swiper-slide>
                    </swiper-container>
                </div>
            </div>
        </div>
    </div>
</div>