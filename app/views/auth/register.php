<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>Create New Account</h2>
    <p>Fill in the form below to register.</p>
</div>

<div class="card">
    <form action="<?= BASEURL ?>/auth/processRegister" method="POST">
        <div class="form-group">
            <label for="username">Username *</label>
            <input type="text" id="username" name="username" required placeholder="Choose a username" minlength="4">
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required placeholder="your.email@example.com">
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" required placeholder="Your full name">
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="08xxxxxxxxxx">
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required placeholder="At least 6 characters" minlength="6">
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter password">
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Register</button>
        </div>
        
        <p>Already have an account? <a href="<?= BASEURL ?>/auth/login">Login here</a></p>
    </form>
</div>

<script>
// Client-side validation untuk password match (demonstrasi JavaScript)
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if(password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
