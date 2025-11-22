<?php
/**
 * BookNest Session Management
 * Handles secure session initialization and management
 */

/**
 * Initialize secure session
 */
function initSession() {
    // Set secure session parameters
    $sessionName = 'booknest_session';
    $secure = true; // Only send over HTTPS
    $httpOnly = true; // Don't allow JavaScript access
    $sameSite = 'Strict'; // Prevent CSRF

    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 86400, // 24 hours
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => $httpOnly,
        'samesite' => $sameSite
    ]);

    // Set session name
    session_name($sessionName);

    // Start session
    session_start();

    // Regenerate session ID to prevent session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }

    // Check for session timeout
    checkSessionTimeout();
}

/**
 * Check if session has timed out
 */
function checkSessionTimeout() {
    $timeout = 86400; // 24 hours

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        // Session has expired
        destroySession();
        return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Login user and create secure session
 * @param array $user User data from database
 */
function loginUser($user) {
    // Clear old session data
    $_SESSION = [];

    // Regenerate session ID for security
    session_regenerate_id(true);

    // Store user information in session
    $_SESSION['user_id'] = $user['ID'];
    $_SESSION['user_name'] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
    $_SESSION['username'] = $user['USERNAME'];
    $_SESSION['role'] = $user['ROLE_NAME']; // Role name from join query
    $_SESSION['role_id'] = $user['ROLE_ID'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    // Log successful login
    logAuth('login', $user['ID'], $user['USERNAME']);
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Get current user ID
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user role
 * @return string|null Role name or null if not logged in
 */
function getCurrentUserRole() {
    return isLoggedIn() ? $_SESSION['role'] : null;
}

/**
 * Check if current user has specific role
 * @param string $role Role to check
 * @return bool True if user has the role
 */
function hasRole($role) {
    return isLoggedIn() && strtoupper($_SESSION['role']) === strtoupper($role);
}

/**
 * Check if current user is an admin
 * @return bool True if user is admin
 */
function isAdmin() {
    return hasRole('ADMIN');
}

/**
 * Check if current user is a parent
 * @return bool True if user is parent
 */
function isParent() {
    return hasRole('PARENT');
}

/**
 * Check if current user is a child
 * @return bool True if user is child
 */
function isChild() {
    return hasRole('CHILD');
}

/**
 * Check if current user is educator
 * @return bool True if user is educator
 */
function isEducator() {
    return hasRole('EDU');
}

/**
 * Require user to be logged in, otherwise redirect to login
 * @param string $redirectTo Page to redirect to after login
 */
function requireLogin($redirectTo = '') {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $redirectTo;
        header('Location: index.php?auth=login');
        exit;
    }
}

/**
 * Require specific role, otherwise redirect with error
 * @param string|array $requiredRoles Role(s) required
 * @param string $errorMessage Error message to display
 */
function requireRole($requiredRoles, $errorMessage = 'Access denied') {
    if (!isLoggedIn()) {
        requireLogin();
        return;
    }

    $roles = is_array($requiredRoles) ? $requiredRoles : [$requiredRoles];
    $hasRequiredRole = false;

    foreach ($roles as $role) {
        if (hasRole($role)) {
            $hasRequiredRole = true;
            break;
        }
    }

    if (!$hasRequiredRole) {
        $_SESSION['error'] = $errorMessage;
        header('Location: index.php?page=404');
        exit;
    }
}

/**
 * Destroy session and logout user
 */
function destroySession() {
    // Unset all session variables
    $_SESSION = [];

    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy session
    session_destroy();

    // Clear session ID cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

/**
 * Logout user
 */
function logoutUser() {
    if (isLoggedIn()) {
        logAuth('logout', $_SESSION['user_id'], $_SESSION['username']);
    }
    destroySession();
}

/**
 * Log authentication events
 * @param string $action Action type (login, logout, failed_login)
 * @param int $userId User ID
 * @param string $username Username
 * @param string $details Additional details
 */
function logAuth($action, $userId, $username, $details = '') {
    $logFile = __DIR__ . '/../../logs/auth.log';
    $logDir = dirname($logFile);

    // Create logs directory if it doesn't exist
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    $logEntry = sprintf(
        "[%s] %s | User ID: %s | Username: %s | IP: %s | Details: %s | UA: %s\n",
        $timestamp,
        strtoupper($action),
        $userId,
        $username,
        $ip,
        $details,
        $userAgent
    );

    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Check for session hijacking attempts
 * @return bool True if session appears to be hijacked
 */
function checkSessionHijack() {
    if (!isLoggedIn()) {
        return false;
    }

    // Check IP address
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        return true;
    }

    // Check User Agent (less reliable but still useful)
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        return true;
    }

    return false;
}

/**
 * Get session information for debugging
 * @return array Session information
 */
function getSessionInfo() {
    return [
        'session_id' => session_id(),
        'logged_in' => isLoggedIn(),
        'user_id' => getCurrentUserId(),
        'role' => getCurrentUserRole(),
        'last_activity' => $_SESSION['last_activity'] ?? null,
        'ip_address' => $_SESSION['ip_address'] ?? null,
        'initiated' => $_SESSION['initiated'] ?? false
    ];
}
?>