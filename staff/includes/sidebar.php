<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h3>Stan Inventory</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>
        <li class="<?php echo $page == 'products' ? 'active' : ''; ?>">
            <a href="products.php">
                <i class="bi bi-box"></i>
                Products
            </a>
        </li>
        <li class="<?php echo $page == 'sales' ? 'active' : ''; ?>">
            <a href="sales.php">
                <i class="bi bi-cart"></i>
                Sales
            </a>
        </li>
        <li class="<?php echo $page == 'bills' ? 'active' : ''; ?>">
            <a href="bills.php">
                <i class="bi bi-receipt"></i>
                Bills
            </a>
        </li>
        <li class="<?php echo $page == 'suppliers' ? 'active' : ''; ?>">
            <a href="suppliers.php">
                <i class="bi bi-truck"></i>
                Suppliers
            </a>
        </li>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <li class="<?php echo $page == 'users' ? 'active' : ''; ?>">
            <a href="users.php">
                <i class="bi bi-people"></i>
                Users
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav> 