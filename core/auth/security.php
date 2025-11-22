<?php
/**
 * BookNest Security Utilities
 * Handles password hashing, verification, and input sanitization
 */

/**
 * Hash password using bcrypt
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool True if password matches
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if token is valid
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Sanitize input data
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 * @param string $email Email to validate
 * @return bool True if email is valid
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password Password to validate
 * @return array Validation result with 'valid' boolean and 'message' string
 */
function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return ['valid' => false, 'message' => 'Password must be at least 8 characters long'];
    }

    if (!preg_match('/[A-Za-z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one letter'];
    }

    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one number'];
    }

    return ['valid' => true, 'message' => 'Password is valid'];
}

/**
 * Validate name fields
 * @param string $name Name to validate
 * @return array Validation result
 */
function validateName($name) {
    if (empty($name)) {
        return ['valid' => false, 'message' => 'Name is required'];
    }

    if (strlen($name) < 2 || strlen($name) > 50) {
        return ['valid' => false, 'message' => 'Name must be between 2 and 50 characters'];
    }

    if (!preg_match('/^[A-Za-z\s\-\'\.]+$/', $name)) {
        return ['valid' => false, 'message' => 'Name can only contain letters, spaces, hyphens, apostrophes, and periods'];
    }

    return ['valid' => true, 'message' => 'Name is valid'];
}

/**
 * Validate phone number (optional field)
 * @param string $phone Phone number to validate
 * @return array Validation result
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return ['valid' => true, 'message' => 'Phone number is optional'];
    }

    // Remove all non-numeric characters
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

    if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
        return ['valid' => false, 'message' => 'Phone number must be between 10 and 15 digits'];
    }

    return ['valid' => true, 'message' => 'Phone number is valid'];
}

/**
 * Validate child code (numeric)
 * @param string $code Child code to validate
 * @return array Validation result
 */
function validateChildCode($code) {
    if (empty($code)) {
        return ['valid' => false, 'message' => 'Child code is required'];
    }

    if (!preg_match('/^[0-9]{4,6}$/', $code)) {
        return ['valid' => false, 'message' => 'Child code must be 4-6 digits'];
    }

    return ['valid' => true, 'message' => 'Child code is valid'];
}

/**
 * Validate child passkey
 * @param string $passkey Passkey to validate
 * @return array Validation result
 */
function validateChildPasskey($passkey) {
    if (empty($passkey)) {
        return ['valid' => false, 'message' => 'Child passkey is required'];
    }

    if (strlen($passkey) < 4 || strlen($passkey) > 8) {
        return ['valid' => false, 'message' => 'Child passkey must be 4-8 characters'];
    }

    if (!preg_match('/^[A-Za-z0-9]+$/', $passkey)) {
        return ['valid' => false, 'message' => 'Child passkey can only contain letters and numbers'];
    }

    return ['valid' => true, 'message' => 'Child passkey is valid'];
}
?>