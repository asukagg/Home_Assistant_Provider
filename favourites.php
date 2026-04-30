<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer()) {
    redirect("dashboard.php");
}

if (is_post()) {
    $provider_id = (int)($_POST["provider_id"] ?? 0);
    $action = $_POST["action"] ?? "";
    if ($provider_id > 0 && $action === "remove") {
        db_query($conn, "DELETE FROM favourite WHERE customer_id = ? AND provider_id = ?", "ii", [$_SESSION["user_id"], $provider_id]);
    }
}

$stmt = db_query(
    $conn,
    "SELECT u.user_id, u.name, u.phone, u.address, u.verification_status, s.service_id, s.service_category, s.price_per_hour
     FROM favourite f
     LEFT JOIN users u ON u.user_id = f.provider_id
     LEFT JOIN offers o ON o.provider_id = u.user_id
     LEFT JOIN service s ON s.service_id = o.service_id
     WHERE f.customer_id = ? AND u.role = 'provider' AND u.verification_status = 'verified'",
    "i",
    [$_SESSION["user_id"]]
);
$favourites = $stmt ? $stmt->get_result() : false;
$provider_map = [];

if ($favourites) {
    while ($row = $favourites->fetch_assoc()) {
        $provider_id = (int)$row["user_id"];
        if (!isset($provider_map[$provider_id])) {
            $provider_map[$provider_id] = [
                "user_id" => $provider_id,
                "name" => $row["name"],
                "phone" => $row["phone"],
                "address" => $row["address"],
                "services" => [],
            ];
        }

        if (!empty($row["service_id"])) {
            $provider_map[$provider_id]["services"][] = [
                "service_id" => (int)$row["service_id"],
                "service_category" => $row["service_category"],
                "price_per_hour" => $row["price_per_hour"],
            ];
        }
    }
}
?>
<div class="card">
    <h2>Favourite Providers</h2>
    <?php if (!empty($provider_map)): ?>
        <div class="grid">
            <?php foreach ($provider_map as $provider): ?>
                <div class="card">
                    <h3><?php echo e($provider["name"]); ?></h3>
                    <p>Phone: <?php echo e($provider["phone"]); ?></p>
                    <p>Address: <?php echo e($provider["address"]); ?></p>
                    <div>
                        <strong>Services</strong>
                        <?php if (!empty($provider["services"])): ?>
                            <ul>
                                <?php foreach ($provider["services"] as $service): ?>
                                    <li>
                                        <?php echo e($service["service_category"]); ?>
                                        (<?php echo e($service["price_per_hour"]); ?>)
                                        <a href="booking_create.php?provider_id=<?php echo (int)$provider["user_id"]; ?>&service_id=<?php echo (int)$service["service_id"]; ?>">Book</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No services listed.</p>
                        <?php endif; ?>
                    </div>
                    <form method="post" style="margin-bottom: 10px;">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="provider_id" value="<?php echo (int)$provider["user_id"]; ?>">
                        <button class="secondary" type="submit">Remove Favourite</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No favourites yet.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
