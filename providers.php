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
    , rstats.avg_rating, rstats.review_count
    FROM users u
    LEFT JOIN offers o ON o.provider_id = u.user_id
    LEFT JOIN service s ON s.service_id = o.service_id
    LEFT JOIN (
        SELECT provider_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
        FROM review
        GROUP BY provider_id
    ) rstats ON rstats.provider_id = u.user_id
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
$provider_map = [];
$provider_ids = [];

if ($providers) {
    while ($row = $providers->fetch_assoc()) {
        $provider_id = (int)$row["user_id"];
        if (!isset($provider_map[$provider_id])) {
            $provider_map[$provider_id] = [
                "user_id" => $provider_id,
                "name" => $row["name"],
                "phone" => $row["phone"],
                "address" => $row["address"],
                "verification_status" => $row["verification_status"],
                "avg_rating" => $row["avg_rating"],
                "review_count" => $row["review_count"],
                "services" => [],
            ];
            $provider_ids[] = $provider_id;
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

$reviews_by_provider = [];
if (is_admin() && !empty($provider_ids)) {
    $placeholders = implode(",", array_fill(0, count($provider_ids), "?"));
    $types = str_repeat("i", count($provider_ids));
    $stmt = db_query(
        $conn,
        "SELECT r.provider_id, r.review_date, r.rating, r.comment, c.name AS customer_name
         FROM review r
         LEFT JOIN users c ON c.user_id = r.customer_id
         WHERE r.provider_id IN ($placeholders)
         ORDER BY r.review_date DESC",
        $types,
        $provider_ids
    );
    if ($stmt) {
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $provider_id = (int)$row["provider_id"];
            if (!isset($reviews_by_provider[$provider_id])) {
                $reviews_by_provider[$provider_id] = [];
            }
            $reviews_by_provider[$provider_id][] = $row;
        }
    }
}
?>
<div class="card">
    <h2>Service Providers</h2>
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
                                        <?php if (is_customer()): ?>
                                            <a href="booking_create.php?provider_id=<?php echo (int)$provider["user_id"]; ?>&service_id=<?php echo (int)$service["service_id"]; ?>">Book</a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No services listed.</p>
                        <?php endif; ?>
                    </div>
                    <p>
                        Rating: <?php echo e($provider["avg_rating"] ? number_format((float)$provider["avg_rating"], 1) : "N/A"); ?>
                        (<?php echo e((int)($provider["review_count"] ?? 0)); ?> reviews)
                    </p>
                    <?php if (is_admin()): ?>
                        <div class="review-list">
                            <strong>Reviews</strong>
                            <?php $provider_reviews = $reviews_by_provider[(int)$provider["user_id"]] ?? []; ?>
                            <?php if (!empty($provider_reviews)): ?>
                                <ul>
                                    <?php foreach (array_slice($provider_reviews, 0, 3) as $review): ?>
                                        <li>
                                            <?php echo e($review["review_date"]); ?> - <?php echo e($review["customer_name"]); ?>:
                                            <?php echo e($review["rating"]); ?>/5 - <?php echo e($review["comment"]); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
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
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No providers found.</p>
    <?php endif; ?>
</div>

