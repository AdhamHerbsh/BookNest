<?php

/**
 * Admin Quizzes Management Page
 * Full CRUD access for administrators
 */

if (!isAdmin()) {
    include "core/pages/401.php";
    exit;
}
?>

<section class="mt-5">
    <div class="my-5 py-5 px-4 bg-white">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h1 class="mb-1" data-i18n="admin.quizzes_title">Admin - Quizzes Management</h1>
                <p class="text-muted mb-0" data-i18n="admin.quizzes_subtitle">
                    View and manage all quizzes in the system
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="?page=create-quiz" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>
                    <span data-i18n="quizzes.btn_create">Create Quiz</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-primary text-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white mb-0" id="totalQuizzes">-</h3>
                                <small data-i18n="admin.total_quizzes">Total Quizzes</small>
                            </div>
                            <i class="bi bi-clipboard2-check fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-success text-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white mb-0" id="totalQuestions">-</h3>
                                <small data-i18n="admin.total_questions">Total Questions</small>
                            </div>
                            <i class="bi bi-question-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-info text-white rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white mb-0" id="totalCreators">-</h3>
                                <small data-i18n="admin.quiz_creators">Quiz Creators</small>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-warning text-dark rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white mb-0" id="totalAttempts">-</h3>
                                <small data-i18n="admin.quiz_attempts">Quiz Attempts</small>
                            </div>
                            <i class="bi bi-graph-up fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <!-- Loading State -->
                <div id="adminQuizzesLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive d-none" id="adminQuizzesTableContainer">
                    <table class="table table-striped table-hover align-middle" id="adminQuizzesTable">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th data-i18n="admin.col_title">Title</th>
                                <th data-i18n="admin.col_creator">Creator</th>
                                <th data-i18n="admin.col_book">Related Book</th>
                                <th data-i18n="admin.col_questions">Questions</th>
                                <th data-i18n="admin.col_created">Created</th>
                                <th data-i18n="admin.col_actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminQuizzesBody">
                            <!-- Quizzes will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="adminQuizzesEmpty" class="text-center py-5 d-none">
                    <i class="bi bi-clipboard2-x fs-1 text-muted mb-3 d-block"></i>
                    <h4 class="text-muted" data-i18n="quizzes.no_quizzes">No quizzes found</h4>
                </div>
            </div>
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
    // Admin page configuration
    window.quizPageConfig = {
        isAdmin: true,
        isAdminPage: true,
        currentUserId: <?php echo json_encode(getCurrentUserId()); ?>
    };
</script>