<?php
include_once 'core/layout/book-card.php';
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

?>
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
    tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">
                    Session Debug
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php $session = getSessionInfo(); ?>
                <?php foreach ($session as $key => $value) : ?>
                <p><strong><?php echo htmlspecialchars((string)$key); ?>:</strong>
                    <?php echo htmlspecialchars((string)$value); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<a class="btn btn-primary position-fixed bottom-0 end-0 m-3" style="z-index: 9999;" data-bs-toggle="modal"
    href="#exampleModalToggle" role="button">Session</a>


<section class="mt-5 p-2 p-md-5">
    <div class="container-fluid">
        <div class="row my-5">
            <div class="text-end">
                <form action="./" method="POST" class="d-inline">
                    <input type="hidden" name="action" value="logout">
                    <input type="hidden" name="csrf_token" value="<?php  // echo htmlspecialchars(generateCsrfToken()); 
                                                                    ?>">
                    <button type="submit" class="btn btn-light rounded-circle">
                        <i class="bi bi-door-open"></i>
                    </button>
                </form>
            </div>
            <h1>Account Settings</h1>

        </div>
        <div class="row my-5">
            <!-- Profile Cards -->
            <div class="row g-3 align-items-stretch">
                <!-- Parent Card -->
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="rounded-4 p-4 shadow-sm h-100" style="background-color:#e0e7ff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span
                                    class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-person fs-4"></i>
                                </span>
                                <div>
                                    <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                    </h5>
                                </div>
                            </div>
                            <h6 class="rounded-pill text-bg-primary px-3 py-2">
                                <?php echo htmlspecialchars($_SESSION['role']); ?>
                            </h6>
                        </div>
                        <p class="mb-0 mt-3">PassKey #22890</p>
                    </div>
                </div>

                <!-- Child Card -->
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="rounded-4 p-4 shadow-sm h-100" style="background-color:#ffedd5;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="rounded-circle d-flex align-items-center justify-content-center text-white"
                                    style="background-color:#fb923c; width: 50px; height: 50px;">
                                    <i class="bi bi-person fs-4"></i>
                                </span>
                                <div>
                                    <h5 class="mb-0 fw-bold">Sara Sophia</h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="rounded-pill text-bg-secondary px-3 py-2">
                                    Child</h6>
                            </div>
                            <button class="btn btn-light rounded-circle p-2 pt-1" style="width:40px; height:40px;">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </div>
                        <p class="mb-0 mt-3">Code #89900</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="underline-tabs mb-5">
            <ul class="nav nav-tabs mb-4 border-bottom" id="underlineTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active border-0 text-primary" id="personal-tab" data-bs-toggle="tab"
                        data-bs-target="#personal" type="button" role="tab">
                        Personal Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 text-muted" id="profile-tab" data-bs-toggle="tab"
                        data-bs-target="#profile" type="button" role="tab">
                        Child Profiles
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 text-muted" id="security-tab" data-bs-toggle="tab"
                        data-bs-target="#security" type="button" role="tab">
                        Password & Security
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 text-muted" id="favorites-tab" data-bs-toggle="tab"
                        data-bs-target="#favorites" type="button" role="tab">
                        Favorites
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="underlineTabsContent">
                <!-- Personal Information Tab -->
                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <form class="form-container">
                            <h3 class="mb-4 fw-bold">Personal Information</h3>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="First Name" />
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Last Name" />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email Address" />
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Phone Number" />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-4">Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Child Profiles Tab -->
                <div class="tab-pane fade" id="profile" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <form class="form-container">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0 fw-bold">Child Profiles</h3>
                                <button type="button" class="btn btn-success d-flex align-items-center gap-2 px-3">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Add Child</span>
                                </button>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Name" />
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Last Name" />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email Address" />
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Phone Number" />
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-4">Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password & Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <form class="form-container">
                            <h3 class="mb-4 fw-bold">Password & Security</h3>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email" />
                                </div>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" placeholder="New Password" />
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" placeholder="Phone Number" />
                                </div>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" placeholder="Confirm Password" />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <input type="password" class="form-control" placeholder="Old Password" />
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-light rounded-4">Forget
                                    Password !</button>
                                <button type="submit" class="btn btn-primary rounded-4">Save
                                    Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Favorites Tab -->
                <div class="tab-pane fade" id="favorites" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0 fw-bold">
                                <i class="bi bi-heart-fill me-2"></i>
                                Favorites
                            </h3>
                            <button type="button" class="btn btn-light shadow d-flex align-items-center gap-2 px-3">
                                <i class="bi bi-trash text-danger"></i>
                                <span class="text-danger">Clear All</span>
                            </button>
                        </div>

                        <!-- Books Grid -->
                        <div class="row g-4">
                            <!-- Book 1 -->

                            <?php
                            foreach ($books as $book) {
                                echo '<div class="col-lg-3 col-md-4 col-sm-6">';
                                echo renderBookCard($book);
                                echo '</div>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>