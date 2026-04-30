<?php
require_once __DIR__ . "/includes/header.php";
require_login();

$service_id = isset($_GET["service_id"]) ? (int)$_GET["service_id"] : 0;

if (is_post() && is_customer()) {
    $provider_id = (int)($_POST["provider_id"] ?? 0);
    $action = $_POST["action"] ?? "";
    if ($provider_id > 0) {
        if ($action === "add") {
            db_query($conn, "INSERT IGNORE INTO favourite (customer_id, provider_id) VALUES (?, ?)", "ii", [$_SESSION["user_id"], $provider_id]);
        } elseif ($action === "remove") {
            db_query($conn, "DELETE FROM favourite WHERE customer_id = ? AND provider_id = ?", "ii", [$_SESSION["user_id"], $provider_id]);
        }
    }
}

$fav_ids = [];
if (is_customer()) {
    $stmt = db_query($conn, "SELECT provider_id FROM favourite WHERE customer_id = ?", "i", [$_SESSION["user_id"]]);
    if ($stmt) {
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $fav_ids[] = (int)$row["provider_id"];
        }
    }
}

$sql = "SELECT u.user_id, u.name, u.phone, u.address, u.verification_status, s.service_id, s.service_category, s.price_per_hour
        FROM users u
        LEFT JOIN offers o ON o.provider_id = u.user_id
        LEFT JOIN service s ON s.service_id = o.service_id
        WHERE u.role = 'provider' AND u.verification_status = 'verified'";

$params = [];
$types = "";
if ($service_id > 0) {
    $sql .= " AND s.service_id = ?";
    $types = "i";
    $params[] = $service_id;
}

$stmt = db_query($conn, $sql, $types, $params);
$providers = $stmt ? $stmt->get_result() : false;
?>
<div class="card">
    <h2>Service Providers</h2>
    <?php if ($providers && $providers->num_rows > 0): ?>
        <div class="grid">
            <?php while ($provider = $providers->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo e($provider["name"]); ?></h3>
                    <p>Phone: <?php echo e($provider["phone"]); ?></p>
                    <p>Address: <?php echo e($provider["address"]); ?></p>
                    <p>Service: <?php echo e($provider["service_category"] ?? "Not assigned"); ?></p>
                    <p>Price/hour: <?php echo e($provider["price_per_hour"] ?? "N/A"); ?></p>
                    <?php if (is_customer()): ?>
                        <form method="post" style="margin-bottom: 10px;">
                            <input type="hidden" name="provider_id" value="<?php echo (int)$provider["user_id"]; ?>">
                            <?php if (in_array((int)$provider["user_id"], $fav_ids, true)): ?>
                                <input type="hidden" name="action" value="remove">
                                <button class="secondary" type="submit">Remove Favorite</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="add">
                                <button type="submit">Add Favorite</button>
                            <?php endif; ?>
                        </form>
                        <?php if (!empty($provider["service_id"])): ?>
                            <a href="booking_create.php?provider_id=<?php echo (int)$provider["user_id"]; ?>&service_id=<?php echo (int)$provider["service_id"]; ?>">Book Service</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No providers found.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
