<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Products Catalog</h2>
    
    <!-- Search and Filter Form (demonstrasi input interface) -->
    <form method="GET" action="<?= BASEURL ?>/products/index" style="margin-bottom: 20px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
                <input type="text" name="search" placeholder="Search products..." value="<?= $search_keyword ?? '' ?>">
            </div>
            
            <div class="form-group" style="flex: 1; min-width: 200px; margin: 0;">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= ($current_category == $cat) ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn">Filter</button>
            <a href="<?= BASEURL ?>/products/index" class="btn">Reset</a>
        </div>
    </form>
    
    <?php if($user && $user['role'] === 'admin'): ?>
        <a href="<?= BASEURL ?>/products/create" class="btn" style="margin-bottom: 20px;">Add New Product</a>
    <?php endif; ?>
</div>

<!-- Products Grid (demonstrasi output interface dengan array) -->
<div class="grid">
    <?php if(!empty($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="product-card">
                <?php if($product['image']): ?>
                    <img src="<?= BASEURL ?>/uploads/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                <?php else: ?>
                    <img src="<?= BASEURL ?>/img/no-image.jpg" alt="No Image">
                <?php endif; ?>
                
                <div class="content">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
                    <p><?= substr(htmlspecialchars($product['description'] ?? ''), 0, 100) ?>...</p>
                    <p><strong>Stock:</strong> <?= $product['stock'] ?> available</p>
                    <div class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <a href="<?= BASEURL ?>/products/show/<?= $product['id'] ?>" class="btn">View</a>
                        
                        <?php if($product['stock'] > 0): ?>
                            <form action="<?= BASEURL ?>/orders/addToCart/<?= $product['id'] ?>" method="POST" style="display: inline;">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn">Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <span class="badge badge-danger">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <p>No products found.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
