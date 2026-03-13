<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Edit Room</h2>
    <a href="<?= BASEURL ?>/rooms/show/<?= $room['id'] ?>" class="btn">← Back to Room</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASEURL ?>/rooms/update/<?= $room['id'] ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Room Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($room['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="capacity">Capacity (people) *</label>
            <input type="number" id="capacity" name="capacity" value="<?= $room['capacity'] ?>" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="price_per_hour">Price per Hour (Rp) *</label>
            <input type="number" id="price_per_hour" name="price_per_hour" value="<?= $room['price_per_hour'] ?>" min="0" max="99999999" step="1000" required>
            <small>Range: Rp 0 - 99,999,999</small>
        </div>
        
        <div class="form-group">
            <label for="facilities">Facilities</label>
            <textarea id="facilities" name="facilities" rows="3"><?= htmlspecialchars($room['facilities'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?= htmlspecialchars($room['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="image">Room Image</label>
            <?php if(!empty($room['image'])): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= BASEURL ?>/uploads/rooms/<?= $room['image'] ?>" alt="Current image" style="max-width: 200px; border-radius: 8px;">
                    <p><small>Current image</small></p>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Max size: 2MB. Leave empty to keep current image.</small>
        </div>
        
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="active" <?= $room['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $room['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Update Room</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
