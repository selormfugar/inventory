<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_sale') {
        try {
            $pdo->beginTransaction();  // Start transaction
            
            // Insert into sales table
            $stmt = $pdo->prepare("INSERT INTO sales (customer_name, customer_number, invoice_date, total_amount) 
                                   VALUES (?, ?, NOW(), ?)");
            $stmt->execute([
                $_POST['customer_name'],
                $_POST['customer_number'],
                $_POST['total_amount']
            ]);
            
            $sales_id = $pdo->lastInsertId(); // Get the last inserted ID
            
            // Insert into sale_order table
            $stmt = $pdo->prepare("INSERT INTO sale_order (sales_id, product_id, quantity, unit_price, total_price, created_at) 
                                   VALUES (?, ?, ?, ?, ?, NOW())");
            
            foreach ($_POST['products'] as $index => $product_id) {
                if (!empty($product_id)) {
                    $quantity = (int)$_POST['quantities'][$index];
                    $unit_price = (float)$_POST['prices'][$index];
                    $total_price = $quantity * $unit_price;
                    
                    $stmt->execute([$sales_id, $product_id, $quantity, $unit_price, $total_price]);
                }
            }
            
            $pdo->commit(); // Commit transaction

            echo json_encode(["status" => "success", "message" => "Sale added successfully."]);
        } catch (Exception $e) {
            $pdo->rollBack(); // Rollback on error
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
// Get filter values
$filter_customer = isset($_POST['filter_customer']) ? $_POST['filter_customer'] : '';
$filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : '';

// Get all sales with related data, applying filters
$sales_query = "SELECT s.id, s.customer_name, s.customer_number, s.invoice_date, s.total_amount
                FROM sales s
                                WHERE s.customer_name LIKE ? AND DATE(s.invoice_date) LIKE ?
                ORDER BY s.invoice_date DESC";
$sales = $pdo->prepare($sales_query);
$sales->execute(["%$filter_customer%", "%$filter_date%"]);
$sales = $sales->fetchAll();

// Get products for dropdown
$products = get_products($pdo);

$saleId = $_GET['sale_id'] ?? null;

if ($saleId) {
    // Fetch order details from sale_order table
    $stmt = $pdo->prepare("
        SELECT so.*, p.name as product_name 
        FROM sale_order so
        JOIN products p ON so.product_id = p.id
        WHERE so.sales_id = ?
    ");
    $stmt->execute([$saleId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($orders);
} else {
    echo json_encode([]);
}
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
                                <h4 class="card-title">Sales Management</h4>
                                <p class="card-description">Manage your sales and invoices efficiently.</p>

                                <!-- Filter Form in a Row -->
                                <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="filter-customer" class="form-control" placeholder="Filter by Customer Name" maxlength="50" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="text" id="filter-product" class="form-control" placeholder="Filter by Product Name" maxlength="50" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="date" id="filter-date" class="form-control" onchange="filterTable()">
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
                                    <table class="table table-light table-hover" id="salesTable">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
                                        </thead>
                                        <tbody>
                <?php
                $counter = 1; 
                 foreach ($sales as $sale): ?>
                    
                <tr>
                    <td><?php echo $counter ; ?></td>
                    <td><?php echo format_date($sale['invoice_date']); ?></td>
                    <td><?php echo $sale['id']; ?></td>
                    <td><?php echo $sale['customer_name']; ?></td>
                    <td><?php echo format_currency($sale['total_amount']); ?></td>
                    <!-- <td>
                        <span class="badge bg-<?php echo $sale['invoice_status'] == 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($sale['invoice_status']); ?>
                        </span>
                    </td> -->
                    <td>
                        <button class="btn btn-sm btn-primary view-sale" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viewSaleModal"
                                data-id="<?php echo $sale['id']; ?>"
                                data-customer="<?php echo $sale['customer_name']; ?>">
                            View
                        </button>
                       
                    </td>
                </tr>
                <?php $counter++;
                endforeach;
                  ?>
            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSaleModalLabel">Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Customer Name:</strong> <span id="modalCustomerName"></span></p>
                <p><strong>Sale ID:</strong> <span id="modalSaleId"></span></p>
                <h6>Order Details:</h6>
                <ul id="modalOrderDetails"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div><script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to all "View" buttons
    document.querySelectorAll('.view-sale').forEach(button => {
        button.addEventListener('click', function() {
            // Get data attributes
            const saleId = this.getAttribute('data-id');
            const customerName = this.getAttribute('data-customer');

            // Set customer name and sale ID in the modal
            document.getElementById('modalCustomerName').textContent = customerName;
            document.getElementById('modalSaleId').textContent = saleId;

            // Fetch order details via AJAX
            fetch(`get_sale_order_details.php?sale_id=${saleId}`)
                .then(response => response.json())
                .then(data => {
                    const orderDetails = document.getElementById('modalOrderDetails');
                    orderDetails.innerHTML = ''; // Clear previous content

                    if (data.length > 0) {
                        data.forEach(order => {
                            const listItem = document.createElement('li');
                            listItem.textContent = `Order ID: ${order.id}, Product: ${order.product_name}, Quantity: ${order.quantity}`;
                            orderDetails.appendChild(listItem);
                        });
                    } else {
                        orderDetails.innerHTML = '<li>No orders found for this sale.</li>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                });
        });
    });
});
</script>
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

    <!-- Add Sale Modal -->
    <div class="modal fade" id="addSaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="saleForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_sale">
                        
                        <!-- Customer Information -->
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

                        <!-- Products -->
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

                        <!-- Total Amount -->
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
    </div>

    <!-- Mark Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1">
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
    // Handle dynamic product rows
    document.getElementById('add-product').addEventListener('click', function() {
        const row = document.querySelector('.product-row').cloneNode(true);
        row.querySelector('.product-select').value = '';
        row.querySelector('.quantity').value = '';
        row.querySelector('.price').value = '';
        document.getElementById('product-rows').appendChild(row);
        attachEventListeners(row);
    });

    // Remove product row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                e.target.closest('.product-row').remove();
                calculateTotal();
            }
        }
    });

    // Handle product selection
    function attachEventListeners(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity');
        const priceInput = row.querySelector('.price');

        productSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            priceInput.value = option.dataset.price;
            quantityInput.max = option.dataset.stock;
            calculateTotal();
        });

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
    }

    // Calculate total amount
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            total += quantity * price;
        });
        document.getElementById('total-amount').value = total.toFixed(2);
    }

    // Initialize event listeners
    document.querySelectorAll('.product-row').forEach(attachEventListeners);

    // Handle Mark Paid button
    document.querySelectorAll('.mark-paid').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('paid-invoice-id').value = this.dataset.id;
        });
    });

    function filterTable() {
        const customerFilter = document.getElementById('filter-customer').value.toLowerCase();
        const productFilter = document.getElementById('filter-product').value.toLowerCase();
        const dateFilter = document.getElementById('filter-date').value;

        const table = document.getElementById('salesTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const customerCell = rows[i].getElementsByTagName('td')[1];
            const productCell = rows[i].getElementsByTagName('td')[2];
            const dateCell = rows[i].getElementsByTagName('td')[5]; // Assuming the status is in the 6th column

            const customerText = customerCell ? customerCell.textContent.toLowerCase() : '';
            const productText = productCell ? productCell.textContent.toLowerCase() : '';
            const dateText = dateCell ? dateCell.textContent : ''; // Adjust if you have a date column

            const matchesCustomer = customerText.includes(customerFilter);
            const matchesProduct = productText.includes(productFilter);
            const matchesDate = dateFilter ? dateText.includes(dateFilter) : true; // Adjust if you have a date column

            if (matchesCustomer && matchesProduct && matchesDate) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
    </script>

</body>
</html>