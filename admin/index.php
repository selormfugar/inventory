<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_id'])) {
  header('Location:../index.php');
  exit();
}
$page = 'dashboard';
$page_title = 'Dashboard';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

$products_query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                   FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   LEFT JOIN suppliers s ON p.supplier_id = s.id";
$products = $pdo->query($products_query)->fetchAll();

$categories = get_categories($pdo);
$suppliers = get_suppliers($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<?php 
  require_once 'includes/head.php';
  require_once 'includes/header.php';

  ?> 
   <body class="with-welcome-text">
    <div class="container-scroller">
           <!-- partial:partials/_navbar.html -->
          <?php
          require_once 'includes/navbar.php';
           ?>
      <!-- partial -->
      
        <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
         <?php require_once 'includes/partial-bar.php';?>
       
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-12">
                <div class="home-tab">
                  <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                      </li>
                   
                    </ul>
                    <div>
                      <!-- <div class="btn-wrapper">
                        <a href="#" class="btn btn-otline-dark align-items-center"><i class="icon-share"></i> Share</a>
                        <a href="#" class="btn btn-otline-dark"><i class="icon-printer"></i> Print</a>
                        <a href="#" class="btn btn-primary text-white me-0"><i class="icon-download"></i> Export</a>
                      </div> -->
                    </div>

                  </div>
                  <div class="tab-content tab-content-basic">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                    
                    <div class="row">
                        <div class="col-sm-12">
                        <div class="statistics-details d-flex align-items-center justify-content-between">
    <div>
        <p class="statistics-title">Total Products</p> 
        <h3 class="rate-percentage">
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM products");
            echo $stmt->fetchColumn();
            ?>
        </h3>
    </div>

    <div>
        <p class="statistics-title">Total Sales</p>
        <h3 class="rate-percentage">
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM sales ");
            echo $stmt->fetchColumn();
            ?>
        </h3>
    </div>

    <div>
        <p class="statistics-title">Pending Bills</p>
        <h3 class="rate-percentage">
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) FROM bills WHERE status = 'unpaid'");
            echo $stmt->fetchColumn();
            ?>
        </h3>
    </div>

    <div class="d-none d-md-block">
        <p class="statistics-title">Low Stock Products</p>
        <h3 class="rate-percentage">
            <?php
             $stmt = $pdo->query("SELECT Count(*) FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
                                        echo $stmt->fetchColumn();
            ?>
        </h3>
    </div>

    <div class="d-none d-md-block">
        <p class="statistics-title">Total Revenue</p>
        <h3 class="rate-percentage">
            <?php
          $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM sales ");
echo number_format($stmt->fetchColumn(), 2);
            ?>
        </h3>
    </div>

    <div class="d-none d-md-block">
        <p class="statistics-title">Top-Selling Product</p>
        <h3 class="rate-percentage">
            <?php
            $stmt = $pdo->query("SELECT p.name 
                                 FROM products p 
                                 JOIN sale_order s ON p.id = s.product_id 
                                 GROUP BY p.id 
                                 ORDER BY COUNT(s.id) DESC 
                                 LIMIT 1");
            echo htmlspecialchars($stmt->fetchColumn() ?: 'N/A');
            ?>
        </h3>
    </div>
</div>

                          </div>
                        </div>
                      </div>
                      
                    
                      <div class="row">
                        <div class="col-lg-8 d-flex flex-column">
                                           
                          <div class="row flex-grow">
                            <div class="col-12 grid-margin stretch-card">
                              <div class="card card-rounded">
                                <div class="card-body">
                                  <div class="d-sm-flex justify-content-between align-items-start">
                                    <div>
                                      <h4 class="card-title card-title-dash">Sales</h4>
                                    </div>
                                    <div>
                                      <button class="btn btn-success btn-lg text-white mb-0 me-0" type="button" data-bs-toggle="modal" data-bs-target="#addSaleModal">
                                        <i class="mdi mdi-clipboard-plus"></i>New Sale
                                      </button> 
                                     <!-- Button to Trigger Modal -->
<button type="button" class="btn btn-primary btn-lg text-white mb-0 me-0" data-bs-toggle="modal" data-bs-target="#generateInvoiceModal">
    Generate Invoice
</button>
                                    </div>
                                  </div>
                                   <div class="table-responsive  mt-1">
                                  <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Customer</th>
                                                        <th>Date</th>
                                                        <th>Product</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $stmt = $pdo->query("SELECT customer_name , customer_number, invoice_date,total_amount
                                                                        FROM sales 
                                                                        ORDER BY invoice_date DESC LIMIT 5");

                                                    $counter = 1; // Initialize counter
                                                    while ($row = $stmt->fetch()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $counter . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                                        echo "<td>" . htmlspecialchars($row['customer_number']) . "</td>";
                                                        echo "<td>$" . number_format($row['total_amount'], 2) . "</td>";
                                                        echo "<td>" . date('Y-m-d', strtotime($row['invoice_date'])) . "</td>";  echo "</tr>";
                                                        $counter++; // Increment counter
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

                                  
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>

                         <!-- Add Sale Modal -->
    <<div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="addSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSaleModalLabel">New Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saleForm" method="POST">
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

                    <!-- Products Container -->
                    <div id="sale-product-rows">
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
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control quantity" name="quantities[]" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit Price</label>
                                <input type="number" step="0.01" class="form-control price" name="prices[]" readonly>
                            </div>
                            <div class="col-md-2">
    <label class="form-label">Row Total</label>
    <input type="number" step="0.01" class="form-control row-total" name="row_totals[]" readonly>
</div>
                            <div class="col-md-1">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-row">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" id="add-sale-product">Add Product</button>

                    <!-- Total Amount -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" name="total_amount" id="sale-total-amount" readonly>
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


<div class="modal fade" id="generateInvoiceModal" tabindex="-1" aria-labelledby="generateInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateInvoiceModalLabel">Generate Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="invoiceForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_invoice">
                    
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

                    <!-- Products Container -->
                    <div id="invoice-product-rows">
    <div class="row mb-3 product-row">
                <div class="col-md-3">
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
                    <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control quantity" name="quantities[]" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Interest</label>
                    <input type="number" class="form-control interest" name="interests[]" step="any" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Unit Price</label>
                    <input type="number" step="0.01" class="form-control price" name="prices[]" readonly>
                </div>
                <div class="col-md-2">
                <label class="form-label">Row Total</label>
                <input type="number" step="0.01" class="form-control row-total" name="row_totals[]" readonly>
            </div>
            <div class="col-md-1">
                <label class="form-label d-block">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
            </div>
    </div>
</div>
                    <button type="button" class="btn btn-secondary mb-3" id="add-invoice-product">Add Product</button>

                    <!-- Total Amount -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" step="0.01" class="form-control" name="total_amount" id="invoice-total-amount" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="position: sticky; bottom: 0; background-color: white; z-index: 1000;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>
                        </div>
                        <div class="col-lg-4 d-flex flex-column">
                          <div class="row flex-grow">
                            <div class="col-12 grid-margin stretch-card">
                              <div class="card card-rounded">
                                <div class="card-body">
                                  <div class="row">
                                    <div class="col-lg-12">
                                      <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title card-title-dash">Low Stock Products</h4>
                                        <div class="add-items d-flex mb-0">
                                          <!-- <input type="text" class="form-control todo-list-input" placeholder="What do you need to do today?"> -->
                                          <!-- <button class="add btn btn-icons btn-rounded btn-primary todo-list-add-btn text-white me-0 pl-12p"><i class="mdi mdi-plus"></i></button> -->
                                        </div>
                                      </div>
                                      <div class="table-responsive mt-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $pdo->query("SELECT name, stock FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
                                        while ($row = $stmt->fetch()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                            echo "<td>" . $row['stock'] . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        
                            </div>
                          </div>

                          
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <?php require_once 'includes/footer.php';?>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <?php 
  require_once 'includes/main.php';
  ?>    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendors/select2/select2.min.js"></script>

<!-- Bootstrap JS (make sure this is included) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
 
<!-- Updated JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Add product button handlers
    ['add-sale-product', 'add-invoice-product'].forEach(buttonId => {
        const addButton = document.getElementById(buttonId);
        if (addButton) {
            addButton.addEventListener('click', function () {
                const containerType = buttonId.includes('sale') ? 'sale' : 'invoice';
                const container = document.getElementById(`${containerType}-product-rows`);
                const newRow = container.querySelector('.product-row').cloneNode(true);

                // Reset values
                newRow.querySelector('.product-select').value = '';
                newRow.querySelector('.quantity').value = '';
                newRow.querySelector('.price').value = '';
                
                // Reset interest and row-total if they exist (for invoice form)
                const interestInput = newRow.querySelector('.interest');
                if (interestInput) interestInput.value = '0';
                
                const rowTotalInput = newRow.querySelector('.row-total');
                if (rowTotalInput) rowTotalInput.value = '';

                container.appendChild(newRow);
                updateTotalAmount(containerType);
            });
        }
    });

    // Remove row handler using event delegation
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            const row = e.target.closest('.product-row');
            const container = row.parentElement;
            const containerType = container.id.includes('sale') ? 'sale' : 'invoice';
            
            if (container.querySelectorAll('.product-row').length > 1) {
                row.remove();
                updateTotalAmount(containerType);
            } else {
                alert('Cannot remove the last row.');
            }
        }
    });

    // Use event delegation for product selection, quantity and interest changes
    ['sale', 'invoice'].forEach(containerType => {
        const container = document.getElementById(`${containerType}-product-rows`);
        if (container) {
            // Handle product selection changes
            container.addEventListener('change', function (e) {
                if (e.target.classList.contains('product-select')) {
                    const row = e.target.closest('.product-row');
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    
                    if (selectedOption && selectedOption.dataset.price) {
                        row.querySelector('.price').value = selectedOption.dataset.price;
                        updateRowTotal(row, containerType);
                        updateTotalAmount(containerType);
                    }
                }
            });

            // Handle quantity and interest changes
            container.addEventListener('input', function (e) {
                if (e.target.classList.contains('quantity') || 
                    e.target.classList.contains('interest') ||
                    e.target.classList.contains('price')) {
                    const row = e.target.closest('.product-row');
                    updateRowTotal(row, containerType);
                    updateTotalAmount(containerType);
                }
            });
        }
    });

    // Handle form submissions
    function handleFormSubmit(formId, modalId, type) {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                const modal = document.getElementById(modalId);

                // Add the action type
                formData.append('action', type === 'sale' ? 'add_sale' : 'add_invoice');

                fetch('api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);

                        // Ask if they want to print
                        const shouldPrint = confirm('Do you want to print the ' + type + '?');
                        if (shouldPrint && data.id) {
                            if (type === 'sale') {
                                printSale(data.id);
                            } else {
                                printInvoice(data.id);
                            }
                        }

                        // Close modal
                        if (modal) {
                            const modalInstance = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                            modalInstance.hide();
                        }

                        // Reset form
                        form.reset();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request. Please try again.');
                });
            });
        }
    }

    function printSale(saleId) {
        fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=print_sale&sale_id=${saleId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                fetch('print_template.html')
                    .then(response => response.text())
                    .then(template => {
                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(template);
                        printWindow.document.close();

                        printWindow.onload = function () {
                            // Populate customer details
                            printWindow.document.getElementById('customerName').textContent = data.data.customer_name;
                            printWindow.document.getElementById('customerNumber').textContent = data.data.customer_number;
                            printWindow.document.getElementById('invoiceDate').textContent = data.data.invoice_date;

                            // Populate product rows
                            const productsTable = printWindow.document.getElementById('productsTable');
                            productsTable.innerHTML = ''; // Clear previous rows

                            data.data.products.forEach(product => {
                                const row = printWindow.document.createElement('tr');
                                row.innerHTML = `
                                    <td>${product.product_name}</td>
                                    <td>${product.quantity}</td>
                                    <td>${product.unit_price}</td>
                                    <td>${product.total_price}</td>
                                `;
                                productsTable.appendChild(row);
                            });

                            // Print the window
                            printWindow.print();
                        };
                    });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while printing the sale.');
        });
    }

    function printInvoice(invoiceId) {
        fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=print_invoice&invoice_id=${invoiceId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                fetch('print_template.html')
                    .then(response => response.text())
                    .then(template => {
                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(template);
                        printWindow.document.close();

                        printWindow.onload = function () {
                            // Populate customer details
                            printWindow.document.getElementById('customerName').textContent = data.data.customer_name;
                            printWindow.document.getElementById('customerNumber').textContent = data.data.customer_number;
                            printWindow.document.getElementById('invoiceDate').textContent = data.data.invoice_date; 
                            printWindow.document.getElementById('total').textContent = data.data.total;


                            // Populate product rows
                            const productsTable = printWindow.document.getElementById('productsTable');
                            productsTable.innerHTML = ''; // Clear previous rows

                            data.data.products.forEach(product => {
                                const row = printWindow.document.createElement('tr');
                                row.innerHTML = `
                                    <td>${product.product_name}</td>
                                    <td>${product.quantity}</td>
                                    <td>${product.unit_price}</td>
                                    <td>${product.total_price}</td>
                                `;
                                productsTable.appendChild(row);
                            });

                            // Print the window
                            printWindow.print();
                        };
                    });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while printing the invoice.');
        });
    }

    // Initialize handlers
    handleFormSubmit('saleForm', 'salesModal', 'sale'); 
    handleFormSubmit('invoiceForm', 'invoicesModal', 'invoice');

    // Initial calculation for all containers
    ['sale', 'invoice'].forEach(containerType => {
        updateTotalAmount(containerType);
    });
});


// Function to update a single row's total
function updateRowTotal(row, containerType) {
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.price').value) || 0;
    const baseAmount = quantity * unitPrice;
    
    // For invoice form with interest
    if (containerType === 'invoice') {
        const interestRate = parseFloat(row.querySelector('.interest').value) || 0;
        const interestAmount = baseAmount * (interestRate / 100);
        const rowTotal = baseAmount + interestAmount;
        
        // Update the row-total input
        const rowTotalInput = row.querySelector('.row-total');
        if (rowTotalInput) {
            rowTotalInput.value = rowTotal.toFixed(2);
        }
        
        return rowTotal;
    } else {
        // For sale form without interest
        // Update the row-total input if it exists
        const rowTotalInput = row.querySelector('.row-total');
        if (rowTotalInput) {
            rowTotalInput.value = baseAmount.toFixed(2);
        }
        return baseAmount;
    }
}

// Function to update the container total
function updateTotalAmount(containerType) {
    const container = document.getElementById(`${containerType}-product-rows`);
    const totalField = document.getElementById(`${containerType}-total-amount`);
    
    if (container && totalField) {
        let total = 0;
        
        container.querySelectorAll('.product-row').forEach(row => {
            total += updateRowTotal(row, containerType);
        });
        
        totalField.value = total.toFixed(2);
    }
}
</script>
  </body>
</html>