<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        throw new Exception('Valid user ID is required');
    }

    $stmt = $pdo->prepare("SELECT ID, FIRST_NAME, LAST_NAME, USERNAME, PHONE, ROLE_ID, IS_SUBSCRIBED, CREATED_DATE 
                           FROM users WHERE ID = :id");
    $stmt->execute([':id' => (int)$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch (Exception $e) {
    error_log("Get User Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
