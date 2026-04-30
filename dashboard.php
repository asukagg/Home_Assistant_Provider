<?php
require_once __DIR__ . "/includes/header.php";
require_login();
$user = current_user($conn);
?>
<div class="card">
    <h2>Hello, <?php echo e($user["name"] ?? "User"); ?></h2>
    <p>Role: <span class="badge"><?php echo e($_SESSION["role"] ?? "customer"); ?></span></p>
    <?php if (is_provider() && ($user["verification_status"] ?? "") !== "verified"): ?>
        <p class="badge">Provider verification pending</p>
    <?php endif; ?>
</div>
<div class="grid">
    <div class="card">
        <h3>Browse Services</h3>
        <p>Find available service categories and prices.</p>
        <a href="services.php">View Services</a>
    </div>
    <div class="card">
        <h3>Providers</h3>
        <p>Find verified service providers.</p>
        <a href="providers.php">View Providers</a>
    </div>
    <?php if (is_customer()): ?>
        <div class="card">
            <h3>My Bookings</h3>
            <p>Track your requests and status.</p>
            <a href="bookings.php">Manage Bookings</a>
        </div>
        <div class="card">
            <h3>History</h3>
            <p>See completed services.</p>
            <a href="history.php">View History</a>
        </div>
    <?php endif; ?>
    <?php if (is_provider()): ?>
        <div class="card">
            <h3>Requests</h3>
            <p>Accept or reject booking requests.</p>
            <a href="provider_bookings.php">View Requests</a>
        </div>
        <div class="card">
            <h3>My Profile</h3>
            <p>Update provider details and services.</p>
            <a href="provider_profile.php">Edit Profile</a>
        </div>
    <?php endif; ?>
    <?php if (is_admin()): ?>
        <div class="card">
            <h3>Admin Dashboard</h3>
            <p>Manage users and approvals.</p>
            <a href="admin.php">Open Admin</a>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
