<?php
/**
 * BookNest Authentication Handler
 * Processes login and registration requests
 */

// Include required files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/session.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

/**
 * Rate limiting for login attempts
 * @param string $identifier User identifier (email or IP)
 * @return array Rate limit status
 */
function checkRateLimit($identifier) {
    $rateLimitFile = __DIR__ . '/../../logs/rate_limits.json';
    $rateLimitDir = dirname($rateLimitFile);

    // Create logs directory if it doesn't exist
    if (!file_exists($rateLimitDir)) {
        mkdir($rateLimitDir, 0755, true);
    }

    $maxAttempts = 5;
    $timeWindow = 900; // 15 minutes
    $currentTime = time();

    // Read existing rate limits
    $rateLimits = [];
    if (file_exists($rateLimitFile)) {
        $json = file_get_contents($rateLimitFile);
        $rateLimits = json_decode($json, true) ?: [];
    }

    // Clean old entries
    $rateLimits = array_filter($rateLimits, function($entry) use ($currentTime, $timeWindow) {
        return ($currentTime - $entry['first_attempt']) < $timeWindow;
    });

    // Check current identifier
    $key = md5($identifier);
    if (!isset($rateLimits[$key])) {
        $rateLimits[$key] = [
            'attempts' => 0,
            'first_attempt' => $currentTime,
            'last_attempt' => $currentTime
        ];
    }

    $rateLimits[$key]['attempts']++;
    $rateLimits[$key]['last_attempt'] = $currentTime;

    // Save updated rate limits
    file_put_contents($rateLimitFile, json_encode($rateLimits), LOCK_EX);

    $remainingAttempts = max(0, $maxAttempts - $rateLimits[$key]['attempts']);
    $resetTime = $rateLimits[$key]['first_attempt'] + $timeWindow;

    return [
        'blocked' => $rateLimits[$key]['attempts'] >= $maxAttempts,
        'remaining_attempts' => $remainingAttempts,
        'reset_time' => $resetTime,
        'time_until_reset' => max(0, $resetTime - $currentTime)
    ];
}

/**
 * Process user registration
 * @param array $data Registration form data
 * @return array Result with success status and message
 */
function processRegistration($data) {
    $errors = [];

    try {
        // Validate CSRF token
        if (!isset($data['csrf_token']) || !verifyCsrfToken($data['csrf_token'])) {
            return ['success' => false, 'message' => 'Invalid form submission. Please try again.'];
        }

        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'username', 'password', 'confirm_password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Validate names
        $firstNameValidation = validateName($data['first_name'] ?? '');
        if (!$firstNameValidation['valid']) {
            $errors['first_name'] = $firstNameValidation['message'];
        }

        $lastNameValidation = validateName($data['last_name'] ?? '');
        if (!$lastNameValidation['valid']) {
            $errors['last_name'] = $lastNameValidation['message'];
        }

        // Validate email
        if (!empty($data['username'])) {
            if (!validateEmail($data['username'])) {
                $errors['username'] = 'Please enter a valid email address';
            }
        }

        // Validate password
        if (!empty($data['password'])) {
            $passwordValidation = validatePasswordStrength($data['password']);
            if (!$passwordValidation['valid']) {
                $errors['password'] = $passwordValidation['message'];
            }
        }

        // Check password confirmation
        if (!empty($data['password']) && !empty($data['confirm_password'])) {
            if ($data['password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
        }

        // Validate phone (optional)
        $phoneValidation = validatePhone($data['phone'] ?? '');
        if (!$phoneValidation['valid']) {
            $errors['phone'] = $phoneValidation['message'];
        }

        // Validate terms agreement
        if (empty($data['terms'])) {
            $errors['terms'] = 'You must agree to the terms and conditions';
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            return ['success' => false, 'message' => 'Please correct the errors below', 'errors' => $errors];
        }

        // Check rate limiting
        $rateLimit = checkRateLimit($data['username']);
        if ($rateLimit['blocked']) {
            return [
                'success' => false,
                'message' => 'Too many registration attempts. Please try again in ' . ceil($rateLimit['time_until_reset'] / 60) . ' minutes.'
            ];
        }

        // Check if username already exists
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT ID FROM Users WHERE USERNAME = ?");
        $stmt->execute([$data['username']]);

        if ($stmt->fetch()) {
            $errors['username'] = 'This email address is already registered';
            return ['success' => false, 'message' => 'Email already exists', 'errors' => $errors];
        }

        // Hash password
        $hashedPassword = hashPassword($data['password']);

        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO Users (FIRST_NAME, LAST_NAME, USERNAME, PASSWORD, PHONE, IS_SUBSCRIBED, ROLE_ID)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $isSubscribed = !empty($data['subscribe']) ? 1 : 0;

        $stmt->execute([
            sanitizeInput($data['first_name']),
            sanitizeInput($data['last_name']),
            sanitizeInput($data['username']),
            $hashedPassword,
            !empty($data['phone']) ? sanitizeInput($data['phone']) : null,
            $isSubscribed,
            2 // PARENT role ID
        ]);

        $userId = $pdo->lastInsertId();

        // Log successful registration
        logAuth('register', $userId, $data['username']);

        return [
            'success' => true,
            'message' => 'Registration successful! You can now log in.',
            'user_id' => $userId
        ];

    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}

/**
 * Process user login
 * @param array $data Login form data
 * @return array Result with success status and message
 */
function processLogin($data) {
    $errors = [];

    try {
        // Validate CSRF token
        if (!isset($data['csrf_token']) || !verifyCsrfToken($data['csrf_token'])) {
            return ['success' => false, 'message' => 'Invalid form submission. Please try again.'];
        }

        $userType = $data['user_type'] ?? 'parent';

        if ($userType === 'parent') {
            return processParentLogin($data);
        } elseif ($userType === 'child') {
            return processChildLogin($data);
        } else {
            return ['success' => false, 'message' => 'Invalid user type selected.'];
        }

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed. Please try again.'];
    }
}

/**
 * Process parent login (email/password)
 * @param array $data Login data
 * @return array Login result
 */
function processParentLogin($data) {
    $errors = [];

    // Validate required fields
    if (empty($data['username'])) {
        $errors['username'] = 'Email is required';
    } elseif (!validateEmail($data['username'])) {
        $errors['username'] = 'Please enter a valid email address';
    }

    if (empty($data['password'])) {
        $errors['password'] = 'Password is required';
    }

    if (!empty($errors)) {
        return ['success' => false, 'message' => 'Please correct the errors below', 'errors' => $errors];
    }

    // Check rate limiting
    $rateLimit = checkRateLimit($data['username']);
    if ($rateLimit['blocked']) {
        return [
            'success' => false,
            'message' => 'Too many login attempts. Please try again in ' . ceil($rateLimit['time_until_reset'] / 60) . ' minutes.'
        ];
    }

    // Get database connection
    $pdo = getDatabaseConnection();

    // Find user with role information
    $stmt = $pdo->prepare("
        SELECT u.*, r.NAME as ROLE_NAME
        FROM Users u
        JOIN Roles r ON u.ROLE_ID = r.ID
        WHERE u.USERNAME = ? AND r.NAME IN ('PARENT', 'ADMIN', 'EDU')
    ");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();

    if (!$user || !verifyPassword($data['password'], $user['PASSWORD'])) {
        // Log failed login attempt
        logAuth('failed_login', 0, $data['username'], 'Invalid credentials');
        return ['success' => false, 'message' => 'Invalid email or password'];
    }

    // Login successful - create session
    loginUser($user);

    return [
        'success' => true,
        'message' => 'Login successful!',
        'redirect' => getUserDashboard($user['ROLE_NAME'])
    ];
}

/**
 * Process child login (code/passkey)
 * @param array $data Login data
 * @return array Login result
 */
function processChildLogin($data) {
    $errors = [];

    // Validate required fields
    $codeValidation = validateChildCode($data['child_code'] ?? '');
    if (!$codeValidation['valid']) {
        $errors['child_code'] = $codeValidation['message'];
    }

    $passkeyValidation = validateChildPasskey($data['child_passkey'] ?? '');
    if (!$passkeyValidation['valid']) {
        $errors['child_passkey'] = $passkeyValidation['message'];
    }

    if (!empty($errors)) {
        return ['success' => false, 'message' => 'Please correct the errors below', 'errors' => $errors];
    }

    // Check rate limiting
    $rateLimit = checkRateLimit($data['child_code']);
    if ($rateLimit['blocked']) {
        return [
            'success' => false,
            'message' => 'Too many login attempts. Please try again in ' . ceil($rateLimit['time_until_reset'] / 60) . ' minutes.'
        ];
    }

    // Get database connection
    $pdo = getDatabaseConnection();

    // Find child user - code can match ID or username
    $stmt = $pdo->prepare("
        SELECT u.*, r.NAME as ROLE_NAME
        FROM Users u
        JOIN Roles r ON u.ROLE_ID = r.ID
        WHERE r.NAME = 'CHILD' AND (
            u.ID = ? OR u.USERNAME = ? OR u.FIRST_NAME = ?
        )
    ");
    $stmt->execute([
        sanitizeInput($data['child_code']),
        sanitizeInput($data['child_code']),
        sanitizeInput($data['child_code'])
    ]);
    $user = $stmt->fetch();

    if (!$user) {
        logAuth('failed_login', 0, $data['child_code'], 'Child code not found');
        return ['success' => false, 'message' => 'Invalid child code or passkey'];
    }

    // For child users, passkey can be:
    // 1. The PASSWORD field if set
    // 2. A combination of ID and name
    // 3. A simple passkey stored in USERNAME or FIRST_NAME
    $validPasskey = false;

    if ($user['PASSWORD'] && verifyPassword($data['child_passkey'], $user['PASSWORD'])) {
        $validPasskey = true;
    } elseif ($data['child_passkey'] === $user['USERNAME']) {
        $validPasskey = true;
    } elseif ($data['child_passkey'] === $user['FIRST_NAME']) {
        $validPasskey = true;
    } elseif (hash('sha256', $data['child_passkey'] . $user['ID']) === $user['PASSWORD']) {
        $validPasskey = true;
    }

    if (!$validPasskey) {
        logAuth('failed_login', $user['ID'], $user['USERNAME'], 'Invalid child passkey');
        return ['success' => false, 'message' => 'Invalid child code or passkey'];
    }

    // Login successful - create session
    loginUser($user);

    return [
        'success' => true,
        'message' => 'Login successful!',
        'redirect' => getUserDashboard($user['ROLE_NAME'])
    ];
}

/**
 * Get dashboard URL based on user role
 * @param string $role User role
 * @return string Dashboard URL
 */
function getUserDashboard($role) {
    switch (strtoupper($role)) {
        case 'CHILD':
            return 'index.php?page=library';
        case 'PARENT':
            return 'index.php?page=account';
        case 'ADMIN':
        case 'EDU':
            return 'index.php?page=account';
        default:
            return 'index.php';
    }
}

/**
 * Handle logout request
 * @return array Logout result
 */
function processLogout() {
    logoutUser();
    return ['success' => true, 'message' => 'You have been logged out successfully'];
}

// Process incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $response = ['success' => false, 'message' => 'Invalid request'];

    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'register':
                $response = processRegistration($_POST);
                break;
            case 'login':
                $response = processLogin($_POST);
                break;
            case 'logout':
                $response = processLogout();
                break;
            default:
                $response = ['success' => false, 'message' => 'Unknown action'];
        }
    } catch (Exception $e) {
        error_log("Auth handler error: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'An error occurred. Please try again.'];
    }

    echo json_encode($response);
    exit;
}
?>