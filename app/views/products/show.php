<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
        <!-- Product Image -->
        <div>
            <?php if($product['image']): ?>
                <img src="<?= BASEURL ?>/uploads/products/<?= $product['image'] ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     style="width: 100%; border-radius: 8px;">
            <?php else: ?>
                <div style="width: 100%; height: 400px; background: #ddd; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <p>No Image Available</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Info -->
        <div>
            <div style="margin-bottom: 20px;">
                <h4>Category</h4>
                <p><?= htmlspecialchars($product['category']) ?></p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h4>Description</h4>
                <p><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available')) ?></p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h4>Price</h4>
                <p class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h4>Stock</h4>
                <?php if($product['stock'] > 0): ?>
                    <p><?= $product['stock'] ?> available</p>
                <?php else: ?>
                    <p class="badge badge-danger">Out of Stock</p>
                <?php endif; ?>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h4>Status</h4>
                <span class="badge <?= $product['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                    <?= ucfirst($product['status']) ?>
                </span>
            </div>
            
            <!-- Actions -->
            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <?php if($product['stock'] > 0 && $product['status'] === 'active'): ?>
                    <form action="<?= BASEURL ?>/orders/addToCart/<?= $product['id'] ?>" method="POST" style="display: inline;">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" 
                               style="width: 80px; padding: 8px; margin-right: 10px;">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                <?php endif; ?>
                
                <?php if($user && $user['role'] === 'admin'): ?>
                    <a href="<?= BASEURL ?>/products/edit/<?= $product['id'] ?>" class="btn">Edit</a>
                    <a href="<?= BASEURL ?>/products/delete/<?= $product['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                <?php endif; ?>
                
                <a href="<?= BASEURL ?>/products/index" class="btn">Back to Products</a>
            </div>
        </div>
    </div>
</div>

<!-- Related Products (if any) -->
<div class="card">
    <h3>Related Products</h3>
    <p>Browse more products in the <strong><?= htmlspecialchars($product['category']) ?></strong> category</p>
    <a href="<?= BASEURL ?>/products/index?category=<?= urlencode($product['category']) ?>" class="btn">
        View <?= htmlspecialchars($product['category']) ?> Products
    </a>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
