<?php
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';
// Initialize session if not already started 
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

header('Content-Type: application/json');

$session = getSessionInfo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

if (!isset($session['user_id']) || $session['role'] !== 'PARENT') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $session['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$childId = $data['id'] ?? null;

if (!$childId) {
    echo json_encode(['success' => false, 'message' => 'Child ID is required']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Verify child belongs to parent
    $stmt = $pdo->prepare("SELECT ID FROM children WHERE ID = ? AND USER_ID = ?");
    $stmt->execute([$childId, $userId]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access to this child']);
        exit;
    }

    // Delete child (Hard delete for now as per schema, or we could implement soft delete if schema had IS_ACTIVE)
    // The prompt mentioned "Soft delete recommended", but schema doesn't have IS_DELETED. 
    // I will use DELETE for now, but constraints might block it if scores exist.
    // Let's check constraints.
    // scores -> child (ON DELETE CASCADE)
    // So hard delete is safe and will clean up scores.

    $stmt = $pdo->prepare("DELETE FROM children WHERE ID = ?");
    $stmt->execute([$childId]);

    echo json_encode(['success' => true, 'message' => 'Child deleted successfully']);
} catch (Exception $e) {
    error_log("Delete Child Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to delete child: ' . $e->getMessage()]);
}
