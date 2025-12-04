<?php
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';

// Initialize session if not already started 
if (session_status() === PHP_SESSION_NONE) {
    initSession();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    $userId = $_SESSION['user_id'];
    $oldPassword = $data['old_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';

    // Validate required fields
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('All password fields are required');
    }

    // Validate new password matches confirmation
    if ($newPassword !== $confirmPassword) {
        throw new Exception('New password and confirmation do not match');
    }

    // Validate password strength (minimum 6 characters)
    if (strlen($newPassword) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // Get current password from database
    $stmt = $pdo->prepare("SELECT PASSWORD FROM users WHERE ID = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Verify old password
    if (!password_verify($oldPassword, $user['PASSWORD'])) {
        throw new Exception('Current password is incorrect');
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $updateStmt = $pdo->prepare("UPDATE users SET PASSWORD = :password WHERE ID = :id");
    $result = $updateStmt->execute([
        ':password' => $hashedPassword,
        ':id' => $userId
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update password');
    }
} catch (Exception $e) {
    error_log("Password Update Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
