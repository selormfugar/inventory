<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $stmt = $pdo->prepare("INSERT INTO suppliers (name, phone, email, address) 
                                         VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['name']),
                        sanitize_input($_POST['phone']),
                        sanitize_input($_POST['email']),
                        sanitize_input($_POST['address'])
                    ]);
                    $alert = display_alert("Supplier added successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error adding supplier: " . $e->getMessage(), "danger");
                }
                break;

            case 'edit':
                try {
                    $stmt = $pdo->prepare("UPDATE suppliers 
                                         SET name = ?, phone = ?, email = ?, 
                                             address = ? 
                                         WHERE id = ?");
                    $stmt->execute([
                        sanitize_input($_POST['name']),
                        sanitize_input($_POST['phone']),
                        sanitize_input($_POST['email']),
                        sanitize_input($_POST['address']),
                        (int)$_POST['id']
                    ]);
                    $alert = display_alert("Supplier updated successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error updating supplier: " . $e->getMessage(), "danger");
                }
                break;

            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
                    $stmt->execute([(int)$_POST['id']]);
                    $alert = display_alert("Supplier deleted successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error deleting supplier: " . $e->getMessage(), "danger");
                }
                break;
        }
    }
}

// Get filter values
$filter_name = isset($_POST['filter_name']) ? $_POST['filter_name'] : '';

// Fetch suppliers
$suppliers_query = "SELECT * FROM suppliers WHERE name LIKE ?";
$suppliers = $pdo->prepare($suppliers_query);
$suppliers->execute(["%$filter_name%"]);
$suppliers = $suppliers->fetchAll();
?>

<?php require_once 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Suppliers</h2>

    <!-- Filter Form in a Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="filter-name" class="form-control" placeholder="Filter by Name" maxlength="50" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="text" id="filter-phone" class="form-control" placeholder="Filter by Phone" maxlength="15" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="email" id="filter-email" class="form-control" placeholder="Filter by Email" maxlength="100" onkeyup="filterTable()">
        </div>
    </div>

    <!-- Suppliers Table -->
    <table class="table table-striped" id="suppliersTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $supplier): ?>
            <tr>
                <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                <td><?php echo htmlspecialchars($supplier['phone']); ?></td>
                <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                <td><?php echo htmlspecialchars($supplier['address']); ?></td>
                <td>
                    <button class="btn btn-sm btn-primary edit-supplier" data-id="<?php echo $supplier['id']; ?>">Edit</button>
                    <button class="btn btn-sm btn-danger delete-supplier" data-id="<?php echo $supplier['id']; ?>">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function filterTable() {
    const nameFilter = document.getElementById('filter-name').value.toLowerCase();
    const phoneFilter = document.getElementById('filter-phone').value.toLowerCase();
    const emailFilter = document.getElementById('filter-email').value.toLowerCase();

    const table = document.getElementById('suppliersTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[0];
        const phoneCell = rows[i].getElementsByTagName('td')[1];
        const emailCell = rows[i].getElementsByTagName('td')[2];

        const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
        const phoneText = phoneCell ? phoneCell.textContent.toLowerCase() : '';
        const emailText = emailCell ? emailCell.textContent.toLowerCase() : '';

        const matchesName = nameText.includes(nameFilter);
        const matchesPhone = phoneText.includes(phoneFilter);
        const matchesEmail = emailText.includes(emailFilter);

        if (matchesName && matchesPhone && matchesEmail) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>

<?php require_once 'includes/footer.php'; ?> 