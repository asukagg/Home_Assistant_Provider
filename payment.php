<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer()) {
    redirect("dashboard.php");
}

$booking_id = isset($_GET["booking_id"]) ? (int)$_GET["booking_id"] : 0;

$stmt = db_query(
    $conn,
    "SELECT b.booking_id, b.booking_status, s.price_per_hour, s.service_category
     FROM booking b
     LEFT JOIN service s ON s.service_id = b.service_id
     WHERE b.booking_id = ? AND b.customer_id = ?",
    "ii",
    [$booking_id, $_SESSION["user_id"]]
);
$booking = $stmt ? $stmt->get_result()->fetch_assoc() : null;

if (!$booking) {
    set_flash("error", "Invalid booking.");
    redirect("bookings.php");
}

if (is_post()) {
    $method = $_POST["method"] ?? "";
    $transaction_ref = trim($_POST["transaction_ref"] ?? "");
    $amount = (float)($booking["price_per_hour"] ?? 0);

    if ($method === "" || $transaction_ref === "") {
        set_flash("error", "Please enter payment details.");
    } else {
        $stmt = db_query(
            $conn,
            "INSERT INTO payment (transaction_ref, payment_status, payment_method, amount, booking_id, paid_at) VALUES (?, 'paid', ?, ?, ?, NOW())",
            "ssdi",
            [$transaction_ref, $method, $amount, $booking_id]
        );
        if ($stmt) {
            add_notification($conn, $_SESSION["user_id"], "Payment successful.");
            set_flash("success", "Payment recorded.");
            redirect("bookings.php");
        } else {
            set_flash("error", "Payment failed.");
        }
    }
}
?>
<div class="card">
    <h2>Payment</h2>
    <p>Booking #<?php echo (int)$booking["booking_id"]; ?> | Service: <?php echo e($booking["service_category"]); ?></p>
    <p>Amount: <?php echo e($booking["price_per_hour"]); ?></p>
    <form method="post">
        <label>Payment Method</label>
        <select name="method" required>
            <option value="card">Card</option>
            <option value="bkash">bKash</option>
            <option value="nagad">Nagad</option>
            <option value="cash">Cash</option>
        </select>
        <label>Transaction ID</label>
        <input type="text" name="transaction_ref" required>
        <button type="submit">Pay Now</button>
    </form>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
