<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Booking Details</h2>
    <a href="<?= BASEURL ?>/bookings/index" class="btn">← Back to Bookings</a>
</div>

<div class="card">
    <h3>Booking Information</h3>
    <table>
        <tr>
            <td><strong>Booking Number:</strong></td>
            <td><?= htmlspecialchars($booking['booking_number']) ?></td>
        </tr>
        <tr>
            <td><strong>Room:</strong></td>
            <td><?= htmlspecialchars($booking['room_name']) ?> (Capacity: <?= $booking['capacity'] ?>)</td>
        </tr>
        <tr>
            <td><strong>Booking Date:</strong></td>
            <td><?= date('l, d F Y', strtotime($booking['booking_date'])) ?></td>
        </tr>
        <tr>
            <td><strong>Time:</strong></td>
            <td><?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?></td>
        </tr>
        <tr>
            <td><strong>Duration:</strong></td>
            <td>
                <?php 
                $duration = (strtotime($booking['end_time']) - strtotime($booking['start_time'])) / 3600;
                echo $duration . ' hour(s)';
                ?>
            </td>
        </tr>
        <tr>
            <td><strong>Total Price:</strong></td>
            <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td><strong>Status:</strong></td>
            <td>
                <?php
                $badgeClass = 'warning';
                if($booking['status'] === 'confirmed') $badgeClass = 'success';
                if($booking['status'] === 'cancelled') $badgeClass = 'danger';
                if($booking['status'] === 'completed') $badgeClass = 'success';
                ?>
                <span class="badge badge-<?= $badgeClass ?>"><?= strtoupper($booking['status']) ?></span>
            </td>
        </tr>
        <?php if(!empty($booking['purpose'])): ?>
        <tr>
            <td><strong>Purpose:</strong></td>
            <td><?= nl2br(htmlspecialchars($booking['purpose'])) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td><strong>Created:</strong></td>
            <td><?= date('d M Y H:i', strtotime($booking['created_at'])) ?></td>
        </tr>
    </table>
</div>

<?php if(isset($user) && $user['role'] === 'admin'): ?>
<div class="card">
    <h3>Customer Information</h3>
    <table>
        <tr>
            <td><strong>Name:</strong></td>
            <td><?= htmlspecialchars($booking['user_name']) ?></td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td><?= htmlspecialchars($booking['user_email']) ?></td>
        </tr>
        <?php if(!empty($booking['user_phone'])): ?>
        <tr>
            <td><strong>Phone:</strong></td>
            <td><?= htmlspecialchars($booking['user_phone']) ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php endif; ?>

<div class="card">
    <h3>Room Details</h3>
    <table>
        <tr>
            <td><strong>Room Name:</strong></td>
            <td><?= htmlspecialchars($booking['room_name']) ?></td>
        </tr>
        <tr>
            <td><strong>Capacity:</strong></td>
            <td><?= $booking['capacity'] ?> people</td>
        </tr>
        <tr>
            <td><strong>Facilities:</strong></td>
            <td><?= nl2br(htmlspecialchars($booking['facilities'] ?? 'N/A')) ?></td>
        </tr>
        <tr>
            <td><strong>Price per Hour:</strong></td>
            <td>Rp <?= number_format($booking['price_per_hour'], 0, ',', '.') ?></td>
        </tr>
    </table>
    <div style="margin-top: 10px;">
        <a href="<?= BASEURL ?>/rooms/show/<?= $booking['room_id'] ?>" class="btn">View Room Details</a>
    </div>
</div>

<?php if($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed'): ?>
<div class="card">
    <h3>Actions</h3>
    <?php if($booking['status'] === 'pending'): ?>
        <a href="<?= BASEURL ?>/bookings/edit/<?= $booking['id'] ?>" class="btn" style="margin-right: 10px;">Edit Booking</a>
        <a href="<?= BASEURL ?>/bookings/delete/<?= $booking['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')" style="margin-right: 10px;">Delete Booking</a>
    <?php endif; ?>
    <a href="<?= BASEURL ?>/bookings/cancel/<?= $booking['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</a>
</div>
<?php endif; ?>

<?php if(isset($user) && $user['role'] === 'admin'): ?>
<div class="card">
    <h3>Admin Actions</h3>
    <form method="POST" action="<?= BASEURL ?>/bookings/updateStatus/<?= $booking['id'] ?>" style="display: inline-block;">
        <select name="status" required>
            <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <button type="submit" class="btn">Update Status</button>
    </form>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
