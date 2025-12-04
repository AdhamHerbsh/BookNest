<?php
// Include database configuration
require_once 'core/db/config.php';
include_once 'core/layout/book-card.php';

// Fetch user's favorite books
if (isset($_SESSION['user_id'])) {

    try {
        $pdo = getDatabaseConnection();

        // Personal Info
        $stmt = $pdo->prepare("SELECT FIRST_NAME, LAST_NAME, USERNAME, PHONE FROM users u WHERE ID = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();


        // Favorites Books
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
$isParent = isset($_SESSION['role']) && $_SESSION['role'] === 'PARENT';
?>

<!-- Session Debug Modal -->
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel" data-i18n="account.session_debug_title">Session Debug</h5>
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

<a class="btn btn-primary position-fixed bottom-0 end-0 m-3" style="z-index: 9999;" data-bs-toggle="modal" href="#exampleModalToggle" role="button" data-i18n="account.btn_session">Session</a>

<section class="mt-5 p-2 p-md-5">
    <div class="container-fluid">
        <div class="row my-5">
            <div class="text-end">
                <form action="./" method="POST" class="d-inline">
                    <input type="hidden" name="action" value="logout">
                    <input type="hidden" name="csrf_token" value="<?php // echo htmlspecialchars(generateCsrfToken()); 
                                                                    ?>">
                    <button type="submit" class="btn btn-light rounded-circle">
                        <i class="bi bi-door-open"></i>
                    </button>
                </form>
            </div>
            <h1 data-i18n="account.page_title">Account Settings</h1>
        </div>

        <div class="row my-5">
            <!-- Profile Cards -->
            <div class="row g-3 align-items-stretch">
                <!-- Parent Card -->
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="rounded-4 p-4 shadow-sm h-100" style="background-color:#e0e7ff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person fs-4"></i>
                                </span>
                                <div>
                                    <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h5>
                                </div>
                            </div>
                            <h6 class="rounded-pill text-bg-primary px-3 py-2">
                                <?php echo htmlspecialchars($_SESSION['role'] ?? 'Guest'); ?>
                            </h6>
                        </div>
                        <?php if ($isParent): ?>
                            <p class="mb-0 mt-3"><span data-i18n="account.passkey_label">PassKey:</span> <strong id="parentPasskeyDisplay">...</strong></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Child Summary Cards (Dynamic) -->
                <div id="childSummaryCards" class="contents d-contents d-flex flex-wrap gap-3">
                    <!-- Will be populated by JS -->
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="underline-tabs mb-5">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4 border-bottom" id="underlineTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 text-primary" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                        <span data-i18n="account.tab_personal">Personal Information</span>
                    </button>
                </li>
                <?php if ($isParent): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-0 text-primary" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                            <span data-i18n="account.tab_profiles">Child Profiles</span>
                        </button>
                    </li>
                <?php endif; ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 text-primary" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                        <span data-i18n="account.tab_security">Password & Security</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 text-primary" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites" type="button" role="tab">
                        <span data-i18n="account.tab_favorites">Favorites</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="underlineTabsContent">
                <!-- Personal Information Tab -->
                <div class="tab-pane fade show" id="personal" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <form id="personalInfoForm" class="form-container">
                            <h3 class="mb-4 fw-bold" data-i18n="account.personal_title">Personal Information</h3>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="firstName" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($user['FIRST_NAME'] ?? ''); ?>" required data-i18n-placeholder="account.placeholder_firstname" />
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($user['LAST_NAME'] ?? ''); ?>" required data-i18n-placeholder="account.placeholder_lastname" />
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <input type="email" class="form-control" placeholder="Email Address" value="<?php echo htmlspecialchars($user['USERNAME'] ?? ''); ?>" readonly data-i18n-placeholder="account.placeholder_email" />
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($user['PHONE'] ?? ''); ?>" data-i18n-placeholder="account.placeholder_phone" />
                                </div>
                            </div>
                            <div class="alert alert-danger d-none" id="personalInfoError"></div>
                            <div class="alert alert-success d-none" id="personalInfoSuccess"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-4" data-i18n="account.btn_save_changes">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($isParent): ?>
                    <!-- Child Profiles Tab -->
                    <div class="tab-pane fade" id="profile" role="tabpanel">
                        <div class="bg-light p-4 rounded-4 my-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0 fw-bold" data-i18n="account.profiles_title">Child Profiles</h3>
                                <button type="button" class="btn btn-success d-flex align-items-center gap-2 px-3" data-bs-toggle="modal" data-bs-target="#addChildModal">
                                    <i class="bi bi-plus-lg"></i>
                                    <span data-i18n="account.btn_add_child">Add Child</span>
                                </button>
                            </div>

                            <!-- Children List Container -->
                            <div id="childrenListContainer" class="row g-3">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden" data-i18n="account.loading">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Password & Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="bg-light p-4 rounded-4 my-5">
                        <form id="passwordForm" class="form-container">
                            <h3 class="mb-4 fw-bold" data-i18n="account.security_title">Password & Security</h3>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <input type="password" class="form-control mb-3" id="newPassword" name="new_password" placeholder="New Password" required data-i18n-placeholder="account.placeholder_new_password" />
                                    <input type="password" class="form-control mb-3" id="confirmPassword" name="confirm_password" placeholder="Confirm Password" required data-i18n-placeholder="account.placeholder_confirm_password" />
                                    <input type="password" class="form-control mb-3" id="oldPassword" name="old_password" placeholder="Old Password" required data-i18n-placeholder="account.placeholder_old_password" />
                                </div>
                            </div>
                            <div class="alert alert-danger d-none" id="passwordError"></div>
                            <div class="alert alert-success d-none" id="passwordSuccess"></div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-4" data-i18n="account.btn_update_password">Update Password</button>
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
                                <span data-i18n="account.favorites_title">Favorites</span>
                            </h3>
                            <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-3">
                                <i class="bi bi-trash text-danger"></i>
                                <span class="text-danger" data-i18n="account.btn_clear_all">Clear All</span>
                            </button>
                        </div>

                        <!-- Books Grid -->
                        <div class="row g-4">
                            <?php if (empty($books)): ?>
                                <p class="text-center text-muted" data-i18n="account.no_favorites">No favorite books yet.</p>
                            <?php else: ?>
                                <?php foreach ($books as $book): ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <?php echo renderBookCard($book); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($isParent): ?>
    <!-- Add Child Modal -->
    <div class="modal fade" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addChildModalLabel" data-i18n="account.modal_add_child_title">Add New Child</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addChildForm">
                        <div class="mb-3">
                            <label for="childName" class="form-label" data-i18n="account.label_child_name">Child Name</label>
                            <input type="text" class="form-control" id="childName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="childDob" class="form-label" data-i18n="account.label_dob">Date of Birth</label>
                            <input type="date" class="form-control" id="childDob" name="dob" min="2013-01-01" max="2022-01-01" required>
                        </div>
                        <div class="alert alert-danger d-none" id="addChildError"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="account.btn_cancel">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitAddChild()" data-i18n="account.btn_add_child_submit">Add Child</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Child Modal -->
    <div class="modal fade" id="editChildModal" tabindex="-1" aria-labelledby="editChildModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editChildModalLabel" data-i18n="account.modal_edit_child_title">Edit Child Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editChildForm">
                        <input type="hidden" id="editChildId" name="id">
                        <div class="mb-3">
                            <label for="editChildName" class="form-label" data-i18n="account.label_child_name">Child Name</label>
                            <input type="text" class="form-control" id="editChildName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editChildDob" class="form-label" data-i18n="account.label_dob">Date of Birth</label>
                            <input type="date" class="form-control" id="editChildDob" name="dob" required>
                        </div>
                        <div class="alert alert-danger d-none" id="editChildError"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-i18n="account.btn_cancel">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitEditChild()" data-i18n="account.btn_save_changes_child">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>