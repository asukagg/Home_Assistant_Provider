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

$stats = [
    "users" => (int)($conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"] ?? 0),
    "providers" => (int)($conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'provider'")->fetch_assoc()["total"] ?? 0),
    "bookings" => (int)($conn->query("SELECT COUNT(*) AS total FROM booking")->fetch_assoc()["total"] ?? 0),
    "payments" => $payment_count,
];

$total_stats = max(1, array_sum($stats));
$users_deg = ($stats["users"] / $total_stats) * 360;
$providers_deg = ($stats["providers"] / $total_stats) * 360;
$bookings_deg = ($stats["bookings"] / $total_stats) * 360;
$payments_deg = ($stats["payments"] / $total_stats) * 360;

$users_end = $users_deg;
$providers_end = $users_end + $providers_deg;
$bookings_end = $providers_end + $bookings_deg;
$payments_end = $bookings_end + $payments_deg;

$max_stat = max(1, (int)max($stats));
$users = $conn->query("SELECT user_id, name, email, phone, role, verification_status, nid_file FROM users ORDER BY user_id DESC");
?>
<div class="card">
    <h2>Admin Dashboard</h2>
    <div class="grid">
        <div class="card">Total Users: <?php echo e($stats["users"]); ?></div>
        <div class="card">Providers: <?php echo e($stats["providers"]); ?></div>
        <div class="card">Bookings: <?php echo e($stats["bookings"]); ?></div>
        <div class="card">Payments: <?php echo e(number_format($payment_total, 2)); ?></div>
    </div>
    <div class="chart-grid">
        <div class="chart-card">
            <h3>Overview (Pie)</h3>
            <div class="pie-chart" style="background: conic-gradient(
                var(--chart-1) 0deg <?php echo $users_end; ?>deg,
                var(--chart-2) <?php echo $users_end; ?>deg <?php echo $providers_end; ?>deg,
                var(--chart-3) <?php echo $providers_end; ?>deg <?php echo $bookings_end; ?>deg,
                var(--chart-4) <?php echo $bookings_end; ?>deg <?php echo $payments_end; ?>deg
            );"></div>
            <div class="pie-legend">
                <div class="legend-item"><span class="legend-swatch" style="background: var(--chart-1);"></span>Users</div>
                <div class="legend-item"><span class="legend-swatch" style="background: var(--chart-2);"></span>Providers</div>
                <div class="legend-item"><span class="legend-swatch" style="background: var(--chart-3);"></span>Bookings</div>
                <div class="legend-item"><span class="legend-swatch" style="background: var(--chart-4);"></span>Payments</div>
            </div>
        </div>
        <div class="chart-card">
            <h3>Totals (Histogram)</h3>
            <div class="histogram">
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["users"] / $max_stat) * 100); ?>%;"><span>Users</span></div>
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["providers"] / $max_stat) * 100); ?>%;"><span>Providers</span></div>
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["bookings"] / $max_stat) * 100); ?>%;"><span>Bookings</span></div>
                <div class="histogram-bar" style="height: <?php echo (int)(($stats["payments"] / $max_stat) * 100); ?>%;"><span>Payments</span></div>
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

