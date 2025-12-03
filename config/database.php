<?php

/**
 * BookNest Database Configuration
 * Centralized database connection using PDO with security settings
 */

// Load environment variables
function loadEnv()
{
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                // Remove quotes if present
                $value = trim($value, '"\'');
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load environment variables
loadEnv();

/**
 * Get database connection
 * @return PDO Database connection
 */
function getDatabaseConnection()
{
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'booknest';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Important for security
        PDO::ATTR_PERSISTENT         => false,  // Connection pooling
    ];

    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        // Log error but don't expose details
        error_log("Database connection failed: " . $e->getMessage());
        throw new RuntimeException("Database connection failed. Please try again later.");
    }
}

/**
 * Check if database tables exist
 * @return bool True if tables exist
 */
function checkDatabaseTables()
{
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
