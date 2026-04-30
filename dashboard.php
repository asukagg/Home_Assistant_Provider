<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_admin()) {
    redirect("dashboard.php");
}

if (is_post()) {
    $action = $_POST["action"] ?? "";
    $user_id = (int)($_POST["user_id"] ?? 0);

    if ($user_id > 0 && $action === "approve") {
        db_query($conn, "UPDATE users SET verification_status = 'verified' WHERE user_id = ?", "i", [$user_id]);
        add_notification($conn, $user_id, "Your provider account was approved.");
    }

    if ($user_id > 0 && $action === "set_role") {
        $role = $_POST["role"] ?? "customer";
        if (in_array($role, ["customer", "provider", "admin"], true)) {
            $flag = $role === "admin" ? 1 : 0;
            db_query($conn, "UPDATE users SET role = ?, flag = ? WHERE user_id = ?", "sii", [$role, $flag, $user_id]);
        }
    }

    if ($user_id > 0 && $action === "delete_user") {
        db_query($conn, "DELETE FROM users WHERE user_id = ?", "i", [$user_id]);
    }
}

$payment_total = (float)($conn->query("SELECT SUM(amount) AS total FROM payment")->fetch_assoc()["total"] ?? 0);
$payment_count = (int)($conn->query("SELECT COUNT(*) AS total FROM payment")->fetch_assoc()["total"] ?? 0);

$customer_count = (int)($conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'")->fetch_assoc()["total"] ?? 0);
$provider_count = (int)($conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'provider'")->fetch_assoc()["total"] ?? 0);
$booking_count = (int)($conn->query("SELECT COUNT(*) AS total FROM booking")->fetch_assoc()["total"] ?? 0);

$stats = [
    "customers" => $customer_count,
    "providers" => $provider_count,
    "bookings" => $booking_count,
    "payments" => $payment_count,
];

$pie_total = $customer_count + $provider_count;
$customer_pct = $pie_total > 0 ? (int)round(($customer_count / $pie_total) * 100) : 0;
$provider_pct = $pie_total > 0 ? 100 - $customer_pct : 0;

$customer_deg = $pie_total > 0 ? ($customer_count / $pie_total) * 360 : 0;
$provider_deg = $pie_total > 0 ? 360 - $customer_deg : 0;

$customer_end = $customer_deg;
$provider_end = $customer_end + $provider_deg;

$max_stat = max(1, (int)max($stats));
$users = $conn->query("SELECT user_id, name, email, phone, role, verification_status, nid_file FROM users ORDER BY user_id DESC");
?>
<div class="card">
    <h2>Admin Dashboard</h2>
    <div class="grid">
        <div class="card">Customers: <?php echo e($stats["customers"]); ?></div>
        <div class="card">Providers: <?php echo e($stats["providers"]); ?></div>
        <div class="card">Bookings: <?php echo e($stats["bookings"]); ?></div>
        <div class="card">Payments: <?php echo e(number_format($payment_total, 2)); ?></div>
    </div>
    <div class="chart-grid">
        <div class="chart-card">
            <h3>Overview (Users)</h3>
            <div class="pie-chart" style="background: conic-gradient(
                var(--chart-1) 0deg <?php echo $customer_end; ?>deg,
                var(--chart-2) <?php echo $customer_end; ?>deg <?php echo $provider_end; ?>deg
            );">
            </div>
            <div class="pie-legend">
                <div class="legend-item"><span class="legend-swatch" style="background: var(--chart-1);"></span>Customers <?php echo e($customer_pct); ?>%</div>
                <div class="legend-item"><span class="legend-swatch" style="background: var(--chart-2);"></span>Providers <?php echo e($provider_pct); ?>%</div>
            </div>
        </div>
        <div class="chart-card">
            <h3>Totals (Histogram)</h3>
            <div class="histogram">
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["customers"] / $max_stat) * 100); ?>%;"><span>Customers<br><?php echo e($stats["customers"]); ?></span></div>
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["providers"] / $max_stat) * 100); ?>%;"><span>Providers<br><?php echo e($stats["providers"]); ?></span></div>
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["bookings"] / $max_stat) * 100); ?>%;"><span>Bookings<br><?php echo e($stats["bookings"]); ?></span></div>
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["payments"] / $max_stat) * 100); ?>%;"><span>Payments<br><?php echo e($stats["payments"]); ?></span></div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <h3>User Management</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Verification</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($user["name"]); ?></td>
                    <td><?php echo e($user["email"]); ?></td>
                    <td><?php echo e($user["phone"]); ?></td>
                    <td><?php echo e($user["role"]); ?></td>
                    <td><?php echo e($user["verification_status"]); ?></td>
                    <td>
                        <?php if ($user["role"] === "provider" && !empty($user["nid_file"])): ?>
                            <a class="button-link" href="<?php echo e($user["nid_file"]); ?>" target="_blank" rel="noopener">View NID</a>
                        <?php endif; ?>
                        <?php if ($user["role"] === "provider" && $user["verification_status"] !== "verified"): ?>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="user_id" value="<?php echo (int)$user["user_id"]; ?>">
                                <button type="submit">Approve</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" class="inline-form">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?php echo (int)$user["user_id"]; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

