<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (!is_customer() && !is_provider()) {
    redirect("dashboard.php");
}

$is_provider_user = is_provider();

$stmt = db_query(
    $conn,
    "SELECT h.booking_id, b.date, b.time, b.booking_status,
            s.service_category,
            p.payment_status,
            cu.name AS customer_name, cu.phone AS customer_phone, cu.address AS customer_address,
            pu.name AS provider_name, pu.phone AS provider_phone, pu.address AS provider_address
     FROM history h
     LEFT JOIN booking b ON b.booking_id = h.booking_id
     LEFT JOIN service s ON s.service_id = b.service_id
     LEFT JOIN users cu ON cu.user_id = b.customer_id
     LEFT JOIN users pu ON pu.user_id = b.provider_id
     LEFT JOIN payment p ON p.booking_id = b.booking_id
     WHERE h.user_id = ? AND b.booking_status = 'completed'
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
                    <th><?php echo $is_provider_user ? "Customer" : "Provider"; ?></th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Service</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $items->fetch_assoc()): ?>
                    <?php
                        $name = $is_provider_user ? ($row["customer_name"] ?? "") : ($row["provider_name"] ?? "");
                        $address = $is_provider_user ? ($row["customer_address"] ?? "") : ($row["provider_address"] ?? "");
                        $phone = $is_provider_user ? ($row["customer_phone"] ?? "") : ($row["provider_phone"] ?? "");
                    ?>
                    <tr>
                        <td>#<?php echo (int)$row["booking_id"]; ?></td>
                        <td><?php echo e($name); ?></td>
                        <td><?php echo e($address); ?></td>
                        <td><?php echo e($phone); ?></td>
                        <td><?php echo e($row["service_category"]); ?></td>
                        <td><?php echo e($row["payment_status"] ?? "unpaid"); ?></td>
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

