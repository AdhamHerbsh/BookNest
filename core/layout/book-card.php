<?php
// book-card.php - Enhanced version
function renderBookCard($book)
{
    // Securely extract values with fallbacks
    $id = htmlspecialchars($book['id'] ?? uniqid('book_'));
    $title = htmlspecialchars($book['title'] ?? 'Book Title');
    $description = htmlspecialchars($book['description'] ?? 'Book description here...');
    $coverImage = htmlspecialchars($book['coverImage'] ?? 'assets/images/books/library-book-1.png');
    $altText = htmlspecialchars($book['altText'] ?? 'Book cover');

    // Generate URLs
    $readUrl = "?page=book&id={$id}";
    $detailsUrl = "?page=book&id={$id}";

    return <<<HTML
    <!-- Book Card -->
    <div class="card bg-transparent h-100 rounded-4 hover border-0">
        <div class="position-relative">
            <button type="button"
                class="btn-favorite btn btn-light rounded-circle shadow-sm position-absolute top-0 end-0 m-2"
                data-book-id="{$id}"
                aria-pressed="false"
                aria-label="Add {$title} to favorites"
                title="Add to favorites">
                <i class="bi bi-heart"></i>
            </button>
        </div>
        <img src="{$coverImage}" 
             class="card-img-top rounded-4 p-2" 
             alt="{$altText}"
             loading="lazy"
             onerror="this.src='assets/images/books/fallback-cover.png'" />
        <div class="card-body">
            <h5 class="card-title">{$title}</h5>
            <p class="card-text">{$description}</p>
            <div class="card-overlay rounded-4">
                <a href="{$readUrl}" class="btn btn-primary mb-4">Read</a>
                <a href="{$detailsUrl}" class="text-white">View Details</a>
            </div>
        </div>
    </div>
    <!-- End Book Card -->
HTML;
}
