<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>My Orders</h2>
    <?php if($user['role'] === 'admin'): ?>
        <p>Viewing all orders (Admin)</p>
    <?php else: ?>
        <p>Your order history</p>
    <?php endif; ?>
</div>

<?php if(!empty($orders)): ?>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Date</th>
                    <th>Delivery Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                        <td><?= date('d M Y', strtotime($order['delivery_date'])) ?></td>
                        <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                        <td>
                            <?php
                            $statusClass = 'badge-warning';
                            if($order['status'] === 'delivered') $statusClass = 'badge-success';
                            if($order['status'] === 'cancelled') $statusClass = 'badge-danger';
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASEURL ?>/orders/show/<?= $order['id'] ?>" class="btn">View</a>
                            <?php if($order['status'] === 'pending'): ?>
                                <a href="<?= BASEURL ?>/orders/edit/<?= $order['id'] ?>" class="btn" style="margin-left: 5px;">Edit</a>
                                <a href="<?= BASEURL ?>/orders/delete/<?= $order['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?')" style="margin-left: 5px;">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="card">
        <p>No orders found.</p>
        <a href="<?= BASEURL ?>/products/index" class="btn">Start Shopping</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
