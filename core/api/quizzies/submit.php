<?php

/**
 * Quiz API - Submit Quiz Score Endpoint
 * Handles quiz score submission and storage
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
        throw new Exception('You must be logged in to submit quiz');
    }

    $pdo = getDatabaseConnection();

    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    $quizId = isset($data['quiz_id']) ? (int)$data['quiz_id'] : null;
    $answers = isset($data['answers']) ? $data['answers'] : [];
    $childId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    // Validate quiz ID
    if (!$quizId) {
        throw new Exception('Quiz ID is required');
    }

    // Verify quiz exists
    $quizStmt = $pdo->prepare("SELECT ID, TITLE FROM quizzes WHERE ID = :id");
    $quizStmt->execute([':id' => $quizId]);
    $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        throw new Exception('Quiz not found');
    }

    // Get all questions with correct answers
    $questionsStmt = $pdo->prepare("
        SELECT q.ID as question_id, o.ID as option_id, o.IS_CORRECT
        FROM questions q
        JOIN options o ON q.ID = o.QUESTION_ID
        WHERE q.QUIZ_ID = :quiz_id AND o.IS_CORRECT = 'Y'
    ");
    $questionsStmt->execute([':quiz_id' => $quizId]);
    $correctAnswers = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a map of question_id => correct_option_id
    $correctAnswerMap = [];
    foreach ($correctAnswers as $answer) {
        $correctAnswerMap[$answer['question_id']] = $answer['option_id'];
    }

    $totalQuestions = count($correctAnswerMap);

    // Handle edge case: no questions
    if ($totalQuestions === 0) {
        throw new Exception('This quiz has no questions');
    }

    // Calculate score
    $correctCount = 0;

    if (!empty($answers) && is_array($answers)) {
        foreach ($answers as $questionId => $selectedOptionId) {
            if (
                isset($correctAnswerMap[$questionId]) &&
                $correctAnswerMap[$questionId] == $selectedOptionId
            ) {
                $correctCount++;
            }
        }
    }

    // Calculate percentage (initialize to 0 if no answers - handles empty submission)
    $scorePercentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

    // Check for existing score on same quiz today (optional double submission prevention)
    $today = date('Y-m-d');
    $existingStmt = $pdo->prepare("
        SELECT ID FROM scores 
        WHERE QUIZ_ID = :quiz_id 
        AND DATE(DATE_COMPLETED) = :today
        " . ($childId ? "AND CHILD_ID = :child_id" : "AND CHILD_ID IS NULL") . "
    ");

    $params = [
        ':quiz_id' => $quizId,
        ':today' => $today
    ];
    if ($childId) {
        $params[':child_id'] = $childId;
    }
    $existingStmt->execute($params);
    $existingScore = $existingStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingScore) {
        // Update existing score if higher
        $updateStmt = $pdo->prepare("
            UPDATE scores 
            SET SCORE_PERCENTAGE = GREATEST(SCORE_PERCENTAGE, :score),
                CORRECT_ANSWERS = IF(:score > SCORE_PERCENTAGE, :correct, CORRECT_ANSWERS),
                DATE_COMPLETED = NOW()
            WHERE ID = :id
        ");
        $updateStmt->execute([
            ':score' => $scorePercentage,
            ':correct' => $correctCount,
            ':id' => $existingScore['ID']
        ]);

        $message = 'Score updated! Your best score is recorded.';
    } else {
        // Insert new score
        $insertStmt = $pdo->prepare("
            INSERT INTO scores (CHILD_ID, QUIZ_ID, SCORE_PERCENTAGE, TOTAL_QUESTIONS, CORRECT_ANSWERS, DATE_COMPLETED)
            VALUES ( :child_id, :quiz_id, :score, :total, :correct, NOW())
        ");
        $insertStmt->execute([
            ':child_id' => $childId ?: null,
            ':quiz_id' => $quizId,
            ':score' => $scorePercentage,
            ':total' => $totalQuestions,
            ':correct' => $correctCount
        ]);

        $message = 'Quiz completed successfully!';
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'score' => [
            'percentage' => $scorePercentage,
            'correct' => $correctCount,
            'total' => $totalQuestions
        ]
    ]);
} catch (Exception $e) {
    error_log("Quiz Submit Score Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
