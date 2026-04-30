<?php
require_once __DIR__ . "/auth.php";
$user = current_user($conn);
$flash = get_flash();

// detect current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Assistant Provider</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php if ($current_page !== 'index.php'): ?>
<header class="site-header">
    <div class="container">
        <h1 class="logo">Home Assistant Provider</h1>

        <nav class="nav">
            <div class="nav-left">
                <button type="button" class="back-button" onclick="history.back()">く</button>
                <!-- <a href="index.php">Home</a> -->

                <?php if ($user): ?>
                    <?php if (is_admin()): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="services.php">Services</a>
                    <a href="providers.php">Providers</a>
                    <?php if (is_customer()): ?>
                    <a href="notifications.php">Notifications</a>
                    <?php endif; ?>
                    <?php if (is_customer()): ?>
                        <a href="bookings.php">My Bookings</a>
                    <?php endif; ?>
                    <?php if (is_customer()): ?>
                    <a href="favourites.php">Favourites</a>
                    <?php endif; ?>
                    <?php if (is_provider()): ?>
                        <a href="provider_profile.php">My Profile</a>
                        <a href="provider_bookings.php">Requests</a>
                        <a href="notifications.php">Notifications</a>
                        
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="nav-right">
                <?php if ($user): ?>
                    <?php if (!is_admin()): ?>
                        <a href="history.php">History</a>
                    <?php endif; ?>
                
                    <a href="logout.php">Logout (<?php echo e($user["name"] ?? "User"); ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>

    </div>
</header>

<main class="container">
    <?php if (!empty($flash)): ?>
        <div class="flash">
            <?php foreach ($flash as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="flash-item flash-<?php echo e($type); ?>">
                        <?php echo e($message); ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>
