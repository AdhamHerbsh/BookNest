<?php require("core/db/config.php") ?>
<div class="auth container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 text-center" data-aos="zoom-in">
            <img class="auth-image img-fluid" src="assets/images/e-learning-isometric-composition.png"
                alt="E-learning illustration" width="75%" />
        </div>
        <div class="col-12 col-md-6 bg-beige bg-opacity-50" data-aos="slide-right">
            <div class="row mb-5">
                <div class="col-6 m-4">
                    <img src="assets/images/Icons/person-square.svg" alt="BookNest" width="70" />
                    <h1 class="d-inline text-primary fw-bold m-1">Login</h1>
                </div>
                <div class="col-6">
                    <img src="assets/images/BookNest Logo/Logo Square RBG.png" alt="BookNest" class="login-logo" />
                </div>
            </div>
            <!-- Login Form -->
            <form action="./" class="login-form row w-75 m-auto text-center" method="POST">
                <div class="container bg-secondary p-5 rounded-4 shadow-lg mb-2">
                    <div>
                        <!-- User Type Selection -->
                        <div class="row mb-3">
                            <div class="btn-group justify-content-center" role="group"
                                aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" id="check-parent">
                                <label class="btn btn-outline-primary" for="check-parent">PARENT</label>

                                <input type="radio" class="btn-check" id="check-child">
                                <label class="btn btn-outline-primary" for="check-child">CHILD</label>
                            </div>
                        </div>

                        <div id="parent">
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
                                <button id="togglePassword" class="btn btn-outline-secondary" type="button" tabindex="1"
                                    aria-label="Toggle password visibility"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div id="child">
                            <!-- Code with right-side icon using Bootstrap input-group -->
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" id="code" placeholder="Code"
                                    aria-label="Code">
                                <span class="btn btn-outline-secondary" tabindex="0"><i class="bi bi-123"></i></span>
                            </div>

                            <!-- Passkey with toggle button on the right -->
                            <div class="input-group mb-3">
                                <input type="password" class="form-control" id="passkey" placeholder="Passkey"
                                    aria-label="passkey">
                                <span class="btn btn-outline-secondary" tabindex="1"><i class="bi bi-key"></i></span>
                            </div>
                        </div>


                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-white" for="rememberMe">
                                Remember Me
                            </label>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="form-check mb-3">
                        <button class="btn btn-success" type="submit">Login</button>
                    </div>
                    <div class="form-check">
                        <a class="btn btn-light" href="?auth=register">Register</a>
                    </div>
                </div>
            </form>
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