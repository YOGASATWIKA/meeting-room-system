<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Admin Dashboard</h2>
    <p>Welcome back, <strong><?= htmlspecialchars($user['full_name']) ?></strong></p>
</div>

<div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
    <div class="card" style="background: #3498db; color: white;">
        <h3><?= $statistics['total_bookings'] ?? 0 ?></h3>
        <p>Total Bookings</p>
    </div>
    <div class="card" style="background: #2ecc71; color: white;">
        <h3>Rp <?= number_format($statistics['total_revenue'] ?? 0, 0, ',', '.') ?></h3>
        <p>Total Revenue</p>
    </div>
    <div class="card" style="background: #f39c12; color: white;">
        <h3><?= $statistics['pending_bookings'] ?? 0 ?></h3>
        <p>Pending Bookings</p>
    </div>
    <div class="card" style="background: #9b59b6; color: white;">
        <h3><?= $total_rooms ?? 0 ?></h3>
        <p>Total Rooms</p>
    </div>
</div>

<div class="card">
    <h3>Upcoming Bookings</h3>
    <?php if(!empty($upcoming_bookings)): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking Number</th>
                    <th>Customer</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($upcoming_bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['booking_number']) ?></td>
                        <td><?= htmlspecialchars($booking['user_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($booking['room_name'] ?? 'N/A') ?></td>
                        <td><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                        <td><?= date('H:i', strtotime($booking['start_time'])) ?> - <?= date('H:i', strtotime($booking['end_time'])) ?></td>
                        <td><span class="badge badge-<?= $booking['status'] === 'confirmed' ? 'success' : 'warning' ?>"><?= strtoupper($booking['status']) ?></span></td>
                        <td>
                            <a href="<?= BASEURL ?>/bookings/show/<?= $booking['id'] ?>" class="btn">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No upcoming bookings.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Quick Actions</h3>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?= BASEURL ?>/rooms/create" class="btn">+ Add New Room</a>
        <a href="<?= BASEURL ?>/rooms/index" class="btn">Manage Rooms</a>
        <a href="<?= BASEURL ?>/bookings/index" class="btn">View All Bookings</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
