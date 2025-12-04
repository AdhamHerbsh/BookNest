<?php
// /core/api/favorites/toggle.php

require_once '../../db/config.php';
require_once '../../auth/session.php';

header('Content-Type: application/json');

// Initialize session if not already started 
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated. Please log in.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$bookId = isset($input['book_id']) ? (int)$input['book_id'] : 0;
$action = isset($input['action']) ? $input['action'] : '';

// Validate inputs
if (!$bookId || !in_array($action, ['like', 'unlike'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid parameters. Check book_id and action.'
    ]);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $pdo->beginTransaction();

    // Check if favorite exists
    $stmt = $pdo->prepare("SELECT ID FROM favorites WHERE USER_ID = ? AND BOOK_ID = ?");
    $stmt->execute([$userId, $bookId]);
    $existing = $stmt->fetch();

    if ($action === 'like' && !$existing) {
        // Verify book exists
        $stmt = $pdo->prepare("SELECT ID FROM books WHERE ID = ? AND IS_ACTIVE = 'Y'");
        $stmt->execute([$bookId]);
        if (!$stmt->fetch()) {
            throw new Exception('Book not found or inactive');
        }

        // Add favorite
        $stmt = $pdo->prepare("INSERT INTO favorites (USER_ID, BOOK_ID, CREATED_DATE) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $bookId]);
        $status = 'added';
    } elseif ($action === 'unlike' && $existing) {
        // Remove favorite
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE USER_ID = ? AND BOOK_ID = ?");
        $stmt->execute([$userId, $bookId]);
        $status = 'removed';
    } else {
        // State already matches action
        $status = $existing ? 'already_added' : 'already_removed';
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'status' => $status,
        'is_favorited' => ($status === 'added' || $status === 'already_added')
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Favorites toggle PDO error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred.'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Favorites toggle error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
