<?php
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';


// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

$session = getSessionInfo();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

try {
    $pdo = getDatabaseConnection();

    // Fetch children
    $stmt = $pdo->prepare("SELECT ID, NAME, CODE, DOB, AGE, AVATER FROM children WHERE USER_ID = ? ORDER BY CREADTED_DATE DESC");
    $stmt->execute([$userId]);
    $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch parent passkey
    $stmtUser = $pdo->prepare("SELECT PASSKEY FROM users WHERE ID = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'children' => $children,
        'parent_passkey' => $user['PASSKEY']
    ]);
} catch (Exception $e) {
    error_log("List Children Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to fetch children']);
}
