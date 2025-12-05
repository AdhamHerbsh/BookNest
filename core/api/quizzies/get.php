<?php

/**
 * Quiz API - Get Quiz Endpoint
 * Retrieves quiz data with questions and options
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../db/config.php';
require_once __DIR__ . '/../../auth/session.php';

// Initialize session
initSession();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : null;
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
    $all = isset($_GET['all']) && $_GET['all'] === 'true';

    // Get quiz by book_id (for reading completion flow)
    if ($bookId) {
        $quizSql = "SELECT q.ID, q.TITLE, q.DESCRIPTION, q.CREATED_DATE, q.USER_ID, q.BOOK_ID,
                           u.FIRST_NAME, u.LAST_NAME, u.USERNAME,
                           b.TITLE as BOOK_TITLE
                    FROM quizzes q
                    LEFT JOIN users u ON q.USER_ID = u.ID
                    LEFT JOIN books b ON q.BOOK_ID = b.ID
                    WHERE q.BOOK_ID = :bookId
                    LIMIT 1";
        $quizStmt = $pdo->prepare($quizSql);
        $quizStmt->execute([':bookId' => $bookId]);
        $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

        if (!$quiz) {
            echo json_encode([
                'success' => false,
                'message' => 'No quiz available for this book yet',
                'quiz_available' => false
            ]);
            exit;
        }

        // Get questions for this quiz
        $questionsSql = "SELECT ID, QUESTION, TYPE FROM questions WHERE QUIZ_ID = :quizId ORDER BY ID";
        $questionsStmt = $pdo->prepare($questionsSql);
        $questionsStmt->execute([':quizId' => $quiz['ID']]);
        $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get options for each question (hide IS_CORRECT for security unless admin)
        foreach ($questions as &$question) {
            $optionsSql = "SELECT ID, `OPTION` FROM options WHERE QUESTION_ID = :questionId ORDER BY ID";
            $optionsStmt = $pdo->prepare($optionsSql);
            $optionsStmt->execute([':questionId' => $question['ID']]);
            $question['options'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $quiz['questions'] = $questions;
        $quiz['question_count'] = count($questions);

        echo json_encode([
            'success' => true,
            'quiz' => $quiz,
            'quiz_available' => true
        ]);
        exit;
    }

    // Get all quizzes
    if ($all) {
        $sql = "SELECT q.ID, q.TITLE, q.DESCRIPTION, q.CREATED_DATE, q.USER_ID, q.BOOK_ID,
                       u.FIRST_NAME, u.LAST_NAME, u.USERNAME,
                       b.TITLE as BOOK_TITLE,
                       (SELECT COUNT(*) FROM questions WHERE QUIZ_ID = q.ID) as QUESTION_COUNT
                FROM quizzes q
                LEFT JOIN users u ON q.USER_ID = u.ID
                LEFT JOIN books b ON q.BOOK_ID = b.ID";

        $params = [];

        // Filter by user_id if provided (for EDU users to see only their quizzes)
        if ($userId) {
            $sql .= " WHERE q.USER_ID = :userId";
            $params[':userId'] = $userId;
        }

        $sql .= " ORDER BY q.CREATED_DATE DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'quizzes' => $quizzes]);
        exit;
    }

    // Get single quiz with questions and options
    if (!$id) {
        throw new Exception('Quiz ID is required');
    }

    // Get quiz details
    $quizSql = "SELECT q.ID, q.TITLE, q.DESCRIPTION, q.CREATED_DATE, q.USER_ID, q.BOOK_ID,
                       u.FIRST_NAME, u.LAST_NAME, u.USERNAME,
                       b.TITLE as BOOK_TITLE
                FROM quizzes q
                LEFT JOIN users u ON q.USER_ID = u.ID
                LEFT JOIN books b ON q.BOOK_ID = b.ID
                WHERE q.ID = :id";
    $quizStmt = $pdo->prepare($quizSql);
    $quizStmt->execute([':id' => $id]);
    $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        throw new Exception('Quiz not found');
    }

    // Get questions
    $questionsSql = "SELECT ID, QUESTION, TYPE FROM questions WHERE QUIZ_ID = :quizId ORDER BY ID";
    $questionsStmt = $pdo->prepare($questionsSql);
    $questionsStmt->execute([':quizId' => $id]);
    $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get options for each question
    foreach ($questions as &$question) {
        $optionsSql = "SELECT ID, `OPTION`, IS_CORRECT FROM options WHERE QUESTION_ID = :questionId ORDER BY ID";
        $optionsStmt = $pdo->prepare($optionsSql);
        $optionsStmt->execute([':questionId' => $question['ID']]);
        $question['options'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $quiz['questions'] = $questions;

    echo json_encode(['success' => true, 'quiz' => $quiz]);
} catch (Exception $e) {
    error_log("Get Quiz Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
