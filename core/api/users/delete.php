<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db/config.php';

// TODO: Add CSRF token validation and admin authentication check

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        throw new Exception('Valid user ID is required');
    }

    // Check if user exists before deleting
    $checkStmt = $pdo->prepare("SELECT ID, USERNAME FROM users WHERE ID = :id");
    $checkStmt->execute([':id' => (int)$id]);
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM users WHERE ID = :id");
    $result = $stmt->execute([':id' => (int)$id]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully',
            'deleted_user' => $user['USERNAME']
        ]);
    } else {
        throw new Exception('Failed to delete user');
    }
} catch (Exception $e) {
    error_log("User Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
