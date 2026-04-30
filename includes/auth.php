<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../DBconnect.php";
require_once __DIR__ . "/functions.php";

function current_user($conn) {
    if (!isset($_SESSION["user_id"])) {
        return null;
    }

    $user_id = (int)$_SESSION["user_id"];
    $stmt = db_query($conn, "SELECT * FROM users WHERE user_id = ? LIMIT 1", "i", [$user_id]);
    if (!$stmt) {
        return null;
    }
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function require_login() {
    if (!isset($_SESSION["user_id"])) {
        redirect("login.php");
    }
}

function require_role($role) {
    $current_role = $_SESSION["role"] ?? "";
    if ($current_role !== $role) {
        redirect("dashboard.php");
    }
}

function is_admin() {
    return ($_SESSION["role"] ?? "") === "admin";
}

function is_provider() {
    return ($_SESSION["role"] ?? "") === "provider";
}

function is_customer() {
    return ($_SESSION["role"] ?? "") === "customer";
}
?>