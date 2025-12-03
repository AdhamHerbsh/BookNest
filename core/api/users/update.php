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
    $firstName = $data['first_name'] ?? '';
    $lastName = $data['last_name'] ?? '';
    $username = $data['username'] ?? '';
    $phone = $data['phone'] ?? '';
    $roleId = $data['role_id'] ?? null;
    $isSubscribed = isset($data['is_subscribed']) && $data['is_subscribed'] === 'Y' ? 'Y' : 'N';

    if (!$id || !is_numeric($id)) {
        throw new Exception('Valid user ID is required');
    }

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($username)) {
        throw new Exception('First name, last name, and username are required');
    }

    // Check if user exists
    $checkStmt = $pdo->prepare("SELECT ID FROM users WHERE ID = :id");
    $checkStmt->execute([':id' => (int)$id]);
    if (!$checkStmt->fetch()) {
        throw new Exception('User not found');
    }

    // Update database
    $sql = "UPDATE users SET 
            FIRST_NAME = :firstName, 
            LAST_NAME = :lastName, 
            USERNAME = :username, 
            PHONE = :phone, 
            ROLE_ID = :roleId, 
            IS_SUBSCRIBED = :isSubscribed
            WHERE ID = :id";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':username' => $username,
        ':phone' => $phone,
        ':roleId' => $roleId ?: null,
        ':isSubscribed' => $isSubscribed,
        ':id' => (int)$id
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        throw new Exception('Failed to update user');
    }
} catch (Exception $e) {
    error_log("User Update Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
