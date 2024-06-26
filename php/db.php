<?php
$servername = "marcostmarcosvil.mysql.db";
$username = "marcostmarcosvil";
$password = "Barza2001";
$dbname = "marcostmarcosvil";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
