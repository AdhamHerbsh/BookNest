<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Get raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        throw new Exception('Book ID is required');
    }

    // Get file paths to delete files
    $stmt = $pdo->prepare("SELECT COVER, FILE_PATH FROM books WHERE ID = :id");
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // Delete files if they exist
        if ($book['COVER'] && file_exists(__DIR__ . '/../../../' . $book['COVER'])) {
            unlink(__DIR__ . '/../../../' . $book['COVER']);
        }
        if ($book['FILE_PATH'] && file_exists(__DIR__ . '/../../../' . $book['FILE_PATH'])) {
            unlink(__DIR__ . '/../../../' . $book['FILE_PATH']);
        }

        // Delete from database
        $deleteStmt = $pdo->prepare("DELETE FROM books WHERE ID = :id");
        $deleteStmt->execute([':id' => $id]);

        echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
    } else {
        throw new Exception('Book not found');
    }
} catch (Exception $e) {
    error_log("Book Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
