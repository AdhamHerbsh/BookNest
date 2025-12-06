<?php
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';

if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

header('Content-Type: application/json');
$session = getSessionInfo();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

if (!isset($session['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$childId = $_GET['id'] ?? null;

if (!$childId) {
    echo json_encode(['success' => false, 'message' => 'Child ID is required']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $userId = $session['user_id'];
    $role = $session['role'];

    // Admin can view any child, parent can view only their children
    if ($role === 'ADMIN') {
        $stmt = $pdo->prepare("SELECT * FROM children WHERE ID = ?");
        $stmt->execute([$childId]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM children WHERE ID = ? AND USER_ID = ?");
        $stmt->execute([$childId, $userId]);
    }

    $child = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$child) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Child not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'child' => $child
    ]);
} catch (Exception $e) {
    error_log("Get Child Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to fetch child']);
}
