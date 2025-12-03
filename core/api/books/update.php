<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'] ?? null;
    $title = $data['title'] ?? '';
    $author = $data['author'] ?? '';
    $language = $data['language'] ?? '';
    $isActive = isset($data['isActive']) && $data['isActive'] == 'Y' ? 'Y' : 'N';
    $description = $data['description'] ?? '';
    $ageGroup = $data['age_group'] ?? '';

    if (!$id) {
        throw new Exception('Book ID is required');
    }

    // Validate required fields
    if (empty($title) || empty($author) || empty($language) || empty($description) || empty($ageGroup)) {
        throw new Exception('All fields are required');
    }

    // Update database
    $sql = "UPDATE books SET 
            TITLE = :title, 
            AUTHOR = :author, 
            LANGUAGE = :language, 
            IS_ACTIVE = :isActive, 
            DESCRIPTION = :description, 
            AGE_GROUP = :ageGroup
            WHERE ID = :id";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':title' => $title,
        ':author' => $author,
        ':language' => $language,
        ':isActive' => $isActive,
        ':description' => $description,
        ':ageGroup' => $ageGroup,
        ':id' => $id
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Book updated successfully']);
    } else {
        throw new Exception('Failed to update book');
    }
} catch (Exception $e) {
    error_log("Book Update Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
