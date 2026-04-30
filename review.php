<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer()) {
    redirect("dashboard.php");
}

$booking_id = isset($_GET["booking_id"]) ? (int)$_GET["booking_id"] : 0;

$stmt = db_query(
    $conn,
    "SELECT b.booking_id, b.booking_status, b.provider_id, s.service_category
     FROM booking b
     LEFT JOIN service s ON s.service_id = b.service_id
     WHERE b.booking_id = ? AND b.customer_id = ?",
    "ii",
    [$booking_id, $_SESSION["user_id"]]
);
$booking = $stmt ? $stmt->get_result()->fetch_assoc() : null;

if (!$booking || $booking["booking_status"] !== "completed") {
    set_flash("error", "Booking is not completed.");
    redirect("bookings.php");
}

if (is_post()) {
    $rating = (int)($_POST["rating"] ?? 0);
    $comment = trim($_POST["comment"] ?? "");

    if ($rating < 1 || $rating > 5) {
        set_flash("error", "Rating must be 1-5.");
    } else {
        $stmt = db_query(
            $conn,
            "INSERT INTO review (review_date, rating, comment, provider_id, booking_id, customer_id) VALUES (CURDATE(), ?, ?, ?, ?, ?)",
            "isiii",
            [$rating, $comment, $booking["provider_id"], $booking_id, $_SESSION["user_id"]]
        );
        if ($stmt) {
            add_notification($conn, (int)$booking["provider_id"], "Review given.");
            set_flash("success", "Review submitted.");
            redirect("bookings.php");
        } else {
            set_flash("error", "Review failed.");
        }
    }
}
?>
<div class="card">
    <h2>Leave a Review</h2>
    <p>Service: <?php echo e($booking["service_category"]); ?></p>
    <form method="post">
        <label>Rating (1-5)</label>
        <input type="number" name="rating" min="1" max="5" required>
        <label>Comment</label>
        <textarea name="comment" rows="4"></textarea>
        <button type="submit">Submit Review</button>
    </form>
</div>

