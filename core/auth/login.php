<?php
// Include authentication system
require_once 'session.php';
require_once 'security.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

// Generate CSRF token
$csrfToken = generateCsrfToken();

$errors = $_SESSION['form_errors'] ?? [];
$values = $_SESSION['form_values'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_values']);
?>
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
                    <h1 class="d-inline text-primary fw-bold m-1" data-i18n="auth.login_title">Login</h1>
                </div>
                <div class="col-6">
                    <img src="assets/images/BookNest Logo/Logo Square RBG.png" alt="BookNest" class="login-logo" />
                </div>
            </div>

            <!-- Error Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Success Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php
                            echo htmlspecialchars($_SESSION['success']);
                            unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="./" class="login-form row w-75 m-auto text-center" method="POST" id="loginForm">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="container bg-secondary p-5 rounded-4 shadow-lg mb-2">
                    <div>
                        <!-- User Type Selection -->
                        <div class="row mb-3">
                            <div class="btn-group justify-content-center" role="group"
                                aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="user_type" id="check-parent" value="parent"
                                    checked tabindex="1">
                                <label class="btn btn-outline-primary" for="check-parent" data-i18n="auth.user_type_parent">PARENT</label>

                                <input type="radio" class="btn-check" name="user_type" id="check-edu" value="edu"
                                    tabindex="2">
                                <label class="btn btn-outline-primary" for="check-edu" data-i18n="auth.user_type_edu">EDUCATOR</label>

                                <input type="radio" class="btn-check" name="user_type" id="check-child" value="child"
                                    tabindex="3">
                                <label class="btn btn-outline-primary" for="check-child" data-i18n="auth.user_type_child">CHILD</label>
                            </div>
                        </div>

                        <!-- Parent/Educator Login Section (Email + Password) -->
                        <div id="parent" class="user-type-section">
                            <!-- Username with right-side icon using Bootstrap input-group -->
                            <div class="input-group mb-3">
                                <input type="email" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" name="username" id="username"
                                    placeholder="Email" aria-label="Email" tabindex="4" value="<?php echo htmlspecialchars($values['username'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_email">
                                <span class="btn btn-outline-secondary"><i class="bi bi-person"></i></span>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['username']); ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Password with toggle button on the right -->
                            <div class="input-group mb-3">
                                <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" name="password" id="password"
                                    placeholder="Password" aria-label="Password" tabindex="5" value="<?php echo htmlspecialchars($values['password'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_password">
                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" tabindex="6"
                                    aria-label="Toggle password visibility"><i class="bi bi-eye"></i></button>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['password']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Child Login Section (Code + Passkey) -->
                        <div id="child" class="user-type-section">
                            <!-- Code with right-side icon using Bootstrap input-group -->
                            <div class="input-group mb-3">
                                <input type="text" class="form-control <?php echo isset($errors['child_code']) ? 'is-invalid' : ''; ?>" name="child_code" id="code"
                                    placeholder="Child Username/Code" aria-label="Child Code" tabindex="7" value="<?php echo htmlspecialchars($values['child_code'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_child_code">
                                <span class="btn btn-outline-secondary" tabindex="0"><i class="bi bi-123"></i></span>
                                <?php if (isset($errors['child_code'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['child_code']); ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Passkey with toggle button on the right -->
                            <div class="input-group mb-3">
                                <input type="password" class="form-control <?php echo isset($errors['child_passkey']) ? 'is-invalid' : ''; ?>" name="child_passkey" id="passkey"
                                    placeholder="Passkey" aria-label="Passkey" tabindex="8" value="<?php echo htmlspecialchars($values['child_passkey'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_passkey">
                                <button class="btn btn-outline-secondary toggle-password-btn" type="button"
                                    tabindex="9" aria-label="Toggle passkey visibility"><i
                                        class="bi bi-eye"></i></button>
                                <?php if (isset($errors['child_passkey'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['child_passkey']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>


                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe" tabindex="10">
                            <label class="form-check-label text-white" for="rememberMe" data-i18n="auth.label_remember_me">
                                Remember Me
                            </label>
                        </div>
                        <div class="mb-3">
                            <a href="?auth=forgot-password" class="btn btn-light" data-i18n="auth.btn_forgot_password">Forgot Password</a>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="form-check mb-3">
                        <button type="submit" class="btn btn-success" tabindex="11" data-i18n="auth.btn_login">Login</button>
                    </div>
                    <div class="form-check">
                        <a class="btn btn-light" href="?auth=register" tabindex="12" data-i18n="auth.btn_register">Register</a>
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