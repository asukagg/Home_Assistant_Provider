<?php
require_once __DIR__ . "/includes/header.php";
require_login();

$is_admin_user = is_admin();

if (is_post() && $is_admin_user) {
    $action = $_POST["action"] ?? "";
    if ($action === "update_service") {
        $service_id = (int)($_POST["service_id"] ?? 0);
        $price = (float)($_POST["price_per_hour"] ?? 0);
        if ($service_id > 0 && $price >= 0) {
            db_query($conn, "UPDATE service SET price_per_hour = ? WHERE service_id = ?", "di", [$price, $service_id]);
        }
    }
}

$result = $conn->query("SELECT * FROM service ORDER BY service_category");
?>
<div class="card">
    <h2>Service Categories</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Price / Hour</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($service = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($service["service_category"]); ?></td>
                    <td>
                        <?php if ($is_admin_user): ?>
                            <form method="post" class="inline-form">
                                <input type="hidden" name="action" value="update_service">
                                <input type="hidden" name="service_id" value="<?php echo (int)$service["service_id"]; ?>">
                                <input type="number" name="price_per_hour" step="0.01" min="0" value="<?php echo e($service["price_per_hour"]); ?>">
                                <button type="submit">Save</button>
                            </form>
                        <?php else: ?>
                            <?php echo e($service["price_per_hour"]); ?>
                        <?php endif; ?>
                    </td>
                    <td><a href="providers.php?service_id=<?php echo (int)$service["service_id"]; ?>">View Providers</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

