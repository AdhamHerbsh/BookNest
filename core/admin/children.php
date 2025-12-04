<?php
// children.php - Parent-specific children management
require_once 'core/db/config.php';
include_once 'core/layout/book-card.php';

// Accept user_id from URL for admin viewing specific parent's children
$viewingUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

/**
 * Fetch children for a specific user or all children with parent info
 * @param int|null $userId Optional user ID to filter by
 * @return array Array of child records
 */
function fetchChildren($userId = null)
{
    $pdo = getDatabaseConnection();

    try {
        $sql = "SELECT c.ID, c.CODE, c.NAME, c.DOB, c.AGE, c.AVATER, c.CREADTED_DATE, 
                       u.ID as PARENT_ID, u.USERNAME as PARENT_USERNAME, u.FIRST_NAME as PARENT_FIRST_NAME, 
                       r.NAME as ROLE_NAME
                FROM children c
                LEFT JOIN users u ON c.USER_ID = u.ID
                LEFT JOIN roles r ON c.ROLE_ID = r.ID";

        $params = [];

        if ($userId) {
            $sql .= " WHERE c.USER_ID = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY c.CREADTED_DATE DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching children: " . $e->getMessage());
        return [];
    }
}

/**
 * Fetch user details for display
 * @param int $userId User ID
 * @return array|null User data or null
 */
function fetchUserDetails($userId)
{
    $pdo = getDatabaseConnection();
    try {
        $stmt = $pdo->prepare("SELECT ID, USERNAME, FIRST_NAME, LAST_NAME, PASSKEY FROM users WHERE ID = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching user: " . $e->getMessage());
        return null;
    }
}

// Fetch roles for modal dropdown
function fetchRoles()
{
    $pdo = getDatabaseConnection();
    try {
        return $pdo->query("SELECT ID, NAME FROM roles ORDER BY NAME")->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching roles: " . $e->getMessage());
        return [];
    }
}

$children = fetchChildren($viewingUserId);
$roles = fetchRoles();
$userDetails = $viewingUserId ? fetchUserDetails($viewingUserId) : null;
$isAdminView = $viewingUserId !== null;

// Determine current user's role
$currentUserRole = $_SESSION['role'] ?? 'GUEST';
$canManageAllChildren = $currentUserRole === 'ADMIN';
?>
<section class="mt-5">
    <div class="my-5 py-5 px-4 bg-white">
        <div class="row align-items-center">
            <div class="col-8">
                <h1>
                    <?php if ($isAdminView && $userDetails): ?>
                        Children of <span class="text-primary"><?php echo htmlspecialchars($userDetails['FIRST_NAME'] . ' ' . $userDetails['LAST_NAME']); ?></span>
                    <?php else: ?>
                        Children Management
                    <?php endif; ?>
                </h1>
                <?php if ($isAdminView && $userDetails): ?>
                    <p class="text-muted mb-0">Parent: <?php echo htmlspecialchars($userDetails['USERNAME']); ?> | PassKey: #<?php echo htmlspecialchars($userDetails['PASSKEY'] ?? 'N/A'); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-4 text-end">
                <div class="btn-group" role="group">
                    <?php if ($isAdminView): ?>
                        <a href="?admin=users" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-arrow-left"></i> Back to Users
                        </a>
                    <?php endif; ?>
                    <?php if (!$isAdminView || $canManageAllChildren): ?>
                        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addChildModal">
                            <i class="bi bi-plus-lg"></i> Add New Child
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-borderless table-primary align-middle">
                    <thead>
                        <caption>
                            <?php if ($isAdminView && $userDetails): ?>
                                Children of <?php echo htmlspecialchars($userDetails['USERNAME']); ?> - Total: <?php echo count($children); ?> records
                            <?php else: ?>
                                All Children - Total: <?php echo count($children); ?> records
                            <?php endif; ?>
                        </caption>
                        <tr class="table-primary">
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>DOB / Age</th>
                            <th>Login Code</th>
                            <?php if (!$isAdminView): ?>
                                <th>Parent</th>
                            <?php endif; ?>
                            <th>Role</th>
                            <th>Date Added</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (empty($children)): ?>
                            <tr class="table-secondary">
                                <td colspan="<?php echo $isAdminView ? '8' : '9'; ?>" class="text-center py-4">
                                    <?php if ($isAdminView): ?>
                                        No children found for this parent.
                                    <?php else: ?>
                                        No children records found in the database.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($children as $child): ?>
                                <tr class="table-secondary" data-child-id="<?php echo htmlspecialchars($child['ID']); ?>">
                                    <td scope="row"><?php echo htmlspecialchars($child['ID']); ?></td>
                                    <td>
                                        <?php if ($child['AVATER']): ?>
                                            <img src="<?php echo htmlspecialchars($child['AVATER']); ?>"
                                                alt="Avatar"
                                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;"
                                                onerror="this.src='https://placehold.co/40x40/f97316/ffffff?text=C';">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; background: #f97316; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                C
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($child['NAME']); ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($child['DOB'])); ?>
                                        <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars($child['AGE']); ?> yrs</span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm" style="max-width: 180px;">
                                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($child['CODE']); ?>" readonly>
                                            <button class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo htmlspecialchars($child['CODE']); ?>')" title="Copy code">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <?php if (!$isAdminView): ?>
                                        <td>
                                            <?php if ($child['PARENT_USERNAME']): ?>
                                                <a href="?page=children&user_id=<?php echo htmlspecialchars($child['PARENT_ID']); ?>"
                                                    class="text-primary text-decoration-none">
                                                    <?php echo htmlspecialchars($child['PARENT_USERNAME']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td><?php echo htmlspecialchars($child['ROLE_NAME'] ?: 'CHILD'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($child['CREADTED_DATE'])); ?></td>
                                    <td scope="row">
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-warning btn-sm py-2 px-3 m-1 rounded-2"
                                                onclick="openEditChildModal(<?php echo htmlspecialchars($child['ID']); ?>)"
                                                title="Edit child">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm py-2 px-3 m-1 rounded-2"
                                                onclick="showDeleteChildModal(<?php echo htmlspecialchars($child['ID']); ?>, '<?php echo htmlspecialchars(addslashes($child['NAME'])); ?>')"
                                                title="Delete child">
                                                <i class="bi bi-backspace"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo $isAdminView ? '8' : '9'; ?>" class="small p-3">
                                Last updated: <?php echo date('Y-m-d H:i:s'); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Add Child Modal -->
<div class="modal fade show" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChildModalLabel">
                    <?php if ($isAdminView && $userDetails): ?>
                        Add Child for <?php echo htmlspecialchars($userDetails['FIRST_NAME'] . ' ' . $userDetails['LAST_NAME']); ?>
                    <?php else: ?>
                        Add New Child
                    <?php endif; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addChildForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="childName" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="childName" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="childCode" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="childCode" name="code" required readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="childDob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="childDob" name="dob" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="childAge" class="form-label">Age</label>
                            <input type="text" class="form-control" id="childAge" name="age" readonly placeholder="Auto-calculated">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="childRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="childRole" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['ID']); ?>" <?php echo ($role['NAME'] === 'CHILD') ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['NAME']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="childAvatar" class="form-label">Avatar URL (Optional)</label>
                        <input type="url" class="form-control" id="childAvatar" name="avater" placeholder="https://example.com/avatar.jpg">
                    </div>
                    <?php if ($isAdminView && $userDetails): ?>
                        <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($userDetails['ID']); ?>">
                    <?php endif; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAddChild()">Save Child</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal scripts and functionality -->
<script>
    // Auto-generate child code and calculate age
    document.addEventListener('DOMContentLoaded', function() {
        const dobInput = document.getElementById('childDob');
        const ageInput = document.getElementById('childAge');
        const codeInput = document.getElementById('childCode');
        const isAdminView = <?php echo $isAdminView ? 'true' : 'false'; ?>;
        const viewingUserId = <?php echo $viewingUserId ?? 'null'; ?>;

        // Generate initial code
        if (codeInput && !codeInput.value) {
            const parentId = viewingUserId || <?php echo $_SESSION['user_id'] ?? '0'; ?>;
            const timestamp = Date.now().toString(36).toUpperCase();
            const random = Math.random().toString(36).substr(2, 4).toUpperCase();
            codeInput.value = `CHILD-${parentId}-${random}-${timestamp}`;
        }

        // Calculate age on DOB change
        if (dobInput && ageInput) {
            dobInput.addEventListener('change', function() {
                if (!this.value) {
                    ageInput.value = '';
                    return;
                }
                const dob = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                ageInput.value = age + ' years';
            });
        }
    });
</script>