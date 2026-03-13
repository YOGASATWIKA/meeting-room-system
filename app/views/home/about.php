<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="card">
    <h2>About <?= htmlspecialchars($company_info['name']) ?></h2>
    <p style="font-size: 16px; line-height: 1.6;">
        <?= htmlspecialchars($company_info['description']) ?>
    </p>
</div>

<div class="card">
    <h3>System Information</h3>
    <table>
        <tr>
            <td><strong>Application Name:</strong></td>
            <td><?= htmlspecialchars($company_info['name']) ?></td>
        </tr>
        <tr>
            <td><strong>Version:</strong></td>
            <td><?= htmlspecialchars($company_info['version']) ?></td>
        </tr>
        <tr>
            <td><strong>Release Date:</strong></td>
            <td>March 2026</td>
        </tr>
    </table>
</div>

<div class="card">
    <h3>Key Features</h3>
    <div class="grid">
        <?php foreach($company_info['features'] as $feature): ?>
            <div class="card">
                <h4>✓ <?= htmlspecialchars($feature) ?></h4>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <h3>Technologies Used</h3>
    <ul style="list-style: none; padding: 20px;">
        <li style="margin: 10px 0;">✓ PHP 8+ with OOP Principles</li>
        <li style="margin: 10px 0;">✓ MySQL Database</li>
        <li style="margin: 10px 0;">✓ MVC Architecture Pattern</li>
        <li style="margin: 10px 0;">✓ Front Controller Pattern</li>
        <li style="margin: 10px 0;">✓ Dependency Injection</li>
        <li style="margin: 10px 0;">✓ Service Layer Pattern</li>
        <li style="margin: 10px 0;">✓ Room Availability Management</li>
    </ul>
</div>

<div class="card">
    <h3>Contact Information</h3>
    <p>For support or inquiries, please contact us through the system administrator.</p>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
