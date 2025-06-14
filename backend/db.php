<?php
// backend/db.php

$host     = "localhost";
$username = "root";
$password = ""; // Leave empty for default XAMPP setup
$database = "smart_feedback";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Set character set (optional but recommended)
if (!$conn->set_charset("utf8mb4")) {
    die("❌ Error loading character set utf8mb4: " . $conn->error);
}
?>
