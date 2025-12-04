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
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $phone = trim($data['phone'] ?? '');

    // Validate required fields
    if (empty($firstName) || empty($lastName)) {
        throw new Exception('First name and last name are required');
    }

    // Update database
    $sql = "UPDATE users SET 
            FIRST_NAME = :firstName, 
            LAST_NAME = :lastName, 
            PHONE = :phone
            WHERE ID = :id";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':phone' => $phone,
        ':id' => $userId
    ]);

    if ($result) {
        // Update session variables
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['phone'] = $phone;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;

        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone
            ]
        ]);
    } else {
        throw new Exception('Failed to update profile');
    }
} catch (Exception $e) {
    error_log("Profile Update Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
