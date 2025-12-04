<?php
if (!isEducator()) {
    header("Location: ?page=landing");
    exit;
}
?>

<section class="mt-5">
    <div class="my-5 py-5 px-4 bg-white">
        <div class="row">
            <div class="col-8">
                <h1 data-i18n="books_manage.page_title">Manage Books</h1>
            </div>
            <div class="col-4 text-end">
                <a href="?page=upload" class="btn btn-success">
                    <i class="bi bi-cloud-upload me-1"></i><span data-i18n="books_manage.btn_upload_book">Upload Book</span>
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-borderless table-primary align-middle">
                    <thead>
                        <caption data-i18n="books_manage.table_caption">Uploaded Books</caption>
                        <tr class="table-primary">
                            <th data-i18n="books_manage.th_cover">Cover</th>
                            <th data-i18n="books_manage.th_title">Title</th>
                            <th data-i18n="books_manage.th_author">Author</th>
                            <th data-i18n="books_manage.th_language">Language</th>
                            <th data-i18n="books_manage.th_age_group">Age Group</th>
                            <th data-i18n="books_manage.th_status">Status</th>
                            <th class="text-end" data-i18n="books_manage.th_actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php
                        require_once 'core/db/config.php';
                        $pdo = getDatabaseConnection();
                        $stmt = $pdo->query("SELECT * FROM books ORDER BY CREATED_DATE DESC");
                        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($books) > 0) {
                            foreach ($books as $book) {
                                $statusBadge = $book['IS_ACTIVE'] === 'Y'
                                    ? '<span class="badge bg-success">Active</span>'
                                    : '<span class="badge bg-secondary">Inactive</span>';

                                $coverImage = $book['COVER']
                                    ? htmlspecialchars($book['COVER'])
                                    : 'assets/images/books/library-book-1.png';

                                echo '<tr class="table-secondary">';
                                echo '<td><img src="' . $coverImage . '" alt="Book cover" style="width: 60px; height: 80px; object-fit: cover;" class="rounded"></td>';
                                echo '<td><strong>' . htmlspecialchars($book['TITLE']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($book['AUTHOR']) . '</td>';
                                echo '<td>' . htmlspecialchars($book['LANGUAGE']) . '</td>';
                                echo '<td>' . htmlspecialchars($book['AGE_GROUP']) . '</td>';
                                echo '<td>' . $statusBadge . '</td>';
                                echo '
                                <td scope="row">
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-warning btn-sm py-2 px-3 m-1 rounded-2"
                                                onclick="editBook(' . $book['ID']  . ')"
                                                title="Edit user">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm py-2 px-3 m-1 rounded-2"
                                                onclick="deleteBook(' . $book['ID'] . ', \'' . htmlspecialchars($book['TITLE'], ENT_QUOTES) . '\')"
                                                title="Delete user">
                                                <i class="bi bi-backspace"></i>
                                            </button>
                                        </div>
                                    </td>
                                ';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="mt-3 text-muted" data-i18n="books_manage.no_books_message">No books uploaded yet. Click "Upload Book" to get started!</p>
                            </td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBookModalLabel" data-i18n="books_manage.modal_edit_title">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBookForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_book_id" name="id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_title" class="form-label" data-i18n="books_manage.label_title">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_author" class="form-label" data-i18n="books_manage.label_author">Author</label>
                            <input type="text" class="form-control" id="edit_author" name="author" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_language" class="form-label" data-i18n="books_manage.label_language">Language</label>
                            <select class="form-select" id="edit_language" name="language" required>
                                <option value="English">English</option>
                                <option value="Spanish">Spanish</option>
                                <option value="French">French</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_age_group" class="form-label" data-i18n="books_manage.label_age_group">Age Group</label>
                            <select class="form-select" id="edit_age_group" name="age_group" required>
                                <option value="4-6">4-6 years</option>
                                <option value="7-9">7-9 years</option>
                                <option value="10-12">10-12 years</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label" data-i18n="books_manage.label_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="isActive">
                        <label class="form-check-label" for="edit_is_active" data-i18n="books_manage.label_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="books_manage.btn_cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-i18n="books_manage.btn_save_changes">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>