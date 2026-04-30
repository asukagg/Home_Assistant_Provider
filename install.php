<?php
require_once __DIR__ . "/DBconnect.php";

function column_exists($conn, $table, $column) {
    $sql = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return (int)$result["cnt"] > 0;
}

function add_column($conn, $table, $column, $definition) {
    if (!column_exists($conn, $table, $column)) {
        $conn->query("ALTER TABLE `$table` ADD COLUMN $definition");
    }
}

add_column($conn, "users", "email", "email VARCHAR(100) NULL AFTER name");
add_column($conn, "users", "role", "role VARCHAR(20) NULL AFTER address");
add_column($conn, "users", "nid_file", "nid_file VARCHAR(255) NULL AFTER nid_card");
add_column($conn, "users", "balance", "balance DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER verification_status");
add_column($conn, "users", "created_at", "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER balance");

add_column($conn, "booking", "customer_id", "customer_id INT NULL AFTER booking_id");
add_column($conn, "booking", "provider_id", "provider_id INT NULL AFTER customer_id");
add_column($conn, "booking", "service_id", "service_id INT NULL AFTER provider_id");
add_column($conn, "booking", "notes", "notes TEXT NULL AFTER date");
add_column($conn, "booking", "created_at", "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER notes");

add_column($conn, "payment", "transaction_ref", "transaction_ref VARCHAR(100) NULL AFTER transaction_id");
add_column($conn, "payment", "paid_at", "paid_at DATETIME NULL AFTER amount");

add_column($conn, "review", "customer_id", "customer_id INT NULL AFTER booking_id");

add_column($conn, "notifications", "created_at", "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER user_id");

$conn->query("UPDATE users SET role = 'admin' WHERE flag = 1");
$conn->query("UPDATE users SET role = 'customer' WHERE role IS NULL OR role = ''");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Install</title>
</head>
<body>
    <h2>Database update complete.</h2>
    <p><a href="index.php">Go to Home</a></p>
</body>
</html>
