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
}

$stats = [
    "users" => $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"],
    "providers" => $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'provider'")->fetch_assoc()["total"],
    "bookings" => $conn->query("SELECT COUNT(*) AS total FROM booking")->fetch_assoc()["total"],
    "payments" => $conn->query("SELECT COUNT(*) AS total FROM payment")->fetch_assoc()["total"],
];

$users = $conn->query("SELECT user_id, name, email, phone, role, verification_status FROM users ORDER BY user_id DESC");
?>
<div class="card">
    <h2>Admin Dashboard</h2>
    <div class="grid">
        <div class="card">Total Users: <?php echo e($stats["users"]); ?></div>
        <div class="card">Providers: <?php echo e($stats["providers"]); ?></div>
        <div class="card">Bookings: <?php echo e($stats["bookings"]); ?></div>
        <div class="card">Payments: <?php echo e($stats["payments"]); ?></div>
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
                        <?php if ($user["role"] === "provider" && $user["verification_status"] !== "verified"): ?>
                            <form method="post" style="margin-bottom: 6px;">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="user_id" value="<?php echo (int)$user["user_id"]; ?>">
                                <button type="submit">Approve</button>
                            </form>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="action" value="set_role">
                            <input type="hidden" name="user_id" value="<?php echo (int)$user["user_id"]; ?>">
                            <select name="role">
                                <option value="customer" <?php echo $user["role"] === "customer" ? "selected" : ""; ?>>Customer</option>
                                <option value="provider" <?php echo $user["role"] === "provider" ? "selected" : ""; ?>>Provider</option>
                                <option value="admin" <?php echo $user["role"] === "admin" ? "selected" : ""; ?>>Admin</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
