<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $stmt = $pdo->prepare("INSERT INTO products (name, sku, category_id, supplier_id, price, stock) 
                                         VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        sanitize_input($_POST['name']),
                        sanitize_input($_POST['sku']),
                        (int)$_POST['category_id'],
                        (int)$_POST['supplier_id'],
                        (float)$_POST['price'],
                        (int)$_POST['stock']
                    ]);
                    $alert = display_alert("Product added successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error adding product: " . $e->getMessage(), "danger");
                }
                break;

            case 'edit':
                try {
                    $stmt = $pdo->prepare("UPDATE products 
                                         SET name = ?, sku = ?, category_id = ?, supplier_id = ?, 
                                             price = ?, stock = ? 
                                         WHERE id = ?");
                    $stmt->execute([
                        sanitize_input($_POST['name']),
                        sanitize_input($_POST['sku']),
                        (int)$_POST['category_id'],
                        (int)$_POST['supplier_id'],
                        (float)$_POST['price'],
                        (int)$_POST['stock'],
                        (int)$_POST['id']
                    ]);
                    $alert = display_alert("Product updated successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error updating product: " . $e->getMessage(), "danger");
                }
                break;

            case 'delete':
                try {
                    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                    $stmt->execute([(int)$_POST['id']]);
                    $alert = display_alert("Product deleted successfully!");
                } catch (PDOException $e) {
                    $alert = display_alert("Error deleting product: " . $e->getMessage(), "danger");
                }
                break;
        }
    }
}

// Get filter values
$filter_name = isset($_POST['filter_name']) ? $_POST['filter_name'] : '';
$filter_category = isset($_POST['filter_category']) ? $_POST['filter_category'] : '';

// Get all products with filters
$products_query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                   FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   LEFT JOIN suppliers s ON p.supplier_id = s.id";
$products = $pdo->query($products_query)->fetchAll();

$categories = get_categories($pdo);
$suppliers = get_suppliers($pdo);
?>

<?php require_once 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Products</h2>

    <!-- Filter Form in a Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" id="filter-name" class="form-control" placeholder="Filter by Name" maxlength="50" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="text" id="filter-sku" class="form-control" placeholder="Filter by SKU" maxlength="20" onkeyup="filterTable()">
        </div>
        <div class="col-md-4">
            <input type="date" id="filter-date" class="form-control" onchange="filterTable()">
        </div>
    </div>

    <!-- Display alert if set -->
    <?php if (isset($alert)) echo $alert; ?>

    <!-- Add Product Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            Add New Product
        </button>
    </div>

    <!-- Products Table -->
    <div class="table-responsive">
        <table class="table table-striped" id="productsTable">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['supplier_name']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-product" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editProductModal"
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo $product['name']; ?>"
                                data-sku="<?php echo $product['sku']; ?>"
                                data-category="<?php echo $product['category_id']; ?>"
                                data-supplier="<?php echo $product['supplier_id']; ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-stock="<?php echo $product['stock']; ?>">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-product" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteProductModal"
                                data-id="<?php echo $product['id']; ?>"
                                data-name="<?php echo $product['name']; ?>">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku" required>
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
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" name="supplier_id" required>
                                <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['id']; ?>">
                                    <?php echo $supplier['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku" id="edit-sku" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" id="edit-category" required>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" name="supplier_id" id="edit-supplier" required>
                                <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['id']; ?>">
                                    <?php echo $supplier['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="edit-price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="edit-stock" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-id">
                        <p>Are you sure you want to delete <span id="delete-name"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Handle Edit Product Modal
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-name').value = this.dataset.name;
            document.getElementById('edit-sku').value = this.dataset.sku;
            document.getElementById('edit-category').value = this.dataset.category;
            document.getElementById('edit-supplier').value = this.dataset.supplier;
            document.getElementById('edit-price').value = this.dataset.price;
            document.getElementById('edit-stock').value = this.dataset.stock;
        });
    });

    // Handle Delete Product Modal
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('delete-id').value = this.dataset.id;
            document.getElementById('delete-name').textContent = this.dataset.name;
        });
    });

    function filterTable() {
        const nameFilter = document.getElementById('filter-name').value.toLowerCase();
        const skuFilter = document.getElementById('filter-sku').value.toLowerCase();
        const dateFilter = document.getElementById('filter-date').value;

        const table = document.getElementById('productsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const skuCell = rows[i].getElementsByTagName('td')[0];
            const nameCell = rows[i].getElementsByTagName('td')[1];
            const dateCell = rows[i].getElementsByTagName('td')[2]; // Assuming you have a date column

            const skuText = skuCell ? skuCell.textContent.toLowerCase() : '';
            const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
            const dateText = dateCell ? dateCell.textContent : ''; // Adjust if you have a date column

            const matchesSKU = skuText.includes(skuFilter);
            const matchesName = nameText.includes(nameFilter);
            const matchesDate = dateFilter ? dateText.includes(dateFilter) : true; // Adjust if you have a date column

            if (matchesSKU && matchesName && matchesDate) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
    </script>

</div>

<?php require_once 'includes/footer.php'; ?> 