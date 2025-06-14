<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Sanitize input
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    // ✅ Hash password
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // ✅ Default role setup
    $role = isset($_POST['role']) ? $_POST['role'] : 'user';
    $company_id = isset($_POST['company_id']) && is_numeric($_POST['company_id']) ? (int)$_POST['company_id'] : null;

    // ✅ Prepare insert query
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, company_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $company_id);

    // ✅ Execute and handle response
    if ($stmt->execute()) {
        echo "<script>alert('✅ Registered successfully! Please log in.'); window.location.href='../public/index.html';</script>";
    } else {
        if ($conn->errno == 1062) {
            // Duplicate email
            echo "<script>alert('❌ Email already registered. Please login.'); window.location.href='../public/index.html';</script>";
        } else {
            echo "<script>alert('❌ Registration failed. Please try again.'); window.location.href='../public/signup.html';</script>";
        }
    }

    $stmt->close();
}
$conn->close();
?>
