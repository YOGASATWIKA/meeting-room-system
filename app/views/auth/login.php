<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Login to Your Account</h2>
    <p>Please enter your credentials to access the system.</p>
</div>

<div class="card">
    <form action="<?= BASEURL ?>/auth/processLogin" method="POST">
        <div class="form-group">
            <label for="username">Username or Email *</label>
            <input type="text" id="username" name="username" required placeholder="Enter username or email">
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required placeholder="Enter password">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Login</button>
        </div>
        
        <p>Don't have an account? <a href="<?= BASEURL ?>/auth/register">Register here</a></p>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
