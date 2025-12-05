<?php

/**
 * Quizzes List Page - For Educators
 * Displays list of quizzes with edit/delete controls
 */

if (!isEducator() && !isAdmin()) {
    include "core/pages/401.php";
    exit;
}

$currentUserId = getCurrentUserId();
$isAdminUser = isAdmin();
?>

<section>
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" data-i18n="quizzes.page_title">My Quizzes</h2>
                <p class="text-muted mb-0" data-i18n="quizzes.page_subtitle">Create and manage your quizzes</p>
            </div>
            <a href="?page=create-quiz" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span data-i18n="quizzes.btn_create">Create Quiz</span>
            </a>
        </div>

        <!-- Loading State -->
        <div id="quizzesLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Empty State -->
        <div id="quizzesEmpty" class="text-center py-5 d-none">
            <i class="bi bi-clipboard2-x fs-1 text-muted mb-3 d-block"></i>
            <h4 class="text-muted" data-i18n="quizzes.no_quizzes">No quizzes yet</h4>
            <p class="text-muted" data-i18n="quizzes.no_quizzes_desc">Start by creating your first quiz</p>
            <a href="?page=create-quiz" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg me-2"></i>
                <span data-i18n="quizzes.btn_create_first">Create First Quiz</span>
            </a>
        </div>

        <!-- Quizzes Grid -->
        <div class="row g-4" id="quizzesGrid">
            <!-- Quizzes will be loaded here dynamically -->
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteQuizModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                <h4 class="mb-3" data-i18n="quizzes.delete_confirm_title">Delete Quiz?</h4>
                <p class="text-muted mb-4" data-i18n="quizzes.delete_confirm_desc">
                    This action cannot be undone. All questions and student scores will be permanently deleted.
                </p>
                <input type="hidden" id="deleteQuizId">
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <span data-i18n="quizzes.btn_cancel">Cancel</span>
                    </button>
                    <button type="button" class="btn btn-danger px-4" id="confirmDeleteQuizBtn">
                        <span data-i18n="quizzes.btn_delete">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP variables to JavaScript
    window.quizPageConfig = {
        currentUserId: <?php echo json_encode($currentUserId); ?>,
        isAdmin: <?php echo $isAdminUser ? 'true' : 'false'; ?>
    };
</script>