
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home_assistant_provider";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
