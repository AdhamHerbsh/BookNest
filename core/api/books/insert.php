<?php
// Set JSON header for proper API response
header('Content-Type: application/json');

// Include database connection (from core/api/books/ go up 3 levels to root, then to config)
require_once __DIR__ . '/../../db/config.php'; // Fixed: From core/auth/ go up to core/, then to db/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Get form data
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $language = $_POST['language'] ?? '';
    $isActive = isset($_POST['isActive']) ? 'Y' : 'N';
    $description = $_POST['description'] ?? '';
    $ageGroup = $_POST['age_group'] ?? '';

    // Validate required fields
    if (empty($title) || empty($author) || empty($language) || empty($description) || empty($ageGroup)) {
        throw new Exception('All fields are required');
    }

    // Handle file uploads
    $uploadDirBooks = '../../../assets/books/files/';
    $uploadDirCovers = '../../../assets/books/images/';

    // Create directories if they don't exist
    if (!file_exists($uploadDirBooks)) mkdir($uploadDirBooks, 0777, true);
    if (!file_exists($uploadDirCovers)) mkdir($uploadDirCovers, 0777, true);

    // Process Cover Image
    $dbCoverPath = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverExtension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $coverFilename = uniqid('cover_') . '.' . $coverExtension;
        $coverPath = $uploadDirCovers . $coverFilename;

        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $coverPath)) {
            throw new Exception('Failed to upload cover image');
        }
        $dbCoverPath = 'assets/books/images/' . $coverFilename;
    }

    // Process Book File
    $dbBookPath = null;
    if (isset($_FILES['book_file']) && $_FILES['book_file']['error'] === UPLOAD_ERR_OK) {
        $bookExtension = pathinfo($_FILES['book_file']['name'], PATHINFO_EXTENSION);
        $bookFilename = uniqid('book_') . '.' . $bookExtension;
        $bookPath = $uploadDirBooks . $bookFilename;

        if (!move_uploaded_file($_FILES['book_file']['tmp_name'], $bookPath)) {
            throw new Exception('Failed to upload book file');
        }
        $dbBookPath = 'assets/books/files/' . $bookFilename;
    }

    // Insert into database
    $sql = "INSERT INTO books (TITLE, AUTHOR, LANGUAGE, IS_ACTIVE, DESCRIPTION, AGE_GROUP, COVER, FILE_PATH) 
            VALUES (:title, :author, :language, :isActive, :description, :ageGroup, :cover, :filePath)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':author' => $author,
        ':language' => $language,
        ':isActive' => $isActive,
        ':description' => $description,
        ':ageGroup' => $ageGroup,
        ':cover' => $dbCoverPath,
        ':filePath' => $dbBookPath
    ]);

    if ($stmt) {
        echo json_encode(['success' => true, 'message' => 'Book uploaded successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload book']);
    }
} catch (Exception $e) {
    // Log error for debugging
    error_log("Book Upload Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());

    // Return JSON error response
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
