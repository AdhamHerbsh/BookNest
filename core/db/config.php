<?php
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


// Check if username already exists
$pdo = getDatabaseConnection();
$query = $pdo->prepare('SELECT * FROM users');

$users = $query->fetchAll();

foreach ($users as $user) {
    echo $user['USERNAME'];
}
echo "Done";
