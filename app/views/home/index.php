<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Welcome to <?= APP_NAME ?></h2>
    <p>Professional Room Booking and Catering Management System</p>
</div>

<div class="card">
    <h3>Featured Products</h3>
    <div class="grid">
        <?php if(!empty($featured_products)): ?>
            <?php foreach($featured_products as $product): ?>
                <div class="product-card">
                    <?php if($product['image']): ?>
                        <img src="<?= BASEURL ?>/uploads/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                    <?php else: ?>
                        <img src="<?= BASEURL ?>/img/no-image.jpg" alt="No Image">
                    <?php endif; ?>
                    
                    <div class="content">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p><?= substr(htmlspecialchars($product['description'] ?? ''), 0, 100) ?>...</p>
                        <div class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
                        <a href="<?= BASEURL ?>/products/show/<?= $product['id'] ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available at the moment.</p>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="<?= BASEURL ?>/products/index" class="btn">View All Products</a>
    </div>
</div>

<div class="card">
    <h3>Our Services</h3>
    <div class="grid">
        <div class="card">
            <h4>🍱 Catering Services</h4>
            <p>Professional catering for all your events. From small meetings to large conferences.</p>
        </div>
        <div class="card">
            <h4>🏢 Room Booking</h4>
            <p>Book meeting rooms, conference halls, and training facilities with ease.</p>
        </div>
        <div class="card">
            <h4>📊 Management Dashboard</h4>
            <p>Track your orders, bookings, and manage your account from one place.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
