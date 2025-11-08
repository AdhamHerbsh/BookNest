<div class="hero-image library" data-aos="fade"></div>
<div class="container-fluid">
    <div class="hero-content container text-center">
        <h1 class="display-1 fw-blod text-white" data-aos="fade-up" data-aos-delay="200">Unlock a World of Stories</h1>
        <p class="fs-4 mb4 text-white p-5" data-aos="fade-up" data-aos-delay="500">
            Ignite your child's imagination with our interactive digital library. Featuring audio narration,
            animations, and progress tracking, BookNest makes reading fun and engaging for kids aged 4-12.
        </p>
        <button class="btn btn-primary" data-aos="zoom-in" data-aos-delay="1000">Start Exploring</button>
    </div>
    <section class="px-5">
        <div class="mb-4 text-center">
            <h1 class="display-2">Explore by Age</h1>

        </div>
        <div class="container">
            <form class="mb-3 w-100">
                <div class="container">
                    <div class="row d-flex justify-content-center mb-4 g-2">
                        <div class="col-4 col-md-2">
                            <button type="button" class="btn btn-outline-primary rounded-5">4-6 yrs</button>
                        </div>
                        <div class="col-4 col-md-2">
                            <button type="button" class="btn btn-outline-primary rounded-5">7-9 yrs</button>
                        </div>
                        <div class="col-4 col-md-2">
                            <button type="button" class="btn btn-outline-primary rounded-5">10-12 yrs</button>
                        </div>

                    </div>
                    <div class="input-group mx-auto flex-nowrap">
                        <input type="text" class="form-control-lg w-100 border-0" placeholder="Search"
                            aria-label="Search" />
                        <span class="input-group-text bg-white border-0" id="basic-addon1">
                            <i class="bi bi-search"></i> </span>
                    </div>
                </div>
            </form>

        </div>

    </section>
    <section class="featured-collections">
        <div class="container-fluid px-4">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="display-4 fw-bold">Featured Collections</h1>
                </div>
            </div>

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
                    <?php include_once 'core/layout/book-card.php'; ?>
                    <?php
                    $books = [
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-1.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-2.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-3.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-4.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-5.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-6.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        // Add more books as needed
                    ];

                    foreach ($books as $book) {
                        echo '<swiper-slide>';
                        echo renderBookCard($book);
                        echo '</swiper-slide>';
                    }
                    ?>
                </swiper-container>
            </div>

            <div class="row">
                <swiper-container class="book-slider" pagination="true" pagination-clickable="true" navigation="true"
                    loop="true" autoplay-delay="2500" autoplay-disable-on-interaction="false" slides-per-view="auto"
                    breakpoints='{
                "320": {"slidesPerView": 1.2, "spaceBetween": 15},
                "480": {"slidesPerView": 2.2, "spaceBetween": 15},
                "768": {"slidesPerView": 3.2, "spaceBetween": 20},
                "992": {"slidesPerView": 4.2, "spaceBetween": 20},
                "1200": {"slidesPerView": 5.2, "spaceBetween": 20}
            }'>
                    <?php include_once 'core/layout/book-card.php'; ?>
                    <?php
                    $books = [
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-1.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-2.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-3.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-4.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-5.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        [
                            'title' => 'Adventures in Wonderland',
                            'description' => 'Dive into a world of whimsy and wonder with our curated collection of adventure stories.',
                            'coverImage' => 'assets/images/books/library-book-6.png',
                            'altText' => 'Adventures in Wonderland cover'
                        ],
                        // Add more books as needed
                    ];

                    foreach ($books as $book) {
                        echo '<swiper-slide>';
                        echo renderBookCard($book);
                        echo '</swiper-slide>';
                    }
                    ?>
                </swiper-container>
            </div>


        </div>
    </section>
    <section class="px-4">
        <div class="contanier">
            <div class="row text-center text-md-start bg-primary-light rounded-4 p-5">
                <div class="col-12 col-md-8 mb-4">
                    <h1>Ready to test your memory ?</h1>
                    <p>Take a quick quiz on the book and earn some shiny rewards!</p>
                    <a href="?page=quiz" class="btn btn-primary">Start Quiz ??..</a>
                </div>
                <div class="col-12 col-md-2">
                    <img class="img-fluid rounded-circle" src="assets/images/jaredd-craig-croped.jpg"
                        alt="image not found" />
                </div>
            </div>

        </div>
    </section>
</div>