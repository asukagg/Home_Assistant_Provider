<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer()) {
    redirect("dashboard.php");
}

if (is_post()) {
    $booking_id = (int)($_POST["booking_id"] ?? 0);
    $action = $_POST["action"] ?? "";
    if ($booking_id > 0 && $action === "cancel") {
        $stmt = db_query($conn, "SELECT provider_id FROM booking WHERE booking_id = ? AND customer_id = ?", "ii", [$booking_id, $_SESSION["user_id"]]);
        $row = $stmt ? $stmt->get_result()->fetch_assoc() : null;
        if ($row) {
            db_query($conn, "UPDATE booking SET booking_status = 'cancelled' WHERE booking_id = ?", "i", [$booking_id]);
            add_notification($conn, (int)$row["provider_id"], "A booking was cancelled by the customer.");
            set_flash("success", "Booking cancelled.");
        }
    }
}

$stmt = db_query(
    $conn,
    "SELECT b.*, s.service_category, s.price_per_hour, u.name AS provider_name, p.payment_status, p.transaction_id, r.review_id
     FROM booking b
     LEFT JOIN service s ON s.service_id = b.service_id
     LEFT JOIN users u ON u.user_id = b.provider_id
     LEFT JOIN payment p ON p.booking_id = b.booking_id
     LEFT JOIN review r ON r.booking_id = b.booking_id
     WHERE b.customer_id = ?
     ORDER BY b.booking_id DESC",
    "i",
    [$_SESSION["user_id"]]
);
$bookings = $stmt ? $stmt->get_result() : false;
?>
<div class="card">
    <h2>My Bookings</h2>
    <?php if ($bookings && $bookings->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo e($row["provider_name"]); ?></td>
                        <td><?php echo e($row["service_category"]); ?></td>
                        <td><?php echo e($row["date"]); ?></td>
                        <td><?php echo e($row["time"]); ?></td>
                        <td><?php echo e($row["booking_status"]); ?></td>
                        <td><?php echo e($row["payment_status"] ?? "unpaid"); ?></td>
                        <td>
                            <?php if (!in_array($row["booking_status"], ["completed", "cancelled", "rejected"], true)): ?>
                                <form method="post" style="margin-bottom: 6px;">
                                    <input type="hidden" name="booking_id" value="<?php echo (int)$row["booking_id"]; ?>">
                                    <input type="hidden" name="action" value="cancel">
                                    <button class="secondary" type="submit">Cancel</button>
                                </form>
                            <?php endif; ?>
                            <?php if (($row["booking_status"] === "confirmed" || $row["booking_status"] === "completed") && empty($row["payment_status"])): ?>
                                <a href="payment.php?booking_id=<?php echo (int)$row["booking_id"]; ?>">Pay</a>
                            <?php endif; ?>
                            <?php if ($row["booking_status"] === "completed" && empty($row["review_id"])): ?>
                                <a href="review.php?booking_id=<?php echo (int)$row["booking_id"]; ?>">Review</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings yet.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
