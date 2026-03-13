<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Meeting Rooms</h2>
    <p>Browse and book our meeting rooms</p>
    
    <?php if(isset($user) && $user['role'] === 'admin'): ?>
        <a href="<?= BASEURL ?>/rooms/create" class="btn">+ Add New Room</a>
    <?php endif; ?>
</div>

<!-- Search and Filter -->
<div class="card">
    <form method="GET" action="<?= BASEURL ?>/rooms/index" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Search rooms..." value="<?= htmlspecialchars($search_keyword ?? '') ?>" style="flex: 1; min-width: 200px;">
        <input type="number" name="capacity" placeholder="Min capacity" value="<?= htmlspecialchars($min_capacity ?? '') ?>" style="width: 150px;">
        <button type="submit" class="btn">Search</button>
        <?php if(!empty($search_keyword) || !empty($min_capacity)): ?>
            <a href="<?= BASEURL ?>/rooms/index" class="btn">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Rooms Grid -->
<?php if(!empty($rooms)): ?>
    <div class="grid">
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
                    
                    <?php if(!empty($room['facilities'])): ?>
                        <p><strong>Facilities:</strong> <?= htmlspecialchars(substr($room['facilities'], 0, 100)) ?>...</p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 10px;">
                        <span class="badge badge-<?= $room['status'] === 'active' ? 'success' : ($room['status'] === 'maintenance' ? 'warning' : 'danger') ?>">
                            <?= strtoupper($room['status']) ?>
                        </span>
                    </div>
                    
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <a href="<?= BASEURL ?>/rooms/show/<?= $room['id'] ?>" class="btn">View Details</a>
                        
                        <?php if($room['status'] === 'active'): ?>
                            <a href="<?= BASEURL ?>/bookings/create?room_id=<?= $room['id'] ?>" class="btn" style="background: #27ae60;">Book Now</a>
                        <?php endif; ?>
                        
                        <?php if(isset($user) && $user['role'] === 'admin'): ?>
                            <a href="<?= BASEURL ?>/rooms/edit/<?= $room['id'] ?>" class="btn" style="background: #f39c12;">Edit</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card">
        <p>No rooms found.</p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
