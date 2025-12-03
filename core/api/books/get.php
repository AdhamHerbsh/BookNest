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

    if (!$id) {
        throw new Exception('Book ID is required');
    }

    $stmt = $pdo->prepare("SELECT * FROM books WHERE ID = :id");
    $stmt->execute([':id' => $id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        echo json_encode(['success' => true, 'book' => $book]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
    }
} catch (Exception $e) {
    error_log("Get Book Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
