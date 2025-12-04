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
        <div class="col-12 col-md-6 bg-beige bg-opacity-50" data-aos="slide-right" style="height: 100vh;">
            <div class="row">
                <div class="col-6 m-4">
                    <img src="assets/images/Icons/person-bounding-box.svg" alt="BookNest" width="70" />
                    <h1 class="d-inline text-primary fw-bold m-1" data-i18n="auth.register_title">Register</h1>
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

            <!-- Register Form -->
            <form action="./" method="POST" class="row w-75 m-auto text-center" id="registerForm">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <h6 data-i18n="auth.register_subtitle">
                    Register a new account as parent has personal account
                </h6>
                <div class="container bg-secondary p-4 rounded-4 shadow-lg mb-3">
                    <div>
                        <div class="input-group mb-3">
                            <input type="text"
                                class="form-control me-1 <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>"
                                name="first_name" id="first_name" placeholder="First Name" aria-label="First Name"
                                tabindex="1" value="<?php echo htmlspecialchars($values['first_name'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_first_name">
                            <input type="text"
                                class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>"
                                name="last_name" id="last_name" placeholder="Last Name" aria-label="Last Name"
                                tabindex="2" value="<?php echo htmlspecialchars($values['last_name'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_last_name">
                            <span class="btn btn-outline-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                                    <path
                                        d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                                    <path
                                        d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
                                </svg></span>
                        </div>

                        <!-- Username (Email) with right-side icon using Bootstrap input-group -->
                        <div class="input-group mb-3">
                            <input type="email"
                                class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                                name="username" id="username" placeholder="Email Address" aria-label="Email Address"
                                tabindex="2" value="<?php echo htmlspecialchars($values['username'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_email_address">
                            <span class="btn btn-outline-secondary"><i class="bi bi-envelope"></i></span>
                            <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['username']); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Phone (optional) with right-side icon using Bootstrap input-group -->
                        <div class="input-group mb-3">
                            <input type="tel" class="form-control" name="phone" id="phone"
                                placeholder="Phone Number (Optional)" aria-label="Phone Number"
                                pattern="[0-9]{3}-?[0-9]{3}-?[0-9]{4}" tabindex="3" data-i18n-placeholder="auth.placeholder_phone_optional">
                            <span class="btn btn-outline-secondary"><i class="bi bi-telephone"></i></span>
                        </div>

                        <!-- Password with toggle button on the right -->
                        <div class="input-group mb-3">
                            <input type="password"
                                class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                                name="password" id="password" placeholder="Password" aria-label="Password" minlength="8"
                                autocomplete="new-password" tabindex="4"
                                value="<?php echo htmlspecialchars($values['password'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_password">
                            <button type="button" class="btn btn-outline-secondary toggle-password-btn"
                                data-target="#password" tabindex="5" aria-label="Toggle password visibility"><i
                                    class="bi bi-eye"></i></button>
                            <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['password']); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Confirm Password with toggle button on the right -->
                        <div class="input-group mb-3">
                            <input type="password"
                                class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>"
                                name="confirm_password" id="confirmPassword" placeholder="Confirm Password"
                                aria-label="Confirm Password" minlength="8" autocomplete="new-password" tabindex="6"
                                value="<?php echo htmlspecialchars($values['confirm_password'] ?? ''); ?>" data-i18n-placeholder="auth.placeholder_confirm_password">
                            <button type="button" class="btn btn-outline-secondary toggle-password-btn"
                                data-target="#confirmPassword" tabindex="7"
                                aria-label="Toggle confirm password visibility"><i class="bi bi-eye"></i></button>
                            <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Subscription checkbox -->
                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" id="subscribe" name="subscribe" value="1"
                                tabindex="8">
                            <label class="form-check-label text-white" for="subscribe" data-i18n="auth.label_subscribe">
                                Subscribe to newsletter and updates
                            </label>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1"
                                tabindex="9" required>
                            <label class="form-check-label text-white" for="terms">
                                <span data-i18n="auth.label_terms">I agree to the</span> <a href="#" class="text-white text-decoration-underline" data-i18n="auth.terms_link">Terms and
                                    Conditions</a>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="form-check mb-3">
                        <button class="btn btn-success" type="submit" data-i18n="auth.btn_register">Register</button>
                    </div>
                    <div class="form-check">
                        <a class="btn btn-light" href="?auth=login" data-i18n="auth.btn_login_link">Login</a>
                    </div>
                </div>
            </form>

        </div>
        <div class="col-12 col-md-6 text-center" data-aos="zoom-in">
            <img class="auth-image img-fluid" src="assets/images/25298 RBG.png" alt="E-learning illustration"
                width="75%" />
        </div>
    </div>
</div>