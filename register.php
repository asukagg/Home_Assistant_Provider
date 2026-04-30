<?php
require_once __DIR__ . "/includes/header.php";

if (isset($_SESSION["user_id"])) {
    redirect("dashboard.php");
}

if (is_post()) {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "customer";
    $nid_number = trim($_POST["nid_number"] ?? "");

    if ($name === "" || $email === "" || $phone === "" || $password === "") {
        set_flash("error", "Please fill in all required fields.");
    } elseif (!in_array($role, ["customer", "provider"], true)) {
        set_flash("error", "Invalid role selected.");
    } else {
        $stmt = db_query($conn, "SELECT user_id FROM users WHERE email = ? OR phone = ? LIMIT 1", "ss", [$email, $phone]);
        if ($stmt && $stmt->get_result()->num_rows > 0) {
            set_flash("error", "Email or phone already exists.");
        } else {
            $nid_file = null;
            if ($role === "provider") {
                if (!isset($_FILES["nid_file"]) || $_FILES["nid_file"]["error"] !== UPLOAD_ERR_OK) {
                    set_flash("error", "NID upload is required for providers.");
                    require __DIR__ . "/includes/footer.php";
                    exit;
                }

                $allowed = ["jpg", "jpeg", "png", "pdf"];
                $original = $_FILES["nid_file"]["name"];
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    set_flash("error", "Invalid NID file type.");
                    require __DIR__ . "/includes/footer.php";
                    exit;
                }

                if ($_FILES["nid_file"]["size"] > 2 * 1024 * 1024) {
                    set_flash("error", "NID file is too large (max 2MB).");
                    require __DIR__ . "/includes/footer.php";
                    exit;
                }

                $filename = uniqid("nid_", true) . "." . $ext;
                $target = __DIR__ . "/uploads/nid/" . $filename;
                if (!move_uploaded_file($_FILES["nid_file"]["tmp_name"], $target)) {
                    set_flash("error", "Failed to upload NID file.");
                    require __DIR__ . "/includes/footer.php";
                    exit;
                }
                $nid_file = "uploads/nid/" . $filename;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $verification_status = ($role === "provider") ? "pending" : "verified";

            $stmt = db_query(
                $conn,
                "INSERT INTO users (name, email, password, phone, address, role, nid_card, nid_file, verification_status, flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)",
                "sssssssss",
                [$name, $email, $hash, $phone, $address, $role, $nid_number, $nid_file, $verification_status]
            );

            if ($stmt) {
                set_flash("success", "Registration successful. Please login.");
                redirect("login.php");
            } else {
                set_flash("error", "Registration failed.");
            }
        }
    }
}
?>
<div class="card">
    <h2>Register</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Phone</label>
        <input type="text" name="phone" required>
        <label>Address</label>
        <textarea name="address" rows="3"></textarea>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Register As</label>
        <select name="role">
            <option value="customer">Customer</option>
            <option value="provider">Service Provider</option>
        </select>
        <div id="providerFields" style="display:none; margin-top:10px;">
        <label>NID Number</label>
        <input type="text" name="nid_number">

        <label>NID File (jpg, png, pdf)</label>
        <input type="file" name="nid_file">
</div>

        <button type="submit">Create Account</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const roleSelect = document.querySelector("select[name='role']");
    const providerFields = document.getElementById("providerFields");

    function toggleFields() {
        if (roleSelect.value === "provider") {
            providerFields.style.display = "block";
        } else {
            providerFields.style.display = "none";
        }
    }

    roleSelect.addEventListener("change", toggleFields);
    toggleFields(); // run on page load
});
</script>


