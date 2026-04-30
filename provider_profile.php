<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_provider()) {
    redirect("dashboard.php");
}

$user = current_user($conn);

if (is_post()) {
    $action = $_POST["action"] ?? "";

    if ($action === "update_profile") {
        $phone = trim($_POST["phone"] ?? "");
        $address = trim($_POST["address"] ?? "");
        $nid_number = trim($_POST["nid_number"] ?? "");

        db_query($conn, "UPDATE users SET phone = ?, address = ?, nid_card = ? WHERE user_id = ?", "sssi", [$phone, $address, $nid_number, $_SESSION["user_id"]]);
        set_flash("success", "Profile updated.");
        redirect("provider_profile.php");
    }

    if ($action === "add_service") {
        $service_id = (int)($_POST["service_id"] ?? 0);
        if ($service_id > 0) {
            db_query($conn, "INSERT IGNORE INTO offers (service_id, provider_id) VALUES (?, ?)", "ii", [$service_id, $_SESSION["user_id"]]);
            set_flash("success", "Service added.");
            redirect("provider_profile.php");
        }
    }

    if ($action === "remove_service") {
        $service_id = (int)($_POST["service_id"] ?? 0);
        if ($service_id > 0) {
            db_query($conn, "DELETE FROM offers WHERE service_id = ? AND provider_id = ?", "ii", [$service_id, $_SESSION["user_id"]]);
            set_flash("success", "Service removed.");
            redirect("provider_profile.php");
        }
    }
}

$services = $conn->query("SELECT * FROM service ORDER BY service_category");
$stmt = db_query(
    $conn,
    "SELECT s.service_id, s.service_category, s.price_per_hour
     FROM offers o
     LEFT JOIN service s ON s.service_id = o.service_id
     WHERE o.provider_id = ?",
    "i",
    [$_SESSION["user_id"]]
);
$my_services = $stmt ? $stmt->get_result() : false;

$stmt = db_query(
    $conn,
    "SELECT r.review_date, r.rating, r.comment, c.name AS customer_name
     FROM review r
     LEFT JOIN users c ON c.user_id = r.customer_id
     WHERE r.provider_id = ?
     ORDER BY r.review_date DESC",
    "i",
    [$_SESSION["user_id"]]
);
$reviews = $stmt ? $stmt->get_result() : false;
?>
<div class="grid">
    <div class="card">
        <h2>Provider Profile</h2>
        <form method="post">
            <input type="hidden" name="action" value="update_profile">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo e($user["phone"] ?? ""); ?>">
            <label>Address</label>
            <textarea name="address" rows="3"><?php echo e($user["address"] ?? ""); ?></textarea>
            <label>NID Number</label>
            <input type="text" name="nid_number" value="<?php echo e($user["nid_card"] ?? ""); ?>">
            <button type="submit">Update Profile</button>
        </form>
        <p>Verification: <span class="badge"><?php echo e($user["verification_status"] ?? "pending"); ?></span></p>
    </div>
    <div class="card">
        <h2>My Services</h2>
        <form method="post">
            <input type="hidden" name="action" value="add_service">
            <label>Add Service</label>
            <select name="service_id">
                <?php while ($service = $services->fetch_assoc()): ?>
                    <option value="<?php echo (int)$service["service_id"]; ?>">
                        <?php echo e($service["service_category"]); ?> (<?php echo e($service["price_per_hour"]); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Add</button>
        </form>
        <h3>Current Services</h3>
        <?php if ($my_services && $my_services->num_rows > 0): ?>
            <ul>
                <?php while ($service = $my_services->fetch_assoc()): ?>
                    <li>
                        <?php echo e($service["service_category"]); ?> (<?php echo e($service["price_per_hour"]); ?>)
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="remove_service">
                            <input type="hidden" name="service_id" value="<?php echo (int)$service["service_id"]; ?>">
                            <button class="secondary" type="submit">Remove</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No services added yet.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h2>Customer Reviews</h2>
    <?php if ($reviews && $reviews->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo e($review["review_date"]); ?></td>
                        <td><?php echo e($review["customer_name"]); ?></td>
                        <td><?php echo e($review["rating"]); ?></td>
                        <td><?php echo e($review["comment"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
</div>

