<?php
require_once __DIR__ . "/includes/header.php";
require_login();

$result = $conn->query("SELECT * FROM service ORDER BY service_category");
?>
<div class="card">
    <h2>Service Categories</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Price / Hour</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($service = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($service["service_category"]); ?></td>
                    <td><?php echo e($service["price_per_hour"]); ?></td>
                    <td><a href="providers.php?service_id=<?php echo (int)$service["service_id"]; ?>">View Providers</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
