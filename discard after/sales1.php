<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_sale':
                try {
                    $pdo->beginTransaction();

                    // Create invoice
                    $stmt = $pdo->prepare("INSERT INTO invoices (customer_name, customer_number, total_amount) 
                                         VALUES (?, ?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['customer_name']),
                        sanitize_input($_POST['customer_number']),
                        (float)$_POST['total_amount']
                    ]);
                    $invoice_id = $pdo->lastInsertId();

                    // Add sales items
                    $stmt = $pdo->prepare("INSERT INTO sales (invoice_id, product_id, quantity, unit_price) 
                                         VALUES (?, ?, ?, ?)");
                    
                    foreach ($_POST['products'] as $index => $product_id) {
                        if (!empty($product_id)) {
                            $stmt->execute([
                                $invoice_id,
                                (int)$product_id,
                                (int)$_POST['quantities'][$index],
                                (float)$_POST['prices'][$index]
                            ]);

                            // Update product stock
                            $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
                                ->execute([(int)$_POST['quantities'][$index], (int)$product_id]);
                        }
                    }

                    $pdo->commit();
                    $alert = display_alert("Sale recorded successfully!");
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $alert = display_alert("Error recording sale: " . $e->getMessage(), "danger");
                }
                break;

            case 'update_status':
                try {
                    $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['status'],
                        (int)$_POST['invoice_id']
                    ]);
                    $alert = display_alert("Invoice status updated successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error updating status: " . $e->getMessage(), "danger");
                }
                break;
        }
    }
}

// Get filter values
$filter_customer = isset($_POST['filter_customer']) ? $_POST['filter_customer'] : '';
$filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : '';

// Get all sales with related data, applying filters
$sales_query = "SELECT s.*, p.name as product_name, i.customer_name, i.customer_number, 
                       i.status as invoice_status, i.invoice_date
                FROM sales s
                JOIN products p ON s.product_id = p.id
                JOIN invoices i ON s.invoice_id = i.id
                WHERE i.customer_name LIKE ? AND DATE(s.created_at) LIKE ?
                ORDER BY s.created_at DESC";
$sales = $pdo->prepare($sales_query);
$sales->execute(["%$filter_customer%", "%$filter_date%"]);
$sales = $sales->fetchAll();

// Get products for dropdown
$products = get_products($pdo);
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <h2>Sales</h2>
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

    <!-- Sales Table -->
    <div class="table-responsive">
        <table class="table table-striped" id="salesTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?php echo format_date($sale['created_at']); ?></td>
                    <td><?php echo $sale['invoice_id']; ?></td>
                    <td><?php echo $sale['customer_name']; ?></td>
                    <td><?php echo $sale['product_name']; ?></td>
                    <td><?php echo $sale['quantity']; ?></td>
                    <td><?php echo format_currency($sale['unit_price']); ?></td>
                    <td><?php echo format_currency($sale['total_price']); ?></td>
                    <td>
                        <span class="badge bg-<?php echo $sale['invoice_status'] == 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($sale['invoice_status']); ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary view-sale" 
                                data-bs-toggle="modal" 
                                data-bs-target="#viewSaleModal"
                                data-id="<?php echo $sale['invoice_id']; ?>"
                                data-customer="<?php echo $sale['customer_name']; ?>">
                            View
                        </button>
                        <?php if ($sale['invoice_status'] == 'unpaid'): ?>
                        <button class="btn btn-sm btn-success mark-paid"
                                data-bs-toggle="modal"
                                data-bs-target="#markPaidModal"
                                data-id="<?php echo $sale['invoice_id']; ?>">
                            Mark Paid
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

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

</div>

<?php require_once 'includes/footer.php'; ?> 