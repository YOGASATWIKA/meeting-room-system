<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Add New Room</h2>
    <a href="<?= BASEURL ?>/rooms/index" class="btn">← Back to Rooms</a>
</div>

<div class="card">
    <form method="POST" action="<?= BASEURL ?>/rooms/store" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Room Name *</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="capacity">Capacity (people) *</label>
            <input type="number" id="capacity" name="capacity" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="price_per_hour">Price per Hour (Rp) *</label>
            <input type="number" id="price_per_hour" name="price_per_hour" min="0" max="99999999" step="1000" required placeholder="e.g., 100000">
            <small>Range: Rp 0 - 99,999,999</small>
        </div>
        
        <div class="form-group">
            <label for="facilities">Facilities</label>
            <textarea id="facilities" name="facilities" rows="3" placeholder="e.g., Projector, Whiteboard, AC, WiFi"></textarea>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label for="image">Room Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Max size: 2MB. Formats: JPG, PNG</small>
        </div>
        
        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Create Room</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
