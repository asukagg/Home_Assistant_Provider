<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_provider()) {
    redirect("dashboard.php");
}

if (is_post()) {
    $booking_id = (int)($_POST["booking_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($booking_id > 0) {
        $stmt = db_query($conn, "SELECT customer_id FROM booking WHERE booking_id = ? AND provider_id = ?", "ii", [$booking_id, $_SESSION["user_id"]]);
        $row = $stmt ? $stmt->get_result()->fetch_assoc() : null;
        if ($row) {
            $customer_id = (int)$row["customer_id"];
            if ($action === "accept") {
                db_query($conn, "UPDATE booking SET booking_status = 'confirmed' WHERE booking_id = ?", "i", [$booking_id]);
                add_notification($conn, $customer_id, "Your booking was accepted.");
            } elseif ($action === "reject") {
                db_query($conn, "UPDATE booking SET booking_status = 'rejected' WHERE booking_id = ?", "i", [$booking_id]);
                add_notification($conn, $customer_id, "Your booking was rejected.");
            } elseif ($action === "complete") {
                db_query($conn, "UPDATE booking SET booking_status = 'completed' WHERE booking_id = ?", "i", [$booking_id]);
                add_notification($conn, $customer_id, "Service completed. Please leave a review.");
                db_query($conn, "INSERT IGNORE INTO history (booking_id, user_id) VALUES (?, ?)", "ii", [$booking_id, $customer_id]);
                db_query($conn, "INSERT IGNORE INTO history (booking_id, user_id) VALUES (?, ?)", "ii", [$booking_id, $_SESSION["user_id"]]);
            }
        }
    }
}

$stmt = db_query(
    $conn,
    "SELECT b.*, s.service_category, u.name AS customer_name, u.phone AS customer_phone, u.address AS customer_address
     FROM booking b
     LEFT JOIN service s ON s.service_id = b.service_id
     LEFT JOIN users u ON u.user_id = b.customer_id
     WHERE b.provider_id = ?
     ORDER BY b.booking_id DESC",
    "i",
    [$_SESSION["user_id"]]
);
$bookings = $stmt ? $stmt->get_result() : false;
?>
<div class="card">
    <h2>Booking Requests</h2>
    <?php if ($bookings && $bookings->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Service</th>
                    <th>Notes</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo e($row["customer_name"]); ?></td>
                        <td><?php echo e($row["customer_phone"]); ?></td>
                        <td><?php echo e($row["customer_address"]); ?></td>
                        <td><?php echo e($row["service_category"]); ?></td>
                        <td><?php echo e($row["notes"]); ?></td>
                        <td><?php echo e($row["date"]); ?></td>
                        <td><?php echo e($row["time"]); ?></td>
                        <td><?php echo e($row["booking_status"]); ?></td>
                        <td>
                            <form method="post" style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <input type="hidden" name="booking_id" value="<?php echo (int)$row["booking_id"]; ?>">
                                <?php if ($row["booking_status"] === "pending"): ?>
                                    <button type="submit" name="action" value="accept">Accept</button>
                                    <button class="secondary" type="submit" name="action" value="reject">Reject</button>
                                <?php elseif ($row["booking_status"] === "confirmed"): ?>
                                    <button type="submit" name="action" value="complete">Complete</button>
                                <?php else: ?>
                                    <span class="badge">No action</span>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No booking requests.</p>
    <?php endif; ?>
</div>

