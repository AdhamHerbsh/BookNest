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

if (!isset($session['user_id']) || !in_array($session['role'], ['PARENT', 'ADMIN'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $session['user_id'];
$role = $session['role'];
$data = json_decode(file_get_contents('php://input'), true);

$childId = $data['id'] ?? null;
$name = $data['name'] ?? '';
$dob = $data['dob'] ?? '';

if (!$childId || empty($name) || empty($dob)) {
    echo json_encode(['success' => false, 'message' => 'ID, Name and Date of Birth are required']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Verify child exists and parent owns it (unless admin)
    if ($role === 'ADMIN') {
        $stmt = $pdo->prepare("SELECT ID FROM children WHERE ID = ?");
        $stmt->execute([$childId]);
    } else {
        $stmt = $pdo->prepare("SELECT ID FROM children WHERE ID = ? AND USER_ID = ?");
        $stmt->execute([$childId, $userId]);
    }
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Child not found or unauthorized']);
        exit;
    }

    // Calculate Age
    $dobDate = new DateTime($dob);
    $now = new DateTime();
    $age = $now->diff($dobDate)->y;

    // Update Child
    $stmt = $pdo->prepare("UPDATE children SET NAME = ?, DOB = ?, AGE = ? WHERE ID = ?");
    $stmt->execute([$name, $dob, $age, $childId]);

    echo json_encode(['success' => true, 'message' => 'Child updated successfully']);
} catch (Exception $e) {
    error_log("Update Child Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update child: ' . $e->getMessage()]);
}
