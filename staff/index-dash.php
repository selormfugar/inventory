<?php
$page = 'dashboard';
$page_title = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

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

$products_query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                   FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   LEFT JOIN suppliers s ON p.supplier_id = s.id";
$products = $pdo->query($products_query)->fetchAll();

$categories = get_categories($pdo);
$suppliers = get_suppliers($pdo);
?><!DOCTYPE html>
<html lang="en">
<?php 
  require_once 'includes/head.php';
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
                      <!-- <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#audiences" role="tab" aria-selected="false">Audiences</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#demographics" role="tab" aria-selected="false">Demographics</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link border-0" id="more-tab" data-bs-toggle="tab" href="#more" role="tab" aria-selected="false">More</a>
                      </li> -->
                    </ul>
                    <div>
                      <div class="btn-wrapper">
                        <a href="#" class="btn btn-otline-dark align-items-center"><i class="icon-share"></i> Share</a>
                        <a href="#" class="btn btn-otline-dark"><i class="icon-printer"></i> Print</a>
                        <a href="#" class="btn btn-primary text-white me-0"><i class="icon-download"></i> Export</a>
                      </div>
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
            $stmt = $pdo->query("SELECT COUNT(*) FROM sales");
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
            $stmt = $pdo->query("SELECT SUM(total_amount) FROM invoices WHERE status = 'paid'");
            echo number_format($stmt->fetchColumn(), 2);
            ?>
        </h3>
    </div>

    <!-- <div class="d-none d-md-block">
        <p class="statistics-title">Top-Selling Product</p>
        <h3 class="rate-percentage">
            <?php
            $stmt = $pdo->query("SELECT p.name 
                                 FROM products p 
                                 JOIN sales s ON p.id = s.product_id 
                                 GROUP BY p.id 
                                 ORDER BY COUNT(s.id) DESC 
                                 LIMIT 1");
            echo htmlspecialchars($stmt->fetchColumn() ?: 'N/A');
            ?>
        </h3>
    </div> -->
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
                                      <button class="btn btn-primary btn-lg text-white mb-0 me-0" type="button" data-bs-toggle="modal" data-bs-target="#addSaleModal">
                                        <i class="mdi mdi-clipboard-plus"></i>Add Invoice
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
        $stmt = $pdo->query("SELECT s.*, p.name as product_name, i.customer_name, 
                                    s.quantity * s.unit_price as total_amount
                             FROM sales s
                             JOIN products p ON s.product_id = p.id
                             JOIN invoices i ON s.invoice_id = i.id
                             ORDER BY s.created_at DESC LIMIT 5");

        $counter = 1; // Initialize counter
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $counter . "</td>";
            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
            echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
            echo "<td>$" . number_format($row['total_amount'], 2) . "</td>";
            echo "</tr>";
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
    <div class="modal fade" id="addSaleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                                    <div class="form-group">
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
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control quantity" name="quantities[]" min="1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Unit Price</label>
                                    <input type="number" step="0.01" class="form-control price" name="prices[]"  readonly>
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
<script>
    document.getElementById('saleForm').addEventListener('submit', function(event) {
        setTimeout(() => {
            this.reset(); // Reset all form fields
            document.getElementById('total-amount').value = ''; // Clear total amount
        }, 500); // Delay to ensure form submission completes
    });
</script>

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

    // Initialize Select2
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });

 </script>
  </body>
</html>