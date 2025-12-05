<?php
// Ensure only educators can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'EDU') {
    echo "<script>window.location.href = '?page=home';</script>";
    exit;
}
?>

<section class="mt-5 pt-5">
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold" data-i18n="edu.dashboard_title">Educator Dashboard</h1>
                <p class="text-muted" data-i18n="edu.dashboard_subtitle">Track student progress and manage content</p>
            </div>
            <div class="d-flex gap-2">
                <a href="?page=upload" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-2"></i>
                    <span data-i18n="edu.btn_upload">Upload Book</span>
                </a>
                <a href="?page=create-quiz" class="btn btn-success">
                    <i class="bi bi-plus-lg me-2"></i>
                    <span data-i18n="edu.btn_create_quiz">Create Quiz</span>
                </a>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-4 mb-5" id="eduStatsContainer">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <h6 class="text-muted mb-0 text-uppercase small fw-bold" data-i18n="edu.stat_students">Total Students</h6>
                        </div>
                        <h2 class="fw-bold mb-0" id="statTotalStudents">-</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2">
                                <i class="bi bi-graph-up fs-4"></i>
                            </div>
                            <h6 class="text-muted mb-0 text-uppercase small fw-bold" data-i18n="edu.stat_avg_score">Class Average</h6>
                        </div>
                        <h2 class="fw-bold mb-0" id="statClassAverage">-</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                                <i class="bi bi-exclamation-triangle fs-4"></i>
                            </div>
                            <h6 class="text-muted mb-0 text-uppercase small fw-bold" data-i18n="edu.stat_difficult">Most Difficult Quiz</h6>
                        </div>
                        <h5 class="fw-bold mb-0 text-truncate" id="statDifficultQuiz">-</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" data-i18n="edu.filters_title">Filter Results</h5>
                <form id="eduFilterForm" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted" data-i18n="edu.filter_student">Student Name</label>
                        <select class="form-select" id="filterStudent">
                            <option value="" selected data-i18n="edu.filter_all_students">All Students</option>
                            <!-- Populated by JS -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted" data-i18n="edu.filter_date_start">Start Date</label>
                        <input type="date" class="form-control" id="filterStartDate">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted" data-i18n="edu.filter_date_end">End Date</label>
                        <input type="date" class="form-control" id="filterEndDate">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-2"></i>
                            <span data-i18n="edu.btn_apply_filters">Apply Filters</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="eduScoresTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3" data-i18n="edu.col_student">Student</th>
                                <th class="px-4 py-3" data-i18n="edu.col_quiz">Quiz Title</th>
                                <th class="px-4 py-3" data-i18n="edu.col_book">Book</th>
                                <th class="px-4 py-3 text-center" data-i18n="edu.col_score">Score</th>
                                <th class="px-4 py-3 text-center" data-i18n="edu.col_date">Date</th>
                                <th class="px-4 py-3 text-end" data-i18n="edu.col_details">Details</th>
                            </tr>
                        </thead>
                        <tbody id="eduScoresBody">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination (Optional - for future) -->
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted" id="showingCount">Showing 0 results</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Score Details Modal -->
<div class="modal fade" id="scoreDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" data-i18n="edu.modal_details_title">Quiz Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold mb-1" id="modalScoreVal">0%</div>
                    <p class="text-muted" id="modalScoreText">0/0 Correct</p>
                </div>
                <div class="bg-light rounded-3 p-3 mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Student</small>
                            <strong id="modalStudentName">-</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Date</small>
                            <strong id="modalDate">-</strong>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted d-block">Quiz</small>
                            <strong id="modalQuizTitle">-</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" data-i18n="edu.btn_close">Close</button>
            </div>
        </div>
    </div>
</div>