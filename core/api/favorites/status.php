<?php
// /core/api/favorites/status.php

require_once '../../db/config.php';
require_once '../../auth/session.php';

header('Content-Type: application/json');

// Initialize session if not already started 
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

try {
    $pdo = getDatabaseConnection();

    // Get all favorite book IDs for this user
    $stmt = $pdo->prepare("SELECT BOOK_ID FROM favorites WHERE USER_ID = ?");
    $stmt->execute([$userId]);
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success' => true,
        'favorites' => array_map('intval', $favorites)
    ]);
} catch (PDOException $e) {
    error_log("Favorites status error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
