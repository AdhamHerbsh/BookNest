<div class="container-fluid mt-5">
    <div class="d-flex" style="height: 100vh;">

        <!-- Sidebar -->
        <aside class="sidebar bg-white d-flex flex-column">
            <div class="row mb-3">
                <div class="col logo">
                    <img class="img-fluid" src="assets/images/BookNest Logo/Logo Icon Square RBG.png"
                        alt="Book Nest Logo" />
                </div>
                <div class="col align-content-center">
                    <h4 class="fw-bold text-primary">BookNest</h4>
                </div>
            </div>
            <div>
                <div class="pages-title">Pages</div>
                <div class="pages-list d-flex flex-column gap-2">
                    <div class="page-item active" tabindex="0" role="button" aria-current="true">
                        <img src="assets/images/books/library-book-1.png" alt="Page 1 thumbnail" />
                        <span>Page 1</span>
                    </div>
                    <div class="page-item inactive" tabindex="0" role="button">
                        <div class="page-preview"></div>
                        <span class="text-muted">Page 2</span>
                    </div>
                    <div class="page-item inactive" tabindex="0" role="button">
                        <div class="page-preview"></div>
                        <span class="text-muted">Page 3</span>
                    </div>
                    <div class="page-item inactive" tabindex="0" role="button">
                        <div class="page-preview"></div>
                        <span class="text-muted">Page 4</span>
                    </div>
                    <div class="page-item inactive" tabindex="0" role="button">
                        <div class="page-preview"></div>
                        <span class="text-muted">Page 5</span>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Main viewer -->
        <main class="main-content d-flex flex-column align-items-center justify-content-center">
            <div class="toolbar" role="toolbar" aria-label="PDF Toolbar">
                <button type="button" title="Zoom Out" aria-label="Zoom Out">
                    <i class="bi bi-zoom-out"></i>
                </button>
                <button type="button" title="Zoom In" aria-label="Zoom In">
                    <i class="bi bi-zoom-in"></i>
                </button>
                <button type="button" title="Fullscreen" aria-label="Fullscreen">
                    <i class="bi bi-fullscreen"></i>
                </button>
            </div>
            <div class="page-indicator" aria-live="polite" aria-atomic="true">
                Page 1 of 24
            </div>
            <div class="pdf-viewer position-relative" role="region" aria-label="PDF Page Viewer">
                <!-- Left Arrow -->
                <button class="nav-arrow left" aria-label="Previous Page" tabindex="0" type="button">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <!-- PDF Image -->
                <img class="img-fluid" src="assets/images/books/library-book-1.png" alt="PDF page 1"
                    draggable="false" />
                <!-- Right Arrow -->
                <button class="nav-arrow right" aria-label="Next Page" tabindex="0" type="button">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <button class="btn btn-success rounded-pill py-2 px-4 shadow" type="button" aria-label="Read Aloud">
                <i class="bi bi-volume-up-fill"></i>
                Read Aloud
            </button>
        </main>
    </div>


    <section class="vh-100 mt-5">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">
            Test Quiz Modal
        </button>

    </section>
    <!-- Modal -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 800px !important;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="p-4 shodow-sm rounded-4 bg-white">
                        <div class="row text-center mb-4">
                            <h2>Quiz Time!</h2>
                            <p>Let's see how much you remember from the story.</p>
                        </div>
                        <div class="row bg-light rounded-4 p-4">
                            <div class="col-4">
                                <img class="rounded-4" src="assets/images/books/library-book-1.png"
                                    alt="quiz cover image" />
                            </div>
                            <div class="col-8">
                                <h4>The Magical Treehouse Adventure</h4>
                                <p class="mb-4">By Sarah Miller</p>
                                <div class="d-flex gap-3 justify-content-center">
                                    <button class="rounded-pill px-5 btn btn-secondary">Start Quiz</button>
                                    <button class="rounded-pill px-5 btn btn-secondary-light">Later</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>

    </script>


</div>