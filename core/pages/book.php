<?php
// book.php - Database-driven version

// Include database configuration and book card renderer
require_once 'core/db/config.php';
include_once 'core/layout/book-card.php';

/**
 * Fetch a single book by ID
 * 
 * @param int $bookId The book ID from URL
 * @return array|null Book data or null if not found
 */
function fetchBookById($bookId)
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
        error_log("Error fetching book: " . $e->getMessage());
        return null;
    }
}

/**
 * Fetch related books (same age group, excluding current)
 * 
 * @param string $ageGroup Current book's age group
 * @param int $currentBookId Current book ID to exclude
 * @param int $limit Number of books to fetch
 * @return array Array of book data
 */
function fetchRelatedBooks($ageGroup, $currentBookId, $limit = 4)
{
    $pdo = getDatabaseConnection();

    try {
        $stmt = $pdo->prepare("SELECT ID as id, TITLE as title, AUTHOR as author, COVER as coverImage
                               FROM books 
                               WHERE AGE_GROUP = :age_group 
                               AND ID != :current_id 
                               AND IS_ACTIVE = 'Y'
                               ORDER BY CREATED_DATE DESC
                               LIMIT :limit");
        $stmt->bindValue(':age_group', $ageGroup, PDO::PARAM_STR);
        $stmt->bindValue(':current_id', $currentBookId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching related books: " . $e->getMessage());
        return [];
    }
}

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch book data
$book = fetchBookById($bookId);

// Handle missing book
if (!$book) {
    http_response_code(404);
    // Still render the layout but with error message
    $book = [
        'id' => 0,
        'title' => 'Book Not Found',
        'author' => 'Unknown Author',
        'description' => 'The requested book could not be found. Please check the URL or return to the library.',
        'ageGroup' => '',
        'coverImage' => 'assets/images/books/fallback-cover.png',
        'filePath' => ''
    ];
}

// Extract age number for badge (e.g., "10-12" -> "12")
$ageBadge = '';
if ($book['ageGroup']) {
    $ageParts = explode('-', $book['ageGroup']);
    $ageBadge = end($ageParts) . ' yrs';
}

// Fetch related books if age group is available
$relatedBooks = $book['ageGroup'] ? fetchRelatedBooks($book['ageGroup'], $book['id']) : [];
?>
<div class="container-fluid">
    <section class="mt-5 px-5">
        <div class="row">
            <div class="col-12 col-md-4">
                <img class="img-fluid w-100 rounded-4"
                    src="<?php echo htmlspecialchars($book['coverImage']); ?>"
                    alt="<?php echo htmlspecialchars($book['title'] . ' Cover'); ?>"
                    onerror="this.src='assets/images/books/fallback-cover.png'" />
            </div>
            <div class="col-12 col-md-8">
                <div class="container">
                    <span class="badge-lg py-2 px-4 rounded-pill bg-primary-light text-primary">
                        Age <?php echo htmlspecialchars($ageBadge); ?>
                    </span>
                    <h1 class="fw-bold my-2"><?php echo htmlspecialchars($book['title']); ?></h1>
                    <p class="fs-2">By <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="lead"><?php echo htmlspecialchars($book['description']); ?></p>

                    <div class="row">
                        <div class="col-6">
                            <div class="d-grid gap-2">
                                <a href="<?php echo htmlspecialchars($book['filePath'] ?: '#'); ?>"
                                    class="btn btn-primary"
                                    target="_blank">
                                    Read Now
                                </a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-grid gap-2">
                                <button type="button"
                                    class="btn btn-light"
                                    data-book-id="<?php echo htmlspecialchars($book['id']); ?>"
                                    onclick="toggleFavorite(this)">
                                    Add to Favorites
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 my-4 mx-auto rounded-4 bg-light p-4">
                            <h3 class="mb-3">Interactive Features</h3>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-dark">Audio Narration</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="audioNarration" checked>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-dark">Interactive Elements</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="interactiveElements" checked>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-dark">Animations</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="animations" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-collections">
        <div class="container-fluid px-4">
            <div class="row mb-4">
                <h2 class="fw-bold">You Might Also Like</h2>
            </div>

            <?php if (empty($relatedBooks)): ?>
                <div class="text-center py-4">
                    <p class="text-muted">No related books found.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($relatedBooks as $relatedBook): ?>
                        <div class="col-6 col-md-3 mb-4">
                            <div class="card bg-transparent h-100 rounded-4 hover border-0">
                                <img src="<?php echo htmlspecialchars($relatedBook['coverImage']); ?>"
                                    class="card-img-top rounded-4 p-2"
                                    alt="<?php echo htmlspecialchars($relatedBook['title'] . ' Cover'); ?>"
                                    loading="lazy"
                                    onerror="this.src='assets/images/books/fallback-cover.png'" />
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($relatedBook['title']); ?></h5>
                                    <p class="card-text">By <?php echo htmlspecialchars($relatedBook['author']); ?></p>
                                    <div class="card-overlay rounded-4">
                                        <a href="?page=book&id=<?php echo htmlspecialchars($relatedBook['id']); ?>"
                                            class="btn btn-primary mb-4">Read</a>
                                        <a href="?page=book&id=<?php echo htmlspecialchars($relatedBook['id']); ?>"
                                            class="text-white">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // Simple favorite toggle function (enhance as needed)
    function toggleFavorite(button) {
        const bookId = button.getAttribute('data-book-id');
        const isFavorited = button.classList.contains('favorited');

        // Here you would typically make an AJAX call to save the favorite status
        console.log('Toggle favorite for book:', bookId);

        if (isFavorited) {
            button.classList.remove('favorited');
            button.textContent = 'Add to Favorites';
        } else {
            button.classList.add('favorited');
            button.textContent = 'Remove from Favorites';
        }
    }
</script>