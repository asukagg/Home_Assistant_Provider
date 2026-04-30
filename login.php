<?php
require_once __DIR__ . "/includes/header.php";

if (isset($_SESSION["user_id"])) {
    redirect("dashboard.php");
}

if (is_post()) {
    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($login === "" || $password === "") {
        set_flash("error", "Please enter login and password.");
    } else {
        $stmt = db_query($conn, "SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1", "ss", [$login, $login]);
        if ($stmt) {
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if (!$user) {
                set_flash("error", "User not found.");
            } else {
                $stored = $user["password"] ?? "";
                $valid = false;
                if (password_verify($password, $stored)) {
                    $valid = true;
                } elseif ($password === $stored) {
                    $valid = true;
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    db_query($conn, "UPDATE users SET password = ? WHERE user_id = ?", "si", [$new_hash, $user["user_id"]]);
                }

                if ($valid) {
                    $_SESSION["user_id"] = $user["user_id"];
                    $role = $user["role"] ?? "customer";
                    if ((int)$user["flag"] === 1) {
                        $role = "admin";
                    }
                    $_SESSION["role"] = $role;
                    $_SESSION["name"] = $user["name"];
                    redirect("dashboard.php");
                } else {
                    set_flash("error", "Invalid password.");
                }
            }
        } else {
            set_flash("error", "Login failed.");
        }
    }
}
?>
<div class="card">
    <h2>Login</h2>
    <form method="post">
        <label>Email or Phone</label>
        <input type="text" name="login" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
<?php require_once __DIR__ . "/includes/footer.php"; ?>
