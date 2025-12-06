<?php
require_once 'core/db/config.php';
include_once 'core/layout/book-card.php';

// Fetch user's favorite books
$books = [];
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT 
                b.ID as id,
                b.TITLE as title,
                b.DESCRIPTION as description,
                b.AUTHOR as author,
                b.COVER as coverImage,
                b.FILE_PATH as filePath,
                b.AGE_GROUP as ageGroup
            FROM favorites f
            INNER JOIN books b ON f.BOOK_ID = b.ID
            WHERE f.USER_ID = :user_id
            AND b.IS_ACTIVE = 'Y'
            ORDER BY f.CREATED_DATE DESC
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching favorites: " . $e->getMessage());
        $books = [];
    }
}
?>

<section class="container my-5 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
        <h1 class="fw-bold">
            <i class="bi bi-heart-fill text-danger me-2"></i>
            <span data-i18n="account.favorites_title">Favorites</span>
        </h1>
        <?php if (!empty($books)): ?>
            <button type="button" class="btn btn-danger d-flex align-items-center gap-2 px-3" onclick="clearAllFavorites()">
                <i class="bi bi-trash"></i>
                <span data-i18n="account.btn_clear_all">Clear All</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Books Grid -->
    <div class="row g-4">
        <?php if (empty($books)): ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-heart display-1 text-muted"></i>
                    <p class="text-muted mt-3" data-i18n="account.no_favorites">No favorite books yet.</p>
                    <a href="?page=library" class="btn btn-primary mt-3">
                        <i class="bi bi-book me-2"></i>
                        <span data-i18n="library.btn_start_exploring">Start Exploring</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <?php echo renderBookCard($book); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>