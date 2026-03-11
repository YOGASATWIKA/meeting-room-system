<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Shopping Cart</h2>
    <p>Review your cart and proceed to checkout.</p>
</div>

<?php if(!empty($cart_items)): ?>
    <div class="card">
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
            <tbody>
                <!-- Loop untuk menampilkan cart items (demonstrasi array dan output) -->
                <?php foreach($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product']['name']) ?></td>
                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        <td>
                            <a href="<?= BASEURL ?>/orders/removeFromCart/<?= $item['product_id'] ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Remove this item from cart?')">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                    <td colspan="2"><strong>Rp <?= number_format($total_amount, 0, ',', '.') ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="card">
        <h3>Delivery Information</h3>
        <form action="<?= BASEURL ?>/orders/store" method="POST">
            <div class="form-group">
                <label for="delivery_date">Delivery Date *</label>
                <input type="date" id="delivery_date" name="delivery_date" required 
                       min="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="form-group">
                <label for="delivery_address">Delivery Address *</label>
                <textarea id="delivery_address" name="delivery_address" required rows="4" 
                          placeholder="Enter complete delivery address"></textarea>
            </div>
            
            <div class="form-group">
                <label for="notes">Notes (Optional)</label>
                <textarea id="notes" name="notes" rows="3" 
                          placeholder="Any special instructions?"></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Place Order</button>
                <a href="<?= BASEURL ?>/products/index" class="btn">Continue Shopping</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="card">
        <p>Your cart is empty.</p>
        <a href="<?= BASEURL ?>/products/index" class="btn">Browse Products</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
