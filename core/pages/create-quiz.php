<?php

/**
 * Create/Edit Quiz Page - For Educators
 * Dynamic form for creating and editing quizzes with questions
 */

if (!isEducator() && !isAdmin()) {
    include "core/pages/401.php";
    exit;
}

$quizId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEditMode = $quizId !== null;
$currentUserId = getCurrentUserId();
$isAdminUser = isAdmin();

// Fetch books for dropdown
require_once 'core/db/config.php';
$pdo = getDatabaseConnection();
$booksStmt = $pdo->query("SELECT b.ID, b.TITLE FROM books b LEFT JOIN quizzes q ON b.ID = q.BOOK_ID WHERE b.IS_ACTIVE = 'Y' AND q.BOOK_ID IS NULL");
$books = $booksStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <div class="container py-5">
        <div class="bg-light p-4 rounded-4 my-3">
            <form id="quizForm" novalidate>
                <input type="hidden" id="quizId" value="<?php echo $quizId ?: ''; ?>">

                <!-- Form Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <?php if ($isEditMode): ?>
                            <span data-i18n="quizzes.edit_title">Edit Quiz</span>
                        <?php else: ?>
                            <span data-i18n="quizzes.create_title">Create Quiz</span>
                        <?php endif; ?>
                    </h3>
                    <a href="?page=quizzes" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        <span data-i18n="quizzes.btn_back">Back to Quizzes</span>
                    </a>
                </div>

                <div class="row g-4">
                    <!-- Left Column - Quiz Details -->
                    <div class="col-lg-5">
                        <div class="mb-3">
                            <label for="quizTitle" class="form-label fw-semibold" data-i18n="quizzes.label_title">
                                Quiz Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="quizTitle"
                                placeholder="Enter quiz title" required>
                            <div class="invalid-feedback" data-i18n="quizzes.error_title_required">
                                Quiz title is required
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quizBook" class="form-label fw-semibold" data-i18n="quizzes.label_book">
                                Related Book (Optional)
                            </label>
                            <select class="form-select" id="quizBook">
                                <option value="">Choose a book...</option>
                                <?php foreach ($books as $book): ?>
                                    <option value="<?php echo htmlspecialchars($book['ID']); ?>">
                                        <?php echo htmlspecialchars($book['TITLE']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quizDescription" class="form-label fw-semibold" data-i18n="quizzes.label_description">
                                Description
                            </label>
                            <textarea class="form-control" id="quizDescription" rows="5"
                                placeholder="Enter quiz description"></textarea>
                        </div>

                        <!-- Questions Summary -->
                        <div class="border rounded-3 p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0 fw-semibold" data-i18n="quizzes.questions_summary">
                                    Questions Summary
                                </h6>
                                <span class="badge bg-primary" id="questionCount">0</span>
                            </div>
                            <p class="text-muted small mb-0" data-i18n="quizzes.questions_summary_desc">
                                Add questions using the form on the right
                            </p>
                        </div>
                    </div>

                    <!-- Right Column - Questions Builder -->
                    <div class="col-lg-7">
                        <!-- Current Question Form -->
                        <div class="border rounded-3 p-3 bg-white mb-3" id="questionBuilder">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle text-primary"></i>
                                <span data-i18n="quizzes.add_question">Add Question</span>
                            </h6>

                            <div class="mb-3">
                                <input type="text" class="form-control" id="questionText"
                                    placeholder="Enter your question">
                            </div>

                            <!-- Options -->
                            <div class="options-container mb-3">
                                <div class="mb-2">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="radio"
                                                name="correctAnswer" value="0">
                                        </div>
                                        <input type="text" class="form-control option-input"
                                            data-index="0" placeholder="Option A">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="radio"
                                                name="correctAnswer" value="1">
                                        </div>
                                        <input type="text" class="form-control option-input"
                                            data-index="1" placeholder="Option B">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="radio"
                                                name="correctAnswer" value="2">
                                        </div>
                                        <input type="text" class="form-control option-input"
                                            data-index="2" placeholder="Option C">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="input-group">
                                        <div class="input-group-text bg-white">
                                            <input class="form-check-input mt-0" type="radio"
                                                name="correctAnswer" value="3">
                                        </div>
                                        <input type="text" class="form-control option-input"
                                            data-index="3" placeholder="Option D">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success flex-grow-1" id="addQuestionBtn">
                                    <i class="bi bi-plus-lg me-2"></i>
                                    <span data-i18n="quizzes.btn_add_question">Add Question</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearQuestionBtn">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Questions Preview List -->
                        <div class="border rounded-3 p-3 bg-white">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-list-check text-primary"></i>
                                <span data-i18n="quizzes.questions_preview">Questions Preview</span>
                            </h6>

                            <div id="questionsPreview" class="list-group">
                                <!-- Empty State -->
                                <div id="noQuestionsMessage" class="text-center text-muted py-4">
                                    <i class="bi bi-clipboard2-plus fs-2 mb-2 d-block"></i>
                                    <p class="mb-0" data-i18n="quizzes.no_questions_yet">
                                        No questions added yet
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="?page=quizzes" class="btn btn-light px-4">
                        <span data-i18n="quizzes.btn_cancel">Cancel</span>
                    </a>
                    <button type="submit" class="btn btn-primary px-4" id="saveQuizBtn">
                        <i class="bi bi-check-lg me-2"></i>
                        <span data-i18n="quizzes.btn_save">Save Quiz</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    // Pass PHP variables to JavaScript
    window.quizFormConfig = {
        isEditMode: <?php echo $isEditMode ? 'true' : 'false'; ?>,
        quizId: <?php echo $quizId ? $quizId : 'null'; ?>,
        currentUserId: <?php echo json_encode($currentUserId); ?>,
        isAdmin: <?php echo $isAdminUser ? 'true' : 'false'; ?>
    };
</script>