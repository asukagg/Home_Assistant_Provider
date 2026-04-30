<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer()) {
    redirect("dashboard.php");
}

$provider_id = isset($_GET["provider_id"]) ? (int)$_GET["provider_id"] : 0;
$service_id = isset($_GET["service_id"]) ? (int)$_GET["service_id"] : 0;

$stmt = db_query($conn, "SELECT name, phone FROM users WHERE user_id = ? LIMIT 1", "i", [$provider_id]);
$provider = $stmt ? $stmt->get_result()->fetch_assoc() : null;
$stmt = db_query($conn, "SELECT service_category, price_per_hour FROM service WHERE service_id = ? LIMIT 1", "i", [$service_id]);
$service = $stmt ? $stmt->get_result()->fetch_assoc() : null;

if (!$provider || !$service) {
    set_flash("error", "Invalid provider or service.");
    redirect("providers.php");
}

if (is_post()) {
    $date = $_POST["date"] ?? "";
    $time = $_POST["time"] ?? "";
    $notes = trim($_POST["notes"] ?? "");

    if ($date === "" || $time === "") {
        set_flash("error", "Please select date and time.");
    } else {
        $stmt = db_query(
            $conn,
            "INSERT INTO booking (booking_status, time, date, notes, customer_id, provider_id, service_id) VALUES ('pending', ?, ?, ?, ?, ?, ?)",
            "sssiii",
            [$time, $date, $notes, $_SESSION["user_id"], $provider_id, $service_id]
        );

        if ($stmt) {
            $booking_id = $conn->insert_id;
            db_query($conn, "INSERT INTO can_book (customer_id, service_id, booking_id) VALUES (?, ?, ?)", "iii", [$_SESSION["user_id"], $service_id, $booking_id]);
            add_notification($conn, $provider_id, "New booking request received.");
            set_flash("success", "Booking request submitted.");
            redirect("bookings.php");
        } else {
            set_flash("error", "Failed to create booking.");
        }
    }
}
?>
<div class="card">
    <h2>Book Service</h2>
    <p>Provider: <?php echo e($provider["name"]); ?> (<?php echo e($provider["phone"]); ?>)</p>
    <p>Service: <?php echo e($service["service_category"]); ?> | Price/hour: <?php echo e($service["price_per_hour"]); ?></p>
    <form method="post">
        <label>Date</label>
        <input type="date" name="date" required>
        <label>Time</label>
        <input type="time" name="time" required>
        <label>Notes</label>
        <textarea name="notes" rows="3"></textarea>
        <button type="submit">Submit Request</button>
    </form>
</div>

