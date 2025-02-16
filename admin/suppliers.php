<?php
session_start();
if (!isset($_SESSION['user_id'])) 
{
    header('Location: ../index.php');
    exit();
}
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
                                <h4 class="card-title">Suppliers</h4>
                                <p class="card-description">Manage your </p>

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


                                <!-- Display alert if set -->
                                <?php if (isset($alert)) echo $alert; ?>

                          <!-- Add Sale Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSaleModal">
            New Sale
        </button>
    </div>
    <!-- it be mainly  copy and paste -->

                                <!-- Sales Table -->
                                <div class="table-responsive">
                                    <table class="table table-light table-hover" id="suppliersTable">
                                        <thead>
                                        <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>`
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
                            </div>
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

    <!-- Add Supplier-->
    <!-- <div class="modal fade" id="addSaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="saleForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_sale">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Number</label>
                                <input type="text" class="form-control" name="customer_number" required>
                            </div>
                        </div>

                        <div id="product-rows">
                            <div class="row mb-3 product-row">
                                <div class="col-md-5">
                                    <label class="form-label">Product</label>
                                    <select class="form-select product-select" name="products[]" required>
                                        <option value="">Select Product</option>
                                        <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" 
                                                data-price="<?php echo $product['price']; ?>"
                                                data-stock="<?php echo $product['stock']; ?>">
                                            <?php echo $product['name']; ?> (Stock: <?php echo $product['stock']; ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control quantity" name="quantities[]" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit Price</label>
                                    <input type="number" step="0.01" class="form-control price" name="prices[]" required>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger remove-row">×</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary" id="add-product">Add Product</button>

                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-6">
                                <label class="form-label">Total Amount</label>
                                <input type="number" step="0.01" class="form-control" name="total_amount" id="total-amount" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Sale</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- Mark Paid Modal -->
    <!-- <div class="modal fade" id="markPaidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark Invoice as Paid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="paid">
                        <input type="hidden" name="invoice_id" id="paid-invoice-id">
                        <p>Are you sure you want to mark this invoice as paid?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Paid</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

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

</body>
</html>