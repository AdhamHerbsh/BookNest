<?php
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDatabaseConnection();

    // Delete all favorites for this user
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE USER_ID = ?");
    $stmt->execute([$userId]);
    $deletedCount = $stmt->rowCount();

    echo json_encode([
        'success' => true,
        'message' => "Deleted $deletedCount favorite(s)",
        'deleted_count' => $deletedCount
    ]);
} catch (PDOException $e) {
    error_log("Clear favorites error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
