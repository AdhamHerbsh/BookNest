<?php

/**
 * Quiz Page - Takes quiz by book_id
 * Dynamically loads and displays quiz questions
 */

require_once 'core/db/config.php';
require_once 'core/auth/session.php';

// Get book_id from URL
$bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : null;
$quizId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Validate: must have either book_id or quiz_id
if (!$bookId && !$quizId) {
    // Redirect to library if no valid ID
    header('Location: ?page=library');
    exit;
}

// Fetch quiz data
$pdo = getDatabaseConnection();
$quiz = null;
$questions = [];
$book = null;

try {
    if ($bookId) {
        // Get quiz by book_id
        $quizStmt = $pdo->prepare("
            SELECT q.ID, q.TITLE, q.DESCRIPTION, q.BOOK_ID,
                   b.TITLE as BOOK_TITLE, b.COVER as BOOK_COVER, b.AUTHOR as BOOK_AUTHOR
            FROM quizzes q
            JOIN books b ON q.BOOK_ID = b.ID
            WHERE q.BOOK_ID = :book_id
            LIMIT 1
        ");
        $quizStmt->execute([':book_id' => $bookId]);
        $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Get quiz by quiz_id
        $quizStmt = $pdo->prepare("
            SELECT q.ID, q.TITLE, q.DESCRIPTION, q.BOOK_ID,
                   b.TITLE as BOOK_TITLE, b.COVER as BOOK_COVER, b.AUTHOR as BOOK_AUTHOR
            FROM quizzes q
            LEFT JOIN books b ON q.BOOK_ID = b.ID
            WHERE q.ID = :quiz_id
        ");
        $quizStmt->execute([':quiz_id' => $quizId]);
        $quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($quiz) {
        // Get questions
        $questionsStmt = $pdo->prepare("
            SELECT ID, QUESTION, TYPE 
            FROM questions 
            WHERE QUIZ_ID = :quiz_id 
            ORDER BY ID
        ");
        $questionsStmt->execute([':quiz_id' => $quiz['ID']]);
        $questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get options for each question
        foreach ($questions as &$question) {
            $optionsStmt = $pdo->prepare("
                SELECT ID, `OPTION` 
                FROM options 
                WHERE QUESTION_ID = :question_id 
                ORDER BY ID
            ");
            $optionsStmt->execute([':question_id' => $question['ID']]);
            $question['options'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    error_log("Quiz fetch error: " . $e->getMessage());
}

$quizAvailable = $quiz && count($questions) > 0;
$totalQuestions = count($questions);
?>

<?php if (!$quizAvailable): ?>
    <!-- No Quiz Available -->
    <section class="vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center p-5">
            <i class="bi bi-clipboard-x text-muted" style="font-size: 5rem;"></i>
            <h2 class="mt-4 fw-bold" data-i18n="quiz.not_available_title">Quiz Not Available</h2>
            <p class="text-muted mb-4" data-i18n="quiz.not_available_desc">
                Sorry, there's no quiz available for this book yet. Check back later!
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="?page=library" class="btn btn-primary btn-lg">
                    <i class="bi bi-book me-2"></i>
                    <span data-i18n="quiz.explore_library">Explore Library</span>
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>
                    <span data-i18n="quiz.go_back">Go Back</span>
                </a>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- Quiz Interface -->
    <section class="min-vh-100 py-4 mt-5">
        <div class="container mt-5">
            <!-- Quiz Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($quiz['BOOK_COVER']): ?>
                                <img src="<?php echo htmlspecialchars($quiz['BOOK_COVER']); ?>"
                                    alt="Book cover" class="rounded-3 shadow-sm" style="width: 60px; height: 80px; object-fit: cover;">
                            <?php endif; ?>
                            <div>
                                <h4 class="mb-1 fw-bold"><?php echo htmlspecialchars($quiz['TITLE']); ?></h4>
                                <?php if ($quiz['BOOK_TITLE']): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-book me-1"></i>
                                        <?php echo htmlspecialchars($quiz['BOOK_TITLE']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="?page=library" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>
                            <span data-i18n="quiz.exit">Exit Quiz</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quiz Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" id="quizCard">
                <div class="card-body p-4">
                    <!-- Progress -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-semibold" id="questionCounter">
                            <span data-i18n="quiz.question">Question</span>
                            <span id="currentQuestionNum">1</span>
                            <span data-i18n="quiz.of">of</span>
                            <span id="totalQuestionsNum"><?php echo $totalQuestions; ?></span>
                        </div>
                        <div class="fs-5 text-primary fw-bold" id="quizTimer">00:00</div>
                    </div>

                    <div class="progress rounded-pill mb-4" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" id="quizProgress"
                            style="width: <?php echo (1 / $totalQuestions) * 100; ?>%;"
                            aria-valuenow="1" aria-valuemin="0" aria-valuemax="<?php echo $totalQuestions; ?>">
                        </div>
                    </div>

                    <!-- Question Display Area -->
                    <div id="questionContainer">
                        <!-- Questions will be rendered here by JavaScript -->
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary px-4" id="prevQuestionBtn" disabled>
                            <i class="bi bi-arrow-left me-2"></i>
                            <span data-i18n="quiz.previous">Previous</span>
                        </button>
                        <button type="button" class="btn btn-primary px-4" id="nextQuestionBtn">
                            <span data-i18n="quiz.next">Next</span>
                            <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                        <button type="button" class="btn btn-success px-4 d-none" id="submitQuizBtn">
                            <i class="bi bi-check-lg me-2"></i>
                            <span data-i18n="quiz.submit">Submit Quiz</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Modal -->
    <div class="modal fade" id="quizResultsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-body p-0">
                    <!-- Results Header -->
                    <div class="text-center py-4" id="resultsHeader">
                        <div class="mb-3" id="resultsIcon">
                            <!-- Icon will be set by JavaScript -->
                        </div>
                        <h2 class="text-white fw-bold mb-2" id="resultsTitle">Quiz Complete!</h2>
                        <p class="text-white mb-0 fs-5" id="resultsSubtitle"></p>
                    </div>

                    <!-- Score Display -->
                    <div class="p-4 text-center">
                        <div class="display-1 fw-bold mb-2" id="scorePercentage">0%</div>
                        <p class="text-muted mb-4" id="scoreDetails">0 of 0 correct</p>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="?page=library" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-book me-2"></i>
                                <span data-i18n="quiz.back_to_library">Back to Library</span>
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill px-4" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                <span data-i18n="quiz.try_again">Try Again</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pass data to JavaScript -->
    <script>
        window.quizConfig = {
            quizId: <?php echo $quiz['ID']; ?>,
            bookId: <?php echo $quiz['BOOK_ID'] ?: 'null'; ?>,
            totalQuestions: <?php echo $totalQuestions; ?>,
            questions: <?php echo json_encode($questions); ?>
        };
    </script>
<?php endif; ?>