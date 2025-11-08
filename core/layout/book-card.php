<?php
function renderBookCard($book)
{
    $id = $book['id'] ?? uniqid();
    $title = $book['title'] ?? 'Book Title';
    $description = $book['description'] ?? 'Book description here...';
    $coverImage = $book['coverImage'] ?? 'assets/images/books/library-book-1.png';
    $altText = $book['altText'] ?? 'Book cover';

    return <<<HTML
  <!-- Book -->
    <div class="card bg-transparent h-100 rounded-4 hover border-0">
        <div class="position-relative">
                <button type="button"
                    class="btn-favorite btn btn-light rounded-circle shadow-sm position-absolute top-0 end-0 m-2"
                    data-book-id="{$id}"
                    aria-pressed="false"
                    aria-label="Add to favorites">
                    <i class="bi bi-heart"></i>
                </button>
        </div>
                <img src="{$coverImage}" 
                    class="card-img-top rounded-4 p-2" 
                    alt="{$altText}"
                    loading="lazy" />
                            <div class="card-body">
            <h5 class="card-title">{$title}</h5>
            <p class="card-text">{$description}</p>
            <div class="card-overlay rounded-4">
                <a href="?page=book" class="btn btn-primary mb-4">Read</a>
                <a href="?page=book" class="text-white">View Details</a>
            </div>
        </div>
    </div>
    <!-- End Book -->
HTML;
}
