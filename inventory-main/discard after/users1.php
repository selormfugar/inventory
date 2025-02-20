<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... existing code ...
}

// Get filter values
$filter_username = isset($_POST['filter_username']) ? $_POST['filter_username'] : '';
$filter_email = isset($_POST['filter_email']) ? $_POST['filter_email'] : '';

// Fetch users along with their profiles
$query = "
    SELECT u.id, u.username, u.role, up.email 
    FROM users u
    JOIN user_profiles up ON u.id = up.user_id
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php require_once 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Users</h2>

    <!-- Filter Form in a Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="filter-username" class="form-control" placeholder="Filter by Username" maxlength="50" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="email" id="filter-email" class="form-control" placeholder="Filter by Email" maxlength="100" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <select id="filter-role" class="form-select" onchange="filterTable()">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="staff">Staff</option>
            </select>
        </div>
    </div>

    <!-- Display alert if set -->
    <?php if (isset($alert)) echo $alert; ?>

    <!-- Add User Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Add New User
        </button>
    </div>

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-striped" id="usersTable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-user" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editUserModal"
                                data-id="<?php echo $user['id']; ?>"
                                data-username="<?php echo $user['username']; ?>"
                                data-email="<?php echo $user['email']; ?>"
                                data-role="<?php echo $user['role']; ?>">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-user"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteUserModal"
                                data-id="<?php echo $user['id']; ?>"
                                data-username="<?php echo $user['username']; ?>">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Include modals and scripts -->
    <!-- ... existing modal code ... -->

</div>

<script>
function filterTable() {
    const usernameFilter = document.getElementById('filter-username').value.toLowerCase();
    const emailFilter = document.getElementById('filter-email').value.toLowerCase();
    const roleFilter = document.getElementById('filter-role').value;

    const table = document.getElementById('usersTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const usernameCell = rows[i].getElementsByTagName('td')[0];
        const emailCell = rows[i].getElementsByTagName('td')[1];
        const roleCell = rows[i].getElementsByTagName('td')[2];

        const usernameText = usernameCell ? usernameCell.textContent.toLowerCase() : '';
        const emailText = emailCell ? emailCell.textContent.toLowerCase() : '';
        const roleText = roleCell ? roleCell.textContent.toLowerCase() : '';

        const matchesUsername = usernameText.includes(usernameFilter);
        const matchesEmail = emailText.includes(emailFilter);
        const matchesRole = roleFilter ? roleText.includes(roleFilter) : true;

        if (matchesUsername && matchesEmail && matchesRole) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>

<?php require_once 'includes/footer.php'; ?> 