<?php

/**
 * Quiz API - Add Quiz Endpoint
 * Creates a new quiz with questions and options
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
    // Check if user is logged in and has permission (EDU or ADMIN)
    if (!isLoggedIn()) {
        throw new Exception('You must be logged in to create a quiz');
    }

    if (!isEducator() && !isAdmin()) {
        throw new Exception('You do not have permission to create quizzes');
    }

    $pdo = getDatabaseConnection();

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Get form data with isset checks
    $title = isset($data['title']) ? trim($data['title']) : '';
    $description = isset($data['description']) ? trim($data['description']) : '';
    $bookId = isset($data['book_id']) ? (int)$data['book_id'] : null;
    $questions = isset($data['questions']) ? $data['questions'] : [];
    $userId = getCurrentUserId();

    // Validate required fields
    if (empty($title)) {
        throw new Exception('Quiz title is required');
    }

    // Validate questions
    if (empty($questions) || !is_array($questions)) {
        throw new Exception('At least one question is required');
    }

    // Validate each question has required fields
    foreach ($questions as $index => $question) {
        $questionNum = $index + 1;

        if (!isset($question['question_text']) || empty(trim($question['question_text']))) {
            throw new Exception("Question {$questionNum} text is required");
        }

        if (!isset($question['options']) || !is_array($question['options']) || count($question['options']) < 2) {
            throw new Exception("Question {$questionNum} must have at least 2 options");
        }

        // Check if correct answer is set
        $hasCorrectAnswer = false;
        foreach ($question['options'] as $option) {
            if (isset($option['is_correct']) && $option['is_correct'] === true) {
                $hasCorrectAnswer = true;
                break;
            }
        }

        if (!$hasCorrectAnswer) {
            throw new Exception("Question {$questionNum} must have a correct answer selected");
        }
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Insert quiz
        $quizSql = "INSERT INTO quizzes (TITLE, DESCRIPTION, USER_ID, BOOK_ID, CREATED_DATE) 
                    VALUES (:title, :description, :userId, :bookId, NOW())";
        $quizStmt = $pdo->prepare($quizSql);
        $quizStmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':userId' => $userId,
            ':bookId' => $bookId ?: null
        ]);

        $quizId = $pdo->lastInsertId();

        // Insert questions and options
        foreach ($questions as $questionIndex => $question) {
            $questionText = trim($question['question_text']);
            $questionType = isset($question['type']) ? $question['type'] : 'multiple_choice';

            // Insert question
            $questionSql = "INSERT INTO questions (QUESTION, TYPE, QUIZ_ID, CREATED_DATE) 
                           VALUES (:question, :type, :quizId, NOW())";
            $questionStmt = $pdo->prepare($questionSql);
            $questionStmt->execute([
                ':question' => $questionText,
                ':type' => $questionType,
                ':quizId' => $quizId
            ]);

            $questionId = $pdo->lastInsertId();

            // Insert options
            foreach ($question['options'] as $option) {
                if (!isset($option['text']) || empty(trim($option['text']))) {
                    continue; // Skip empty options
                }

                $isCorrect = isset($option['is_correct']) && $option['is_correct'] === true ? 'Y' : 'N';

                $optionSql = "INSERT INTO options (`OPTION`, IS_CORRECT, QUESTION_ID) 
                             VALUES (:option, :isCorrect, :questionId)";
                $optionStmt = $pdo->prepare($optionSql);
                $optionStmt->execute([
                    ':option' => trim($option['text']),
                    ':isCorrect' => $isCorrect,
                    ':questionId' => $questionId
                ]);
            }
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Quiz created successfully',
            'quiz_id' => $quizId
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    error_log("Quiz Add Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
