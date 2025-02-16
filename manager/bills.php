<?php 

// session_start();


require_once 'includes/functions.php';
require_once 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['action'])) {
      switch ($_POST['action']) {
          case 'add':
              try {
                  $stmt = $pdo->prepare("INSERT INTO bills (type, amount, due_date, category_id) 
                                       VALUES (?, ?, ?, ?)");
                  $stmt->execute([
                      sanitize_input($_POST['type']),
                      (float)$_POST['amount'],
                      $_POST['due_date'],
                      (int)$_POST['category_id']
                  ]);
                  $alert = display_alert("Bill added successfully!");
              } catch (PDOException $e) {
                  $alert = display_alert("Error adding bill: " . $e->getMessage(), "danger");
              }
              break;

          case 'pay':
              try {
                  $pdo->beginTransaction();

                  // Update bill status
                  $stmt = $pdo->prepare("UPDATE bills SET status = 'paid' WHERE id = ?");
                  $stmt->execute([(int)$_POST['bill_id']]);

                  // Record payment
                  $stmt = $pdo->prepare("INSERT INTO bill_payments (bill_id, payment_date, payment_method) 
                                       VALUES (?, ?, ?)");
                  $stmt->execute([
                      (int)$_POST['bill_id'],
                      $_POST['payment_date'],
                      sanitize_input($_POST['payment_method'])
                  ]);

                  $pdo->commit();
                  $alert = display_alert("Payment recorded successfully!");
              } catch (PDOException $e) {
                  $pdo->rollBack();
                  $alert = display_alert("Error recording payment: " . $e->getMessage(), "danger");
              }
              break;
      }
  }
}

// Fetch bills with related data
$bills_query = "SELECT b.*, bc.name as category_name 
              FROM bills b 
              LEFT JOIN bill_categories bc ON b.category_id = bc.id 
              ORDER BY b.due_date ASC";
$bills = $pdo->query($bills_query)->fetchAll();

// Get bill categories for dropdown
$categories = $pdo->query("SELECT * FROM bill_categories")->fetchAll();

?> 
<!DOCTYPE html>
<html lang="en">
<?php

require_once 'includes/head.php';
require_once 'includes/header.php';
?>
<body >
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
                                <h4 class="card-title">Bills Management</h4>
                                <p class="card-description">Manage your bills and payments efficiently.</p>

                                <!-- Filter Form in a Row -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <input type="text" id="filter-type" class="form-control" placeholder="Filter by Type" maxlength="50" onkeyup="filterTable()">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" id="filter-date" class="form-control" onchange="filterTable()">
                                    </div>
                                    <div class="col-md-4">
                                        <select id="filter-status" class="form-select" onchange="filterTable()">
                                            <option value="">All Statuses</option>
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Display alert if set -->
                                <?php if (isset($alert)) echo $alert; ?>

                                <!-- Add Bill Button -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBillModal">
            Add New Bill
        </button>
    </div>

                                <!-- Bills Table -->
                                <div class="table-responsive">
                                    <table class="table table-light table-hover" id="billsTable">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Due Date</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <?php foreach ($bills as $bill): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($bill['type']); ?></td>
                                                <td><?php echo format_currency($bill['amount']); ?></td>
                                                <td><?php echo format_date($bill['due_date']); ?></td>
                                                <td><?php echo htmlspecialchars($bill['category_name']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $bill['status'] == 'paid' ? 'success' : 'warning'; ?>">
                                                        <?php echo ucfirst($bill['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($bill['status'] == 'unpaid'): ?>
                                                    <button class="btn btn-sm btn-success pay-bill"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#payBillModal"
                                                            data-id="<?php echo $bill['id']; ?>"
                                                            data-type="<?php echo $bill['type']; ?>"
                                                            data-amount="<?php echo $bill['amount']; ?>">
                                                        Pay
                                                    </button>
                                                    <?php endif; ?>
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

    <!-- Add Bill Modal -->
    <div class="modal fade" id="addBillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Bill Type</label>
                            <input type="text" class="form-control" name="type" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Bill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pay Bill Modal -->
    <div class="modal fade" id="payBillModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pay Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="pay">
                        <input type="hidden" name="bill_id" id="pay-bill-id">
                        <div class="mb-3">
                            <label class="form-label">Bill Type</label>
                            <input type="text" class="form-control" id="pay-bill-type" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control" id="pay-bill-amount" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Check">Check</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Scripts -->
<script src="../../assets/vendors/js/vendor.bundle.base.js"></script>
<script src="../../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="../../assets/js/off-canvas.js"></script>
<script src="../../assets/js/template.js"></script>
<script src="../../assets/js/settings.js"></script>
<script src="../../assets/js/hoverable-collapse.js"></script>
<script src="../../assets/js/todolist.js"></script>

<script>
// Handle Pay Bill Modal
document.querySelectorAll('.pay-bill').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('pay-bill-id').value = this.dataset.id;
            document.getElementById('pay-bill-type').value = this.dataset.type;
            document.getElementById('pay-bill-amount').value = 
                new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' })
                    .format(this.dataset.amount);
        });
    });

function filterTable() {
    const typeFilter = document.getElementById('filter-type').value.toLowerCase();
    const dateFilter = document.getElementById('filter-date').value;
    const statusFilter = document.getElementById('filter-status').value;

    const table = document.getElementById('billsTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const typeCell = rows[i].getElementsByTagName('td')[0];
        const dateCell = rows[i].getElementsByTagName('td')[2];
        const statusCell = rows[i].getElementsByTagName('td')[4];

        const typeText = typeCell ? typeCell.textContent.toLowerCase() : '';
        const dateText = dateCell ? dateCell.textContent : '';
        const statusText = statusCell ? statusCell.textContent.toLowerCase() : '';

        const matchesType = typeText.includes(typeFilter);
        const matchesDate = dateFilter ? dateText.includes(dateFilter) : true;
        const matchesStatus = statusFilter ? statusText.includes(statusFilter) : true;

        if (matchesType && matchesDate && matchesStatus) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}
</script>
</body>
</html>