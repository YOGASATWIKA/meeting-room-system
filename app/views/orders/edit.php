<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Edit Order</h2>
    <a href="<?= BASEURL ?>/orders/show/<?= $order['id'] ?>" class="btn">← Back to Order</a>
</div>

<div class="card">
    <h3>Order Items</h3>
    <form action="<?= BASEURL ?>/orders/update/<?= $order['id'] ?>" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="order-items">
                <?php if(!empty($order['items'])): ?>
                    <?php foreach($order['items'] as $index => $item): ?>
                        <tr class="order-item">
                            <td>
                                <select name="product_id[]" class="product-select" required onchange="updatePrice(this)">
                                    <option value="">-- Select Product --</option>
                                    <?php foreach($products as $product): ?>
                                        <option value="<?= $product['id'] ?>" 
                                                data-price="<?= $product['price'] ?>"
                                                data-stock="<?= $product['stock'] ?>"
                                                <?= $item['product_id'] == $product['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($product['name']) ?> (Stock: <?= $product['stock'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <span class="item-price">Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                                <input type="hidden" name="price[]" class="price-input" value="<?= $item['price'] ?>">
                            </td>
                            <td>
                                <input type="number" name="quantity[]" class="quantity-input" 
                                       value="<?= $item['quantity'] ?>" min="1" required 
                                       onchange="updateSubtotal(this)" style="width: 80px;">
                            </td>
                            <td>
                                <span class="item-subtotal">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                    <td colspan="2">
                        <strong id="total-amount">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <div style="margin-top: 15px;">
            <button type="button" class="btn" onclick="addItem()">+ Add Item</button>
        </div>
        
        <h3 style="margin-top: 30px;">Delivery Information</h3>
        
        <div class="form-group">
            <label for="delivery_date">Delivery Date *</label>
            <input type="date" id="delivery_date" name="delivery_date" 
                   value="<?= htmlspecialchars($order['delivery_date']) ?>" 
                   required min="<?= date('Y-m-d') ?>">
        </div>
        
        <div class="form-group">
            <label for="delivery_address">Delivery Address *</label>
            <textarea id="delivery_address" name="delivery_address" required rows="4" 
                      placeholder="Enter complete delivery address"><?= htmlspecialchars($order['delivery_address']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes (Optional)</label>
            <textarea id="notes" name="notes" rows="3" 
                      placeholder="Any special instructions?"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn" style="width: 100%;">Update Order</button>
            <a href="<?= BASEURL ?>/orders/show/<?= $order['id'] ?>" class="btn btn-danger" style="width: 100%; margin-top: 10px;">Cancel</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>Important Notes</h3>
    <ul style="list-style: disc; padding-left: 30px;">
        <li>Only pending orders can be edited</li>
        <li>Changing items will update product stock automatically</li>
        <li>Ensure product availability before updating</li>
        <li>Total amount will be recalculated based on items</li>
    </ul>
</div>

<script>
// Product data for JavaScript
const productsData = <?= json_encode($products) ?>;

function updatePrice(selectElement) {
    const row = selectElement.closest('tr');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const price = selectedOption.dataset.price || 0;
    
    row.querySelector('.item-price').textContent = 'Rp ' + parseInt(price).toLocaleString('id-ID');
    row.querySelector('.price-input').value = price;
    
    updateSubtotal(row.querySelector('.quantity-input'));
}

function updateSubtotal(quantityInput) {
    const row = quantityInput.closest('tr');
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const quantity = parseInt(quantityInput.value) || 0;
    const subtotal = price * quantity;
    
    row.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.order-item').forEach(row => {
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        total += price * quantity;
    });
    
    document.getElementById('total-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function removeItem(button) {
    const row = button.closest('tr');
    const itemCount = document.querySelectorAll('.order-item').length;
    
    if (itemCount <= 1) {
        alert('Order must have at least one item');
        return;
    }
    
    if (confirm('Remove this item?')) {
        row.remove();
        updateTotal();
    }
}

function addItem() {
    const tbody = document.getElementById('order-items');
    const newRow = document.createElement('tr');
    newRow.className = 'order-item';
    
    let productOptions = '<option value="">-- Select Product --</option>';
    productsData.forEach(product => {
        productOptions += `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">
            ${escapeHtml(product.name)} (Stock: ${product.stock})
        </option>`;
    });
    
    newRow.innerHTML = `
        <td>
            <select name="product_id[]" class="product-select" required onchange="updatePrice(this)">
                ${productOptions}
            </select>
        </td>
        <td>
            <span class="item-price">Rp 0</span>
            <input type="hidden" name="price[]" class="price-input" value="0">
        </td>
        <td>
            <input type="number" name="quantity[]" class="quantity-input" 
                   value="1" min="1" required 
                   onchange="updateSubtotal(this)" style="width: 80px;">
        </td>
        <td>
            <span class="item-subtotal">Rp 0</span>
        </td>
        <td>
            <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
        </td>
    `;
    
    tbody.appendChild(newRow);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize total on page load
updateTotal();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
