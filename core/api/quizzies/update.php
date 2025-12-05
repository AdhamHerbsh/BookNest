<?php

/**
 * Quiz API - Update Quiz Endpoint
 * Updates an existing quiz with questions and options
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
        throw new Exception('You must be logged in to update a quiz');
    }

    $pdo = getDatabaseConnection();

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    $quizId = isset($data['id']) ? (int)$data['id'] : null;
    $title = isset($data['title']) ? trim($data['title']) : '';
    $description = isset($data['description']) ? trim($data['description']) : '';
    $bookId = isset($data['book_id']) ? (int)$data['book_id'] : null;
    $questions = isset($data['questions']) ? $data['questions'] : [];
    $currentUserId = getCurrentUserId();

    // Validate quiz ID
    if (!$quizId) {
        throw new Exception('Quiz ID is required');
    }

    // Check if quiz exists and user has permission
    $checkSql = "SELECT USER_ID FROM quizzes WHERE ID = :id";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':id' => $quizId]);
    $quiz = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        throw new Exception('Quiz not found');
    }

    // Role-based access control: Admin can edit any, EDU can only edit their own
    if (!isAdmin() && $quiz['USER_ID'] != $currentUserId) {
        throw new Exception('You do not have permission to edit this quiz');
    }

    // Validate required fields
    if (empty($title)) {
        throw new Exception('Quiz title is required');
    }

    // Validate questions
    if (empty($questions) || !is_array($questions)) {
        throw new Exception('At least one question is required');
    }

    // Validate each question
    foreach ($questions as $index => $question) {
        $questionNum = $index + 1;

        if (!isset($question['question_text']) || empty(trim($question['question_text']))) {
            throw new Exception("Question {$questionNum} text is required");
        }

        if (!isset($question['options']) || !is_array($question['options']) || count($question['options']) < 2) {
            throw new Exception("Question {$questionNum} must have at least 2 options");
        }

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
        // Update quiz
        $updateQuizSql = "UPDATE quizzes SET TITLE = :title, DESCRIPTION = :description, BOOK_ID = :bookId WHERE ID = :id";
        $updateQuizStmt = $pdo->prepare($updateQuizSql);
        $updateQuizStmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':bookId' => $bookId ?: null,
            ':id' => $quizId
        ]);

        // Delete existing questions and options (cascade will handle options)
        $deleteQuestionsSql = "DELETE FROM questions WHERE QUIZ_ID = :quizId";
        $deleteQuestionsStmt = $pdo->prepare($deleteQuestionsSql);
        $deleteQuestionsStmt->execute([':quizId' => $quizId]);

        // Re-insert questions and options
        foreach ($questions as $question) {
            $questionText = trim($question['question_text']);
            $questionType = isset($question['type']) ? $question['type'] : 'multiple_choice';

            $questionSql = "INSERT INTO questions (QUESTION, TYPE, QUIZ_ID, CREATED_DATE) 
                           VALUES (:question, :type, :quizId, NOW())";
            $questionStmt = $pdo->prepare($questionSql);
            $questionStmt->execute([
                ':question' => $questionText,
                ':type' => $questionType,
                ':quizId' => $quizId
            ]);

            $questionId = $pdo->lastInsertId();

            foreach ($question['options'] as $option) {
                if (!isset($option['text']) || empty(trim($option['text']))) {
                    continue;
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

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Quiz updated successfully'
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    error_log("Quiz Update Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
