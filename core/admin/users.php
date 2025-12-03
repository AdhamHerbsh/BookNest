<?php
// users.php - Database-driven with Edit/Delete functionality
require_once 'core/db/config.php';

/**
 * Fetch all users with their roles
 * 
 * @return array Array of user records with role names
 */
function fetchAllUsers()
{
    $pdo = getDatabaseConnection();

    try {
        $stmt = $pdo->query("SELECT u.ID, u.FIRST_NAME, u.LAST_NAME, u.USERNAME, u.PHONE, 
                             u.IS_SUBSCRIBED, u.ROLE_ID, r.NAME as ROLE_NAME, u.CREATED_DATE
                             FROM users u
                             LEFT JOIN roles r ON u.ROLE_ID = r.ID
                             ORDER BY u.CREATED_DATE DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching users: " . $e->getMessage());
        return [];
    }
}

$users = fetchAllUsers();
?>
<section class="mt-5">
    <div class="my-5 py-5 px-4 bg-white">
        <div class="row">
            <div class="col-8">
                <h1>Users Management</h1>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-borderless table-primary align-middle">
                    <thead>
                        <caption>
                            Users - Total: <?php echo count($users); ?> records
                        </caption>
                        <tr class="table-primary">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Subscribed</th>
                            <th>Date Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php if (empty($users)): ?>
                            <tr class="table-secondary">
                                <td colspan="8" class="text-center py-4">No users found in the database.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr class="table-secondary" data-user-id="<?php echo htmlspecialchars($user['ID']); ?>">
                                    <td scope="row"><?php echo htmlspecialchars($user['ID']); ?></td>
                                    <td><?php echo htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?></td>
                                    <td><?php echo htmlspecialchars($user['USERNAME']); ?></td>
                                    <td><?php echo htmlspecialchars($user['PHONE'] ?: 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($user['ROLE_NAME'] ?: 'N/A'); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo $user['IS_SUBSCRIBED'] === 'Y' ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $user['IS_SUBSCRIBED'] === 'Y' ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['CREATED_DATE'])); ?></td>
                                    <td scope="row">
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-warning btn-sm py-2 px-3 m-1 rounded-2"
                                                onclick="openEditModal(<?php echo htmlspecialchars($user['ID']); ?>)"
                                                title="Edit user">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm py-2 px-3 m-1 rounded-2"
                                                onclick="deleteUser(<?php echo htmlspecialchars($user['ID']); ?>)"
                                                title="Delete user">
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
                            <td colspan="8" class="small p-3">
                                Last updated: <?php echo date('Y-m-d H:i:s'); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="editPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role_id">
                            <option value="">Select Role</option>
                            <?php
                            // Fetch roles for dropdown
                            try {
                                $pdo = getDatabaseConnection();
                                $roles = $pdo->query("SELECT ID, NAME FROM roles ORDER BY NAME")->fetchAll();
                                foreach ($roles as $role) {
                                    echo '<option value="' . htmlspecialchars($role['ID']) . '">' . htmlspecialchars($role['NAME']) . '</option>';
                                }
                            } catch (PDOException $e) {
                                error_log("Error fetching roles: " . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="editSubscribed" name="is_subscribed">
                        <label class="form-check-label" for="editSubscribed">Subscribed</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save Changes</button>
            </div>
        </div>
    </div>
</div>