<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Welcome to <?= APP_NAME ?></h2>
    <p>Professional Meeting Room Booking System</p>
</div>

<div class="card">
    <h3>Available Rooms</h3>
    <div class="grid">
        <?php if(!empty($rooms)): ?>
            <?php foreach($rooms as $room): ?>
                <div class="card">
                    <?php if($room['image']): ?>
                        <img src="<?= BASEURL ?>/uploads/rooms/<?= $room['image'] ?>" alt="<?= $room['name'] ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px 8px 0 0;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: #ecf0f1; display: flex; align-items: center; justify-content: center; border-radius: 8px 8px 0 0;">
                            <span style="font-size: 48px;">🏢</span>
                        </div>
                    <?php endif; ?>
                    
                    <div style="padding: 15px;">
                        <h3><?= htmlspecialchars($room['name']) ?></h3>
                        <p><strong>Capacity:</strong> <?= $room['capacity'] ?> people</p>
                        <p><strong>Price:</strong> Rp <?= number_format($room['price_per_hour'], 0, ',', '.') ?>/hour</p>
                        
                        <div style="margin-top: 10px;">
                            <span class="badge badge-<?= $room['status'] === 'active' ? 'success' : 'warning' ?>">
                                <?= strtoupper($room['status']) ?>
                            </span>
                        </div>
                        
                        <div style="margin-top: 15px;">
                            <a href="<?= BASEURL ?>/rooms/show/<?= $room['id'] ?>" class="btn">View Details</a>
                            <?php if($room['status'] === 'active'): ?>
                                <a href="<?= BASEURL ?>/bookings/create?room_id=<?= $room['id'] ?>" class="btn" style="background: #27ae60;">Book Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No rooms available at the moment.</p>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="<?= BASEURL ?>/rooms/index" class="btn">View All Rooms</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
