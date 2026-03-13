<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Edit Product</h2>
    <p>Update product information</p>
</div>

<div class="card">
    <form action="<?= BASEURL ?>/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required placeholder="Enter product name">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Product description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">Price (Rp) *</label>
            <input type="number" id="price" name="price" value="<?= $product['price'] ?>" required min="0" max="99999999" step="100">
            <small>Range: Rp 0 - 99,999,999</small>
        </div>
        
        <div class="form-group">
            <label for="category">Category *</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="Catering Package" <?= $product['category'] === 'Catering Package' ? 'selected' : '' ?>>Catering Package</option>
                <option value="Snack Box" <?= $product['category'] === 'Snack Box' ? 'selected' : '' ?>>Snack Box</option>
                <option value="Beverage" <?= $product['category'] === 'Beverage' ? 'selected' : '' ?>>Beverage</option>
                <option value="Special Order" <?= $product['category'] === 'Special Order' ? 'selected' : '' ?>>Special Order</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="stock">Stock Quantity *</label>
            <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?>" required min="0">
        </div>
        
        <div class="form-group">
            <label for="image">Product Image</label>
            <?php if(!empty($product['image'])): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?= BASEURL ?>/uploads/products/<?= $product['image'] ?>" alt="Current image" style="max-width: 200px; border-radius: 8px;">
                    <p><small>Current image</small></p>
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Accepted formats: JPG, PNG. Max size: 2MB. Leave empty to keep current image.</small>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Update Product</button>
            <a href="<?= BASEURL ?>/products/show/<?= $product['id'] ?>" class="btn btn-danger">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
