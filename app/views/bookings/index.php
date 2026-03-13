<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>My Bookings</h2>
    <p>View and manage your room bookings</p>
    <?php if(!isset($user) || $user['role'] !== 'admin'): ?>
        <a href="<?= BASEURL ?>/bookings/create" class="btn">+ Create New Booking</a>
    <?php endif; ?>
</div>

<?php if(!empty($bookings)): ?>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Booking Number</th>
                    <?php if(isset($user) && $user['role'] === 'admin'): ?>
                        <th>Customer</th>
                    <?php endif; ?>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['booking_number']) ?></td>
                        <?php if(isset($user) && $user['role'] === 'admin'): ?>
                            <td><?= htmlspecialchars($booking['user_name'] ?? 'N/A') ?></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($booking['room_name'] ?? 'Room #' . $booking['room_id']) ?></td>
                        <td><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                        <td><?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?></td>
                        <td>Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                        <td>
                            <?php
                            $badgeClass = 'warning';
                            if($booking['status'] === 'confirmed') $badgeClass = 'success';
                            if($booking['status'] === 'cancelled') $badgeClass = 'danger';
                            if($booking['status'] === 'completed') $badgeClass = 'success';
                            ?>
                            <span class="badge badge-<?= $badgeClass ?>"><?= strtoupper($booking['status']) ?></span>
                        </td>
                        <td>
                            <a href="<?= BASEURL ?>/bookings/show/<?= $booking['id'] ?>" class="btn">View</a>
                            <?php if($booking['status'] === 'pending'): ?>
                                <a href="<?= BASEURL ?>/bookings/edit/<?= $booking['id'] ?>" class="btn" style="margin-left: 5px;">Edit</a>
                                <a href="<?= BASEURL ?>/bookings/delete/<?= $booking['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')" style="margin-left: 5px;">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="card">
        <p>You don't have any bookings yet.</p>
        <a href="<?= BASEURL ?>/rooms/index" class="btn">Browse Rooms</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
