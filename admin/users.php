<?php
session_start();if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        // Retrieve form data
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        // Validate form data
        $errors = [];
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }
        if (empty($role)) {
            $errors[] = "Role is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // If no errors, proceed to insert the user
        if (empty($errors)) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            try {
                $pdo->beginTransaction();

                // Insert into users table
                $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $hashed_password,
                    ':role' => $role
                ]);
                $user_id = $pdo->lastInsertId();

                // Insert into user_profiles table
                $query = "INSERT INTO user_profiles (user_id, full_name, email, phone, address) 
                          VALUES (:user_id, :full_name, :email, :phone, :address)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':full_name' => $full_name,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':address' => $address,
                ]);

                $pdo->commit();

                // Set success message
                $_SESSION['message'] = "User added successfully!";
                header('Location: users.php');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $errors[] = "Database error: " . $e->getMessage();
            }
        }

        // If there are errors, set them in session and redirect back
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: user_management.php');
            exit();
        }
    }
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


<!DOCTYPE html>
<html lang="en">
<?php
require_once 'includes/head.php';
require_once 'includes/header.php';
?>
<body class="with-welcome-text">
<div class="container-scroller">
    <!-- partial:../../partials/_navbar.html -->
    <?php require_once 'includes/navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:../../partials/_sidebar.html -->
        <?php require_once 'includes/partial-bar.php'; ?>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">User Management</h4>
                                <p class="card-description">Manage your users.</p>

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
                <!-- <option value="admin">Admin</option> -->
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
    <!-- it be mainly  copy and paste -->

                                <!-- Sales Table -->
                                <div class="table-responsive">
                                    <table class="table table-light table-hover" id="usersTable">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <!-- Username & Password -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="staff" selected>Staff</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name">
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    
                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    
                    <!-- Address -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address"></textarea>
                    </div>
                    
                    <!-- Avatar Upload -->
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <input type="file" class="form-control" id="avatar" name="avatar">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Save User</button>
            </div>
        </div>
    </div>
</div>

            <!-- content-wrapper ends -->
            <!-- partial:../../partials/_footer.html -->
            <?php require_once 'includes/footer.php';
              require_once 'includes/main.php';
              ?>
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->


<!-- Scripts -->
<script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
<script src="../../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="../../assets/js/off-canvas.js"></script>
<script src="../../assets/js/template.js"></script>
<script src="../../assets/js/settings.js"></script>
<script src="../../assets/js/hoverable-collapse.js"></script>
<script src="../../assets/js/todolist.js"></script>
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

document.getElementById('addUserForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('users.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User added successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>

</body>
</html>