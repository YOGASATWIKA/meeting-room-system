<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Add New Product</h2>
    <p>Fill in the form to add a new product to the catalog.</p>
</div>

<div class="card">
    <!-- Form Input (demonstrasi interface input dengan berbagai tipe data) -->
    <form action="<?= BASEURL ?>/products/store" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" required placeholder="Enter product name">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Product description"></textarea>
        </div>
        
        <div class="form-group">
            <label for="price">Price (Rp) *</label>
            <input type="number" id="price" name="price" required min="0" max="99999999" step="100" placeholder="e.g., 25000">
            <small>Range: Rp 0 - 99,999,999</small>
        </div>
        
        <div class="form-group">
            <label for="category">Category *</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <option value="Catering Package">Catering Package</option>
                <option value="Snack Box">Snack Box</option>
                <option value="Beverage">Beverage</option>
                <option value="Special Order">Special Order</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="stock">Stock Quantity *</label>
            <input type="number" id="stock" name="stock" required min="0" value="0">
        </div>
        
        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Accepted formats: JPG, PNG. Max size: 2MB</small>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Save Product</button>
            <a href="<?= BASEURL ?>/products/index" class="btn btn-danger">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
