<?php
// books.php - Database-driven admin table (Read-only)
require_once 'core/db/config.php';

/**
 * Fetch all books for admin display
 * 
 * @return array Array of book records
 */
function fetchAllBooks()
{
    $pdo = getDatabaseConnection();

    try {
        $stmt = $pdo->query("SELECT ID, TITLE, AUTHOR, AGE_GROUP, LANGUAGE, COVER, IS_ACTIVE, CREATED_DATE 
                             FROM books 
                             ORDER BY CREATED_DATE DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching books: " . $e->getMessage());
        return [];
    }
}

$books = fetchAllBooks();
?>
<section class="mt-5">
    <div class="my-5 py-5 px-4 bg-white">
        <div class="row">
            <div class="col-8">
                <h1>Admin - Books Table</h1>
            </div>
            <!-- <div class="col-4 text-end">
                <a href="?page=upload" class="btn btn-success m-1"><i class="bi bi-cloud-upload me-1"></i>Upload Books</a>
                <a href="?page=create-quiz" class="btn btn-success m-1"><i class="bi bi-file-plus me-1"></i>Create Quiz</a>
            </div> -->
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-borderless table-primary align-middle">
                    <thead>
                        <caption>
                            Books Table - Total: <?php echo count($books); ?> books
                        </caption>
                        <tr class="table-primary">
                            <th>ID</th>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Age Group</th>
                            <th>Language</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (empty($books)): ?>
                            <tr class="table-secondary">
                                <td colspan="9" class="text-center py-4">No books found in the database.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <tr class="table-secondary">
                                    <td scope="row"><?php echo htmlspecialchars($book['ID']); ?></td>
                                    <td>
                                        <?php if ($book['COVER']): ?>
                                            <img src="<?php echo htmlspecialchars($book['COVER']); ?>"
                                                alt="Cover"
                                                style="width: 50px; height: 75px; object-fit: cover; border-radius: 4px;"
                                                loading="lazy"
                                                onerror="this.src='assets/images/books/fallback-cover.png'">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 75px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px;">No Cover</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($book['TITLE']); ?></td>
                                    <td><?php echo htmlspecialchars($book['AUTHOR']); ?></td>
                                    <td><?php echo htmlspecialchars($book['AGE_GROUP']); ?></td>
                                    <td><?php echo htmlspecialchars($book['LANGUAGE'] ?: 'English'); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo $book['IS_ACTIVE'] === 'Y' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $book['IS_ACTIVE'] === 'Y' ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($book['CREATED_DATE'])); ?></td>
                                    <td>
                                        <a href="?page=book&id=<?php echo htmlspecialchars($book['ID']); ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="small p-3">
                                Last updated: <?php echo date('Y-m-d H:i:s'); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>