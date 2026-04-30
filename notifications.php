<?php
require_once __DIR__ . "/includes/header.php";
require_login();

if (is_post()) {
    db_query($conn, "UPDATE notifications SET status = 'read' WHERE user_id = ?", "i", [$_SESSION["user_id"]]);
    set_flash("success", "Notifications marked as read.");
}

$stmt = db_query($conn, "SELECT * FROM notifications WHERE user_id = ? ORDER BY notif_id DESC", "i", [$_SESSION["user_id"]]);
$items = $stmt ? $stmt->get_result() : false;
?>
<div class="card">
    <h2>Notifications</h2>
    <form method="post" style="margin-bottom: 10px;">
        <button type="submit">Mark All Read</button>
    </form>
    <?php if ($items && $items->num_rows > 0): ?>
        <ul>
            <?php while ($item = $items->fetch_assoc()): ?>
                <li>
                    <strong><?php echo e($item["status"]); ?>:</strong>
                    <?php echo e($item["notif_msg"]); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No notifications.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
