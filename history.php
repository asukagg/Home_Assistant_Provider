<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer()) {
    redirect("dashboard.php");
}

$stmt = db_query(
    $conn,
    "SELECT h.booking_id, b.date, b.time, s.service_category, u.name AS provider_name
     FROM history h
     LEFT JOIN booking b ON b.booking_id = h.booking_id
     LEFT JOIN service s ON s.service_id = b.service_id
     LEFT JOIN users u ON u.user_id = b.provider_id
     WHERE h.user_id = ?
     ORDER BY h.booking_id DESC",
    "i",
    [$_SESSION["user_id"]]
);
$items = $stmt ? $stmt->get_result() : false;
?>
<div class="card">
    <h2>Service History</h2>
    <?php if ($items && $items->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Provider</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $items->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo (int)$row["booking_id"]; ?></td>
                        <td><?php echo e($row["provider_name"]); ?></td>
                        <td><?php echo e($row["service_category"]); ?></td>
                        <td><?php echo e($row["date"]); ?></td>
                        <td><?php echo e($row["time"]); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No history records yet.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
