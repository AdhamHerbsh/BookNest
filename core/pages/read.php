<?php
// read.php - PDF Book Reader Page
// Fetches book data from database and renders PDF pages

require_once 'core/db/config.php';

/**
 * Fetch a single book by ID for reading
 * 
 * @param int $bookId The book ID from URL
 * @return array|null Book data or null if not found
 */
function fetchBookForReader($bookId)
{
    $pdo = getDatabaseConnection();

    try {
        $stmt = $pdo->prepare("SELECT ID as id, TITLE as title, DESCRIPTION as description, AUTHOR as author, 
                                      AGE_GROUP as ageGroup, COVER as coverImage, FILE_PATH as filePath
                               FROM books 
                               WHERE ID = :id AND IS_ACTIVE = 'Y'");
        $stmt->execute([':id' => $bookId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching book for reader: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if a quiz exists for this book
 * 
 * @param int $bookId The book ID
 * @return bool True if quiz exists
 */
function hasQuizForBook($bookId)
{
    $pdo = getDatabaseConnection();

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE BOOK_ID = :book_id");
        $stmt->execute([':book_id' => $bookId]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Error checking quiz: " . $e->getMessage());
        return false;
    }
}

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch book data
$book = fetchBookForReader($bookId);

// Handle missing book
$bookNotFound = false;
if (!$book) {
    $bookNotFound = true;
    $book = [
        'id' => 0,
        'title' => 'Book Not Found',
        'author' => 'Unknown Author',
        'description' => 'The requested book could not be found.',
        'ageGroup' => '',
        'coverImage' => 'assets/images/books/fallback-cover.png',
        'filePath' => ''
    ];
}

// Check if quiz is available for this book
$hasQuiz = !$bookNotFound && hasQuizForBook($bookId);

// Get the PDF file path (ensure it starts from the root)
$pdfPath = $book['filePath'] ? $book['filePath'] : '';
?>

<!-- PDF.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>


<section>
    <!-- Reader Layout -->
    <div class="container-fluid p-0 reader-container mt-5">
        <div class="d-flex">
            <!-- Left Sidebar -->
            <aside class="reader-sidebar" id="readerSidebar">
                <!-- Header -->
                <div class="sidebar-header">
                    <img class="logo-icon" src="assets/images/BookNest Logo/Logo Icon Square RBG.png" alt="BookNest Logo">
                    <h4>BookNest</h4>
                </div>

                <?php if (!$bookNotFound): ?>
                    <!-- Book Info -->
                    <div class="book-info mb-2">
                        <h6 class="fw-bold text-truncate" title="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h6>
                        <small class="text-muted">By <?php echo htmlspecialchars($book['author']); ?></small>
                    </div>

                    <!-- Pages Section -->
                    <div class="pages-section">
                        <div class="pages-section-title">
                            <i class="bi bi-file-earmark-text"></i>
                            <span data-i18n="read.pages_title">Pages</span>
                        </div>
                        <nav class="page-thumbnails" id="pageThumbnails">
                            <!-- Thumbnails will be generated dynamically by JavaScript -->
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2 small">Loading pages...</p>
                            </div>
                        </nav>
                    </div>

                    <!-- Quiz Button (only show if quiz available) -->
                    <?php if ($hasQuiz): ?>
                        <div class="mt-auto">
                            <a href="?page=quiz&book_id=<?php echo htmlspecialchars($book['id']); ?>"
                                class="btn btn-start-quiz w-100">
                                <i class="bi bi-patch-question"></i>
                                <span data-i18n="read.take_quiz">Take Quiz</span>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </aside>

            <!-- Main Viewer -->
            <main class="reader-main">
                <?php if ($bookNotFound): ?>
                    <!-- Error State -->
                    <div class="book-error">
                        <i class="bi bi-book"></i>
                        <h3 data-i18n="read.book_not_found">Book Not Found</h3>
                        <p class="text-muted" data-i18n="read.book_not_found_desc">
                            The requested book could not be found. Please check the URL or go back to the library.
                        </p>
                        <a href="?page=library" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-left me-2"></i>
                            <span data-i18n="read.back_to_library">Back to Library</span>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Toolbar -->
                    <div class="reader-toolbar" role="toolbar" aria-label="PDF Toolbar">
                        <div class="toolbar-btn-group">
                            <button type="button" class="toolbar-btn" id="zoomOutBtn" title="Zoom Out" aria-label="Zoom Out">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                            <button type="button" class="toolbar-btn" id="zoomInBtn" title="Zoom In" aria-label="Zoom In">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                            <button type="button" class="toolbar-btn" id="fullscreenBtn" title="Fullscreen"
                                aria-label="Fullscreen">
                                <i class="bi bi-fullscreen"></i>
                            </button>
                        </div>
                        <div class="page-counter-badge" id="pageCounter" aria-live="polite" aria-atomic="true">
                            Page 1 of 1
                        </div>
                    </div>

                    <!-- Book Stage -->
                    <div class="book-stage" id="bookStage">
                        <!-- Previous Page -->
                        <button class="reader-nav-arrow prev" id="prevBtn" aria-label="Previous Page" type="button">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <!-- Page Content -->
                        <div class="page-content-wrapper">
                            <div class="loading-spinner" id="loadingSpinner">
                                <i class="bi bi-arrow-repeat spin"></i>
                            </div>
                            <canvas id="pdfCanvas"></canvas>
                        </div>

                        <!-- Next Page -->
                        <button class="reader-nav-arrow next" id="nextBtn" aria-label="Next Page" type="button">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 flex-wrap justify-content-center">
                        <button class="btn-read-aloud" type="button" id="readAloudBtn" aria-label="Read Aloud">
                            <i class="bi bi-volume-up-fill"></i>
                            <span id="readAloudText" data-i18n="read.read_aloud">Read Aloud</span>
                        </button>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</section>


<!-- Book Completion Modal -->
<div class="modal fade" id="bookCompleteModal" tabindex="-1" aria-labelledby="bookCompleteModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-body p-0">
                <!-- Celebration Header -->
                <div class="bg-success text-white text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-trophy-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold mb-2" data-i18n="read.congrats_title">🎉 Congratulations! 🎉</h2>
                    <p class="mb-0 fs-5" data-i18n="read.congrats_subtitle">You finished the book!</p>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <div class="row bg-light rounded-4 p-3 align-items-center mb-4">
                        <div class="col-3">
                            <img class="rounded-3 img-fluid shadow-sm"
                                src="<?php echo htmlspecialchars($book['coverImage'] ?? 'assets/images/books/fallback-cover.png'); ?>"
                                alt="Book cover">
                        </div>
                        <div class="col-9">
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($book['title'] ?? 'Book Title'); ?></h5>
                            <p class="text-muted mb-0 small">By <?php echo htmlspecialchars($book['author'] ?? 'Author'); ?></p>
                        </div>
                    </div>

                    <?php if ($hasQuiz): ?>
                        <div class="text-center mb-3">
                            <p class="fs-5 mb-3" data-i18n="read.quiz_prompt">Ready to test your knowledge?</p>
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <a href="?page=quiz&book_id=<?php echo htmlspecialchars($book['id']); ?>"
                                    class="btn btn-success btn-lg rounded-pill px-4" id="startQuizBtn">
                                    <i class="bi bi-patch-question-fill me-2"></i>
                                    <span data-i18n="read.start_quiz_now">Start Quiz Now</span>
                                </a>
                                <button class="btn btn-outline-secondary btn-lg rounded-pill px-4" data-bs-dismiss="modal">
                                    <span data-i18n="read.maybe_later">Maybe Later</span>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center mb-3">
                            <p class="text-muted mb-3" data-i18n="read.no_quiz_available">No quiz available for this book yet.</p>
                            <div class="d-flex gap-3 justify-content-center flex-wrap">
                                <a href="?page=library" class="btn btn-primary btn-lg rounded-pill px-4">
                                    <i class="bi bi-book me-2"></i>
                                    <span data-i18n="read.explore_more">Explore More Books</span>
                                </a>
                                <button class="btn btn-outline-secondary btn-lg rounded-pill px-4" data-bs-dismiss="modal">
                                    <span data-i18n="read.close">Close</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.bookNestConfig = {
        pdfPath: '<?php echo addslashes($pdfPath); ?>',
        bookNotFound: <?php echo $bookNotFound ? 'true' : 'false'; ?>,
        bookId: <?php echo $bookId ?: 0; ?>,
        hasQuiz: <?php echo $hasQuiz ? 'true' : 'false'; ?>
    };
</script>