<div class="auth container-fluid">
    <div class="row">
        <div class="col-12 col-md-6 bg-beige bg-opacity-50" data-aos="slide-right" style="height: 100vh;">
            <div class="row">
                <div class="col-6 m-4">
                    <img src="assets/images/Icons/person-bounding-box.svg" alt="BookNest" width="70" />
                    <h1 class="d-inline text-primary fw-bold m-1">Register</h1>
                </div>
                <div class="col-6">
                    <img src="assets/images/BookNest Logo/Logo Square RBG.png" alt="BookNest" class="login-logo" />
                </div>
            </div>
            <div class="row w-75 m-auto text-center">
                <h6>
                    Register a new account as parent has personal account
                </h6>
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


                    <!-- Login Form -->
                    <form class="login-form">

                        <div class="input-group mb-3">
                            <input type="text" class="form-control me-1" id="firstName" placeholder="First Name"
                                aria-label="First Name">
                            <input type="text" class="form-control" id="lastName" placeholder="Last Name"
                                aria-label="Last Name">
                            <span class="btn btn-outline-secondary" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="20" height="20" fill="currentColor" class="bi bi-person-vcard"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                                    <path
                                        d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
                                </svg></span>
                        </div>

                        <!-- Username with right-side icon using Bootstrap input-group -->
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" id="username" placeholder="Username"
                                aria-label="Username">
                            <span class="btn btn-outline-secondary" tabindex="0"><i class="bi bi-person"></i></span>
                        </div>

                        <!-- Password with toggle button on the right -->
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" aria-label="Password" required minlength="8"
                                autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary toggle-password-btn"
                                data-target="#password" tabindex="-1" aria-label="Toggle password visibility"><i
                                    class="bi bi-eye"></i></button>
                        </div>

                        <!-- Confirm Password with toggle button on the right -->
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" id="cpassword" name="cpassword"
                                placeholder="Confirm Password" aria-label="Confirm Password" required minlength="8"
                                autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary toggle-password-btn"
                                data-target="#cpassword" tabindex="-1"
                                aria-label="Toggle confirm password visibility"><i class="bi bi-eye"></i></button>
                        </div>

                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" id="Terms-Conditions">
                            <label class="form-check-label text-white" for="Terms-Conditions">
                                Approve on Terms and Conditions
                            </label>
                        </div>
                    </form>
                </div>
                <div class="container">
                    <div class="form-check mb-3">
                        <button class="btn btn-success" type="submit">Register</button>
                    </div>
                    <div class="form-check">
                        <a class="btn btn-light" href="?auth=login">Login</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12 col-md-6 text-center" data-aos="zoom-in">
            <img class="auth-image img-fluid" src="assets/images/25298 RBG.png" alt="E-learning illustration"
                width="75%" />
        </div>
    </div>
</div>