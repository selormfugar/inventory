<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
require_once 'includes/header.php';
require_once 'includes/functions.php';


// Get filter values
$filter_username = isset($_POST['filter_username']) ? $_POST['filter_username'] : '';
$filter_email = isset($_POST['filter_email']) ? $_POST['filter_email'] : '';

// Fetch users along with their profiles
$query = "
    SELECT u.id, u.username, u.role, up.email ,up.*
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
                                <p class="card-description">Manage your staff.</p>

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
    data-role="<?php echo $user['role']; ?>"
    data-full_name="<?php echo $user['full_name']; ?>"
    data-phone="<?php echo $user['phone']; ?>"
    data-address="<?php echo $user['address']; ?>">
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
            </div>
            
            <!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                <div id="formError"></div> <!-- Error messages appear here -->

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
                    <!-- <div class="mb-3">
                        <label for="avatar" class="form-label">Avatar</label>
                        <input type="file" class="form-control" id="avatar" name="avatar">
                    </div> -->
                </form>
            </div>
            <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">Close</button>
    <button type="submit" form="addUserForm" class="btn btn-primary" id="saveUserBtn">
    Save User
</button>

</div>

        </div>
    </div>
</div>
<!-- edit modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    <div id="editFormError"></div> <!-- Error messages appear here -->
                    
                    <!-- Hidden User ID -->
                    <input type="hidden" id="editUserId" name="user_id">

                    <!-- Username (Not Editable) -->
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" readonly>
                    </div>

                    <!-- Password (Optional) -->
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">New Password (leave blank to keep unchanged)</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label for="editRole" class="form-label">Role</label>
                        <select class="form-select" id="editRole" name="role">
                            <option value="staff">Staff</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="editFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editFullName" name="full_name">
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone">
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="editAddress" class="form-label">Address</label>
                        <textarea class="form-control" id="editAddress" name="address"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteUsername"></strong>?</p>
                <input type="hidden" id="deleteUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteUser">Delete</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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


$(document).ready(function () {
    $("#addUserForm").submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "add_user.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("Server Response:", response); // Debugging

                if (typeof response !== "object") {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error("Invalid JSON response", response);
                        alert("Unexpected response. Check console for details.");
                        return;
                    }
                }

                if (response.status === "success") {
                    alert(response.message);
                    $("#addUserForm")[0].reset();
                    $("#addUserModal").modal("hide");
                    location.reload();
                } else {
                    $("#formError").html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                alert("An error occurred. Check the console.");
            }
        });
    });
});


$(document).ready(function () {
    // EDIT USER
    $(".edit-user").click(function () {
        let userId = $(this).data("id");
        let username = $(this).data("username");
        let email = $(this).data("email");
        let role = $(this).data("role");
        let fullName = $(this).data("full_name");  // Get full name
        let phone = $(this).data("phone");        // Get phone number
        let address = $(this).data("address");    // Get address

        $("#editUserId").val(userId);
        $("#editUsername").val(username);
        $("#editEmail").val(email);
        $("#editRole").val(role);
        $("#editFullName").val(fullName);
        $("#editPhone").val(phone);
        $("#editAddress").val(address);
    });

    // Handle form submission
    $("#editUserForm").submit(function (e) {
        e.preventDefault();

        let formData = $(this).serialize(); // Collect form data

        $.ajax({
            url: "edit_user.php",
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.status === "success") {
                    alert("User updated successfully!");
                    $("#editUserModal").modal("hide");
                    location.reload(); // Refresh the page
                } else {
                    $("#editFormError").html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                $("#editFormError").html(`<div class="alert alert-danger">An error occurred. Please try again.</div>`);
            }
        });
    });
    // DELETE USER
    $(document).ready(function () {
    $(".delete-user").click(function () {
        let userId = $(this).data("id");
        let username = $(this).data("username");

        // Populate delete modal with user info
        $("#deleteUserId").val(userId);
        $("#deleteUsername").text(username); // Display username in modal for confirmation
    });

    // Handle delete user action
    $("#confirmDeleteUser").click(function () {
        let userId = $("#deleteUserId").val();

        $.ajax({
            url: "delete_user.php",
            type: "POST",
            data: { user_id: userId },
            success: function (response) {
                console.log("Delete Response:", response);
                if (response.status === "success") {
                    alert(response.message);
                    location.reload();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert("An error occurred. Check the console.");
            }
        });
    });
});


});

</script>

</body>
</html>