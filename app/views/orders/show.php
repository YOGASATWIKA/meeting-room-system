<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Order Details</h2>
    <a href="<?= BASEURL ?>/orders/index" class="btn">← Back to Orders</a>
</div>

<div class="card">
    <h3>Order Information</h3>
    <table>
        <tr>
            <td><strong>Order Number:</strong></td>
            <td><?= htmlspecialchars($order['order_number']) ?></td>
        </tr>
        <tr>
            <td><strong>Order Date:</strong></td>
            <td><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
        </tr>
        <tr>
            <td><strong>Delivery Date:</strong></td>
            <td><?= date('d M Y', strtotime($order['delivery_date'])) ?></td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>
                <?php
                $badgeClass = 'warning';
                if($order['status'] === 'delivered') $badgeClass = 'success';
                if($order['status'] === 'cancelled') $badgeClass = 'danger';
                if($order['status'] === 'processing') $badgeClass = 'info';
                ?>
                <span class="badge badge-<?= $badgeClass ?>"><?= strtoupper($order['status']) ?></span>
            </td>
        </tr>
        <tr>
            <td><strong>Delivery Address:</strong></td>
            <td><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></td>
        </tr>
        <?php if(!empty($order['notes'])): ?>
        <tr>
            <td><strong>Notes:</strong></td>
            <td><?= nl2br(htmlspecialchars($order['notes'])) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card">
    <h3>Order Items</h3>
    <?php if(!empty($order['items'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($order['items'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name'] ?? 'Product #' . $item['product_id']) ?></td>
                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background: #f8f9fa; font-weight: bold;">
                    <td colspan="3" style="text-align: right;">Total Amount:</td>
                    <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items found for this order.</p>
    <?php endif; ?>
</div>

<?php if($order['status'] === 'pending'): ?>
<div class="card">
    <h3>Actions</h3>
    <a href="<?= BASEURL ?>/orders/edit/<?= $order['id'] ?>" class="btn" style="margin-right: 10px;">Edit Order</a>
    <a href="<?= BASEURL ?>/orders/delete/<?= $order['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.')" style="margin-right: 10px;">Delete Order</a>
    <a href="<?= BASEURL ?>/orders/cancel/<?= $order['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</a>
</div>
<?php elseif($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
<div class="card">
    <h3>Actions</h3>
    <p><em>Only pending orders can be edited or deleted.</em></p>
</div>
<?php endif; ?>

<?php if($user['role'] === 'admin'): ?>
<div class="card">
    <h3>Admin Actions</h3>
    <form method="POST" action="<?= BASEURL ?>/orders/updateStatus/<?= $order['id'] ?>" style="display: inline;">
        <select name="status" required>
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit" class="btn">Update Status</button>
    </form>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
