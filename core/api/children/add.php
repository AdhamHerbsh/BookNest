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

if (!$data) {
    // Handle form-data if JSON is not sent
    $data = $_POST;
}

$name = $data['name'] ?? '';
$dob = $data['dob'] ?? '';

if (empty($name) || empty($dob)) {
    echo json_encode(['success' => false, 'message' => 'Name and Date of Birth are required']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $pdo->beginTransaction();

    // 1. Calculate Age
    $dobDate = new DateTime($dob);
    $now = new DateTime();
    $age = $now->diff($dobDate)->y;

    // 2. Generate Child Code
    // Format: CHILD-{ParentID}-{rand 6 digits}
    $code = sprintf("CHILD-%d-%d", $userId, rand(100000, 999999));

    // 3. Insert Child
    // Get Child Role ID
    $stmt = $pdo->prepare("SELECT ID FROM roles WHERE NAME = 'CHILD'");
    $stmt->execute();
    $roleRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $childRoleId = $roleRow ? $roleRow['ID'] : 4; // Default to 4 if not found, but should exist

    $stmt = $pdo->prepare("INSERT INTO children (CODE, NAME, DOB, AGE, USER_ID, ROLE_ID, CREADTED_DATE) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$code, $name, $dob, $age, $userId, $childRoleId]);
    $childId = $pdo->lastInsertId();

    // 4. Check/Generate Parent Passkey
    $stmt = $pdo->prepare("SELECT PASSKEY FROM users WHERE ID = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $passkey = $user['PASSKEY'];
    if (empty($passkey)) {
        // Generate a 6-digit passkey
        $passkey = rand(100000, 999999);
        $updateStmt = $pdo->prepare("UPDATE users SET PASSKEY = ? WHERE ID = ?");
        $updateStmt->execute([$passkey, $userId]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Child added successfully',
        'child' => [
            'id' => $childId,
            'name' => $name,
            'code' => $code,
            'age' => $age,
            'dob' => $dob
        ],
        'parent_passkey' => $passkey
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Add Child Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to add child: ' . $e->getMessage()]);
}
