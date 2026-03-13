<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>My Dashboard</h2>
    <p>Welcome, <strong><?= htmlspecialchars($user['full_name']) ?></strong></p>
</div>

<div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
    <div class="card" style="background: #3498db; color: white;">
        <h3><?= $booking_count ?></h3>
        <p>Total Bookings</p>
    </div>
    <div class="card" style="background: #2ecc71; color: white;">
        <h3><?= count(array_filter($my_bookings, function($b) { return $b['status'] === 'completed' || $b['status'] === 'confirmed'; })) ?></h3>
        <p>Confirmed Bookings</p>
    </div>
    <div class="card" style="background: #f39c12; color: white;">
        <h3><?= count(array_filter($my_bookings, function($b) { return $b['status'] === 'pending'; })) ?></h3>
        <p>Pending Bookings</p>
    </div>
</div>

<div class="card">
    <h3>My Bookings</h3>
    <?php if(!empty($my_bookings)): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking Number</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($my_bookings, 0, 10) as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['booking_number']) ?></td>
                        <td><?= htmlspecialchars($booking['room_name'] ?? 'N/A') ?></td>
                        <td><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                        <td><?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?></td>
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
                            <a href="<?= BASEURL ?>/bookings/show/<?= $booking['id'] ?>" class="btn">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You haven't made any bookings yet.</p>
        <a href="<?= BASEURL ?>/rooms/index" class="btn">Browse Rooms</a>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= BASEURL ?>/rooms/index" class="btn">Browse Rooms</a>
        <a href="<?= BASEURL ?>/bookings/create" class="btn" style="background: #27ae60;">Book a Room</a>
        <a href="<?= BASEURL ?>/bookings/index" class="btn">View All My Bookings</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
