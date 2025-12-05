<?php

/**
 * Quiz API - Delete Quiz Endpoint
 * Deletes a quiz and all associated questions/options
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';

// Initialize session
initSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('You must be logged in to delete a quiz');
    }

    $pdo = getDatabaseConnection();

    // Get raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $quizId = isset($data['id']) ? (int)$data['id'] : null;
    $currentUserId = getCurrentUserId();

    if (!$quizId) {
        throw new Exception('Quiz ID is required');
    }

    // Check if quiz exists and get owner
    $checkSql = "SELECT USER_ID FROM quizzes WHERE ID = :id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':id' => $quizId]);
    $quiz = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        throw new Exception('Quiz not found');
    }

    // Role-based access control: Admin can delete any, EDU can only delete their own
    if (!isAdmin() && $quiz['USER_ID'] != $currentUserId) {
        throw new Exception('You do not have permission to delete this quiz');
    }

    // Delete quiz (CASCADE will handle questions and options in the database)
    $deleteSql = "DELETE FROM quizzes WHERE ID = :id";
    $deleteStmt = $pdo->prepare($deleteSql);
    $deleteStmt->execute([':id' => $quizId]);

    if ($deleteStmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Quiz deleted successfully']);
    } else {
        throw new Exception('Failed to delete quiz');
    }
} catch (Exception $e) {
    error_log("Quiz Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
