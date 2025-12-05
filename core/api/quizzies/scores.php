<?php

/**
 * Quiz Scores API
 * Fetches quiz scores for children associated with the logged-in user (Parent or Educator)
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
    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('You must be logged in to view scores');
    }

    $pdo = getDatabaseConnection();
    $userId = getCurrentUserId();
    $userRole = $_SESSION['user_role'] ?? 'PARENT'; // Default to PARENT if not set

    // Determine if we want summary data (for account page) or detailed data (for edu dashboard)
    $view = isset($_GET['view']) ? $_GET['view'] : 'summary';

    // Filter by specific child if provided
    $childId = isset($_GET['child_id']) ? (int)$_GET['child_id'] : null;

    if ($view === 'summary') {
        // --- SUMMARY VIEW (For Parent Account Page) ---
        // Get list of children with their aggregate stats

        // First, verify children belong to this user
        $childrenSql = "SELECT ID, NAME, AVATER FROM children WHERE USER_ID = :userId";
        if ($childId) {
            $childrenSql .= " AND ID = :childId";
        }

        $childrenStmt = $pdo->prepare($childrenSql);
        $params = [':userId' => $userId];
        if ($childId) {
            $params[':childId'] = $childId;
        }
        $childrenStmt->execute($params);
        $children = $childrenStmt->fetchAll(PDO::FETCH_ASSOC);

        $summaryData = [];

        foreach ($children as $child) {
            // Get stats for each child
            $statsStmt = $pdo->prepare("
                SELECT 
                    COUNT(*) as total_quizzes,
                    AVG(SCORE_PERCENTAGE) as average_score
                FROM scores 
                WHERE CHILD_ID = :childId
            ");
            $statsStmt->execute([':childId' => $child['ID']]);
            $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

            // Get recent scores
            $recentStmt = $pdo->prepare("
                SELECT 
                    qs.SCORE_PERCENTAGE,
                    qs.DATE_COMPLETED,
                    q.TITLE as quiz_title,
                    b.TITLE as book_title
                FROM scores qs
                JOIN quizzes q ON qs.QUIZ_ID = q.ID
                LEFT JOIN books b ON q.BOOK_ID = b.ID
                WHERE qs.CHILD_ID = :childId
                ORDER BY qs.DATE_COMPLETED DESC
                LIMIT 5
            ");
            $recentStmt->execute([':childId' => $child['ID']]);
            $recentScores = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

            $summaryData[] = [
                'child_id' => $child['ID'],
                'child_name' => $child['NAME'],
                'child_avatar' => $child['AVATER'],
                'total_quizzes' => (int)$stats['total_quizzes'],
                'average_score' => round((float)$stats['average_score'], 1),
                'recent_scores' => $recentScores
            ];
        }

        echo json_encode(['success' => true, 'data' => $summaryData]);
    } else if ($view === 'detailed') {
        // --- DETAILED VIEW (For Educator Dashboard) ---
        // Get comprehensive list of ALL scores for all associated children (no filters)

        $sql = "
        SELECT
            qs.ID,
            qs.SCORE_PERCENTAGE,
            qs.DATE_COMPLETED,
            qs.TOTAL_QUESTIONS,
            qs.CORRECT_ANSWERS,
            c.ID AS child_id,
            c.NAME AS child_name,
            q.ID AS quiz_id,
            q.TITLE AS quiz_title,
            b.TITLE AS book_title
        FROM scores qs
        JOIN children c ON qs.CHILD_ID = c.ID
        JOIN quizzes q ON qs.QUIZ_ID = q.ID
        LEFT JOIN books b ON q.BOOK_ID = b.ID
       
        ORDER BY qs.DATE_COMPLETED DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate class summary metrics
        $totalScores = count($scores);
        $classAverage = 0;
        $lowestScoreQuiz = null;

        if ($totalScores > 0) {
            $sumScores = 0;
            $quizScores = []; // To track average per quiz

            foreach ($scores as $score) {
                $sumScores += $score['SCORE_PERCENTAGE'];

                $qTitle = $score['quiz_title'];
                if (!isset($quizScores[$qTitle])) {
                    $quizScores[$qTitle] = ['total' => 0, 'count' => 0];
                }
                $quizScores[$qTitle]['total'] += $score['SCORE_PERCENTAGE'];
                $quizScores[$qTitle]['count']++;
            }

            $classAverage = round($sumScores / $totalScores, 1);

            // Find most difficult quiz (lowest average)
            $minAvg = 100;
            foreach ($quizScores as $title => $data) {
                $avg = $data['total'] / $data['count'];
                if ($avg < $minAvg) {
                    $minAvg = $avg;
                    $lowestScoreQuiz = $title;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $scores,
            'summary' => [
                'class_average' => $classAverage,
                'total_attempts' => $totalScores,
                'most_difficult_quiz' => $lowestScoreQuiz
            ]
        ]);
    } else {
        throw new Exception('Invalid view parameter');
    }
} catch (Exception $e) {
    error_log("Scores API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
