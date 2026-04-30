<?php
require_once __DIR__ . "/includes/header.php";

// if (isset($_SESSION["user_id"])) {
   // redirect("dashboard.php");
//}
?>

<style>
.hero {
    height: 100vh;
    background: url('assets/h1.jpg') no-repeat center center/cover;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    position: relative;
    padding: 20px;
}

/* Overlay */
.hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

/* Content */
.hero-content {
    position: relative;
    z-index: 1;
    color: #fff;
    max-width: 700px;
}

.hero h1 {
    font-size: 42px;
    margin-bottom: 10px;
}

.hero h3 {
    font-weight: normal;
    margin-bottom: 20px;
}

.hero p {
    font-size: 16px;
    margin-bottom: 15px;
}

.grid {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 15px;
}

.card {
    padding: 12px 25px;
    background: #ffffff;
    color: #333;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: 0.3s;
}

.card:hover {
    background: #f0f0f0;
}
</style>

<div class="hero">
    <div class="hero-content">
        <h1>Welcome to Home Assistant Provider</h1>
        <h3>Compassionate Care, Right at Your Doorstep</h3>

        <p>
            We are dedicated to providing reliable and personalized home assistance services 
            to ensure comfort, safety, and peace of mind for you and your loved ones.
        </p>

        <div class="grid">
            <a class="card" href="login.php">Login</a>
            <a class="card" href="register.php">Register</a>
        </div>
    </div>
</div>



