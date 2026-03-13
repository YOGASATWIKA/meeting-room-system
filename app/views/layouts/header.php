<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Room Management System' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        header { background: #2c3e50; color: white; padding: 20px 0; margin-bottom: 30px; }
        header h1 { text-align: center; }
        nav { background: #34495e; padding: 15px 0; margin-bottom: 20px; }
        nav ul { list-style: none; display: flex; justify-content: center; gap: 30px; }
        nav a { color: white; text-decoration: none; padding: 10px 20px; display: block; }
        nav a:hover { background: #2c3e50; border-radius: 5px; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .flash { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; }
        .product-card .content { padding: 15px; }
        .product-card h3 { margin-bottom: 10px; }
        .product-card .price { font-size: 20px; color: #27ae60; font-weight: bold; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; background: white; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #34495e; color: white; }
        table tr:hover { background: #f5f5f5; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header>
        <h1><?= APP_NAME ?></h1>
    </header>
    
    <nav>
        <ul>
            <li><a href="<?= BASEURL ?>/home/index">Home</a></li>
            <li><a href="<?= BASEURL ?>/rooms/index">Rooms</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="<?= BASEURL ?>/home/dashboard">Dashboard</a></li>
                <li><a href="<?= BASEURL ?>/bookings/index">My Bookings</a></li>
                <li><a href="<?= BASEURL ?>/auth/logout">Logout (<?= $_SESSION['full_name'] ?>)</a></li>
            <?php else: ?>
                <li><a href="<?= BASEURL ?>/auth/login">Login</a></li>
                <li><a href="<?= BASEURL ?>/auth/register">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="container">
        <?php if(isset($_SESSION['flash'])): ?>
            <div class="flash <?= $_SESSION['flash']['type'] ?>">
                <?= $_SESSION['flash']['message'] ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
