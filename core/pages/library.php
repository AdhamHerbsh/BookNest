<?php
// library.php - Database-driven version

// Include required files
require_once 'core/db/config.php';
include_once 'core/layout/book-card.php';

/**
 * Fetch books from database with filters
 * 
 * @param int|null $limit Maximum books to fetch
 * @param string|null $ageGroup Filter by age group
 * @param string|null $searchTerm Search in title/description/author
 * @return array
 */
function fetchBooks($limit = null, $ageGroup = null, $searchTerm = null)
{
    $pdo = getDatabaseConnection();

    $sql = "SELECT ID as id, TITLE as title, DESCRIPTION as description, COVER as coverImage 
            FROM books 
            WHERE IS_ACTIVE = 'Y'";
    $params = [];

    // Apply age group filter
    if ($ageGroup && in_array($ageGroup, ['4-6', '7-9', '10-12'])) {
        $sql .= " AND AGE_GROUP = :age_group";
        $params[':age_group'] = $ageGroup;
    }

    // Apply search filter
    if ($searchTerm) {
        $sql .= " AND (TITLE LIKE :search_term OR DESCRIPTION LIKE :search_term OR AUTHOR LIKE :search_term)";
        $params[':search_term'] = '%' . $searchTerm . '%';
    }

    $sql .= " ORDER BY CREATED_DATE DESC";

    if ($limit) {
        $sql .= " LIMIT :limit";
        $params[':limit'] = (int)$limit;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        return [];
    }
}

// Get filter parameters from URL
$selectedAgeGroup = isset($_GET['age_group']) && in_array($_GET['age_group'], ['4-6', '7-9', '10-12']) ? $_GET['age_group'] : null;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;

// Fetch books for different sections
$featuredBooks = fetchBooks(12, $selectedAgeGroup, $searchTerm);
$recentBooks = fetchBooks(12, $selectedAgeGroup, $searchTerm); // Could use different criteria

// Helper function for active button state
function isAgeGroupActive($group, $selected)
{
    return $group === $selected ? 'active' : '';
}

// Build query string while preserving other parameters
function buildQueryString($params)
{
    return http_build_query(array_merge($_GET, $params));
}
?>
<div class="hero-image library" data-aos="fade"></div>
<div class="container-fluid">
    <div class="hero-content container text-center">
        <h1 class="display-1 fw-bold text-white" data-aos="fade-up" data-aos-delay="200" data-i18n="library.hero_title">Unlock a World of Stories</h1>
        <p class="fs-4 mb-4 text-white p-5" data-aos="fade-up" data-aos-delay="500" data-i18n="library.hero_text">
            Ignite your child's imagination with our interactive digital library. Featuring audio narration,
            animations, and progress tracking, BookNest makes reading fun and engaging for kids aged 4-12.
        </p>
        <a href="#featured-collections" class="btn btn-primary" data-aos="zoom-in" data-aos-delay="1000" data-i18n="library.btn_start_exploring">Start Exploring</a>
    </div>

    <section class="px-5">
        <div class="mb-4 text-center">
            <h1 class="display-2" data-i18n="library.explore_by_age">Explore by Age</h1>

            <!-- Show active filters -->
            <?php if ($selectedAgeGroup || $searchTerm): ?>
                <div class="mt-3">
                    <small class="text-muted">
                        <span data-i18n="library.filters_label">Filters:</span>
                        <?php if ($selectedAgeGroup): echo "Age $selectedAgeGroup";
                        endif; ?>
                        <?php if ($searchTerm): echo ($selectedAgeGroup ? ' • ' : '') . "Search: " . htmlspecialchars($searchTerm);
                        endif; ?>
                        | <a href="?page=library#featured-collections" class="text-decoration-none" data-i18n="library.clear_all">Clear all</a>
                    </small>
                </div>
            <?php endif; ?>
        </div>

        <div class="container">
            <form class="mb-3 w-100" method="GET" action="">
                <div class="container">
                    <div class="row d-flex justify-content-center mb-4 g-2">
                        <div class="col-4 col-md-2">
                            <a href="?<?php echo buildQueryString(['age_group' => '4-6']); ?>#featured-collections"
                                class="btn btn-outline-primary rounded-5 w-100 <?php echo isAgeGroupActive('4-6', $selectedAgeGroup); ?>">
                                4-6
                            </a>
                        </div>
                        <div class="col-4 col-md-2">
                            <a href="?<?php echo buildQueryString(['age_group' => '7-9']); ?>#featured-collections"
                                class="btn btn-outline-primary rounded-5 w-100 <?php echo isAgeGroupActive('7-9', $selectedAgeGroup); ?>">
                                7-9
                            </a>
                        </div>
                        <div class="col-4 col-md-2">
                            <a href="?<?php echo buildQueryString(['age_group' => '10-12']); ?>#featured-collections"
                                class="btn btn-outline-primary rounded-5 w-100 <?php echo isAgeGroupActive('10-12', $selectedAgeGroup); ?>">
                                10-12
                            </a>
                        </div>
                    </div>
                    <div class="input-group mx-auto flex-nowrap">
                        <input type="text" class="form-control-lg w-100 border-0"
                            placeholder="Search by title, author, or description..."
                            aria-label="Search"
                            name="search"
                            value="<?php echo htmlspecialchars($searchTerm ?? '', ENT_QUOTES); ?>"
                            data-i18n-placeholder="library.search_placeholder" />
                        <button type="submit" class="input-group-text bg-white border-0" id="basic-addon1">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <?php if ($selectedAgeGroup): ?>
                        <input type="hidden" name="age_group" value="<?php echo htmlspecialchars($selectedAgeGroup); ?>">
                    <?php endif; ?>
                    <input type="hidden" name="page" value="library">
                </div>
            </form>
        </div>
    </section>

    <section id="featured-collections" class="featured-collection">
        <div class="container-fluid px-4">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold" data-i18n="library.featured_collections">Featured Collections</h1>
                </div>
            </div>

            <?php if (empty($featuredBooks)): ?>
                <div class="text-center py-5">
                    <h3 data-i18n="library.no_books_found">No books found</h3>
                    <p data-i18n="library.try_adjusting_filters">Try adjusting your search filters.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <swiper-container class="book-slider" pagination="true" pagination-clickable="true" navigation="true"
                        loop="true" autoplay-delay="5000" autoplay-disable-on-interaction="false" slides-per-view="auto"
                        breakpoints='{
                            "320": {"slidesPerView": 1.2, "spaceBetween": 15},
                            "480": {"slidesPerView": 2.2, "spaceBetween": 15},
                            "768": {"slidesPerView": 3.2, "spaceBetween": 20},
                            "992": {"slidesPerView": 4.2, "spaceBetween": 20},
                            "1200": {"slidesPerView": 5.2, "spaceBetween": 20}
                        }'>
                        <?php foreach ($featuredBooks as $book):
                            $book['altText'] = 'Cover of ' . htmlspecialchars($book['title']);
                        ?>
                            <swiper-slide>
                                <?php echo renderBookCard($book); ?>
                            </swiper-slide>
                        <?php endforeach; ?>
                    </swiper-container>
                </div>


            <?php endif; ?>
        </div>
    </section>

    <section class="px-4">
        <div class="container">
            <div class="row text-center text-md-start bg-primary-light rounded-4 p-5">
                <div class="col-12 col-md-8 mb-4">
                    <h1 data-i18n="library.quiz_title">Ready to test your memory?</h1>
                    <p data-i18n="library.quiz_text">Take a quick quiz on the book and earn some shiny rewards!</p>
                    <a href="?page=quiz" class="btn btn-primary" data-i18n="library.btn_start_quiz">Start Quiz</a>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-center justify-content-center">
                    <img class="img-fluid rounded-circle" src="assets/images/jaredd-craig-croped.jpg"
                        alt="Quiz challenge" style="max-width: 200px;" />
                </div>
            </div>
        </div>
    </section>
</div>