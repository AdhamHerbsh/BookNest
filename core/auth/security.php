<?php

declare(strict_types=1);

/**
 * BookNest Security Utilities
 * Handles password hashing, verification, and input sanitization
 */

/**
 * Hash password using bcrypt
 *
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 *
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool True if password matches
 */
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 *
 * @return string CSRF token
 */
function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 *
 * @param string $token Token to verify
 * @return bool True if token is valid
 */
function verifyCsrfToken(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }


    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 *
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 *
 * @param string $email Email to validate
 * @return bool True if email is valid
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 *
 * @param string $password Password to validate
 * @return array{valid: bool, message: string} Validation result
 */
function validatePasswordStrength(string $password): array
{
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
 *
 * @param string $name Name to validate
 * @return array{valid: bool, message: string} Validation result
 */
function validateName(string $name): array
{
    if (empty($name)) {
        return ['valid' => false, 'message' => 'Name is required'];
    }

    if (strlen($name) < 2 || strlen($name) > 50) {
        return ['valid' => false, 'message' => 'Name must be between 2 and 50 characters'];
    }

    if (!preg_match("/^[A-Za-z\s\-\'\.]+$/", $name)) {
        return ['valid' => false, 'message' => 'Name can only contain letters, spaces, hyphens, apostrophes, and periods'];
    }

    return ['valid' => true, 'message' => 'Name is valid'];
}

/**
 * Validate phone number (optional field)
 *
 * @param string $phone Phone number to validate
 * @return array{valid: bool, message: string} Validation result
 */
function validatePhone(string $phone): array
{
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
 *
 * @param string $code Child code to validate
 * @return array{valid: bool, message: string} Validation result
 */

function validateChildCode(string $code): array
{
    if (empty($code)) {
        return ['valid' => false, 'message' => 'Child code is required'];
    }

    // Format: CHILD-{ParentID}-{rand 6 digits}
    if (!preg_match('/^CHILD-\d+-\d{6}$/', $code)) {
        return ['valid' => false, 'message' => 'Child code must be in the format CHILD-{ParentID}-{6-digit-number}'];
    }

    return ['valid' => true, 'message' => 'Child code is valid'];
}

/**
 * Validate child passkey
 *
 * @param string $passkey Passkey to validate
 * @return array{valid: bool, message: string} Validation result
 */
function validateChildPasskey(string $passkey): array
{
    if (empty($passkey)) {
        return ['valid' => false, 'message' => 'Child passkey is required'];
    }

    if (strlen($passkey) < 4 || strlen($passkey) > 8) {
        return ['valid' => false, 'message' => 'Child passkey must be 4-8 characters'];
    }

    if (!preg_match('/^[0-9]+$/', $passkey)) {
        return ['valid' => false, 'message' => 'Child passkey can only contain numbers'];
    }

    return ['valid' => true, 'message' => 'Child passkey is valid'];
}
