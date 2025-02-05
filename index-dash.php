<!-- Add Invoice Button -->
<button class="btn btn-primary btn-lg text-white mb-0 me-0" type="button" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
    <i class="mdi mdi-clipboard-plus"></i>Add Invoice
</button>

<!-- Add Invoice Modal -->
<div class="modal fade" id="addInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="invoiceForm">
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
                    <button type="submit" class="btn btn-primary">Save Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

// JavaScript for handling dynamic product rows and total calculation
<script>
    document.getElementById('add-product').addEventListener('click', function() {
        const row = document.querySelector('.product-row').cloneNode(true);
        row.querySelector('.product-select').value = '';
        row.querySelector('.quantity').value = '';
        row.querySelector('.price').value = '';
        document.getElementById('product-rows').appendChild(row);
        attachEventListeners(row);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                e.target.closest('.product-row').remove();
                calculateTotal();
            }
        }
    });

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

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            total += quantity * price;
        });
        document.getElementById('total-amount').value = total.toFixed(2);
    }

    document.querySelectorAll('.product-row').forEach(attachEventListeners);
</script> 