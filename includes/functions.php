<?php
function e($value) {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function redirect($path) {
    header("Location: $path");
    exit;
}

function set_flash($type, $message) {
    if (!isset($_SESSION["flash"])) {
        $_SESSION["flash"] = [];
    }
    if (!isset($_SESSION["flash"][$type])) {
        $_SESSION["flash"][$type] = [];
    }
    $_SESSION["flash"][$type][] = $message;
}

function get_flash() {
    $flash = $_SESSION["flash"] ?? [];
    unset($_SESSION["flash"]);
    return $flash;
}

function db_query($conn, $sql, $types = "", $params = []) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    if ($types !== "" && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        return false;
    }
    return $stmt;
}

function is_post() {
    return $_SERVER["REQUEST_METHOD"] === "POST";
}

function add_notification($conn, $user_id, $message, $status = "unread") {
    db_query(
        $conn,
        "INSERT INTO notifications (status, notif_msg, user_id) VALUES (?, ?, ?)",
        "ssi",
        [$status, $message, $user_id]
    );
}
?>