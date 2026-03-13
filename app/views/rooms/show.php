<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <a href="<?= BASEURL ?>/rooms/index" class="btn">← Back to Rooms</a>
    <h2><?= htmlspecialchars($room['name']) ?></h2>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr;">
    <div class="card">
        <?php if($room['image']): ?>
            <img src="<?= BASEURL ?>/uploads/rooms/<?= $room['image'] ?>" alt="<?= $room['name'] ?>" style="width: 100%; border-radius: 8px;">
        <?php else: ?>
            <div style="width: 100%; height: 300px; background: #ecf0f1; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                <span style="font-size: 100px;">🏢</span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h3>Room Information</h3>
        <table>
            <tr>
                <td><strong>Name:</strong></td>
                <td><?= htmlspecialchars($room['name']) ?></td>
            </tr>
            <tr>
                <td><strong>Capacity:</strong></td>
                <td><?= $room['capacity'] ?> people</td>
            </tr>
            <tr>
                <td><strong>Price per Hour:</strong></td>
                <td>Rp <?= number_format($room['price_per_hour'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>
                    <span class="badge badge-<?= $room['status'] === 'active' ? 'success' : ($room['status'] === 'maintenance' ? 'warning' : 'danger') ?>">
                        <?= strtoupper($room['status']) ?>
                    </span>
                </td>
            </tr>
        </table>
        
        <?php if($room['status'] === 'active'): ?>
            <div style="margin-top: 20px;">
                <a href="<?= BASEURL ?>/bookings/create?room_id=<?= $room['id'] ?>" class="btn" style="background: #27ae60; width: 100%; text-align: center;">Book This Room</a>
            </div>
        <?php endif; ?>
        
        <?php if(isset($user) && $user['role'] === 'admin'): ?>
            <div style="margin-top: 10px; display: flex; gap: 10px;">
                <a href="<?= BASEURL ?>/rooms/edit/<?= $room['id'] ?>" class="btn" style="flex: 1;">Edit</a>
                <a href="<?= BASEURL ?>/rooms/delete/<?= $room['id'] ?>" class="btn btn-danger" style="flex: 1;" onclick="return confirm('Are you sure you want to delete this room?')">Delete</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h3>Facilities</h3>
    <?php if(!empty($room['facilities'])): ?>
        <p><?= nl2br(htmlspecialchars($room['facilities'])) ?></p>
    <?php else: ?>
        <p>No facilities information available.</p>
    <?php endif; ?>
</div>

<?php if(!empty($room['description'])): ?>
<div class="card">
    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($room['description'])) ?></p>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
