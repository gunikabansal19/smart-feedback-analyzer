<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Check for duplicate
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>alert('❌ Email already registered. Please log in.'); window.location.href='../public/index.html';</script>";
        exit();
    }

    // Generate OTP
    $otp = strval(rand(100000, 999999));
    $_SESSION['otp_user'] = [
        'name'     => $name,
        'email'    => $email,
        'password' => $pass,
        'otp'      => $otp,
        'expires'  => time() + 300  // 5 minutes
    ];

    echo "<script>
        alert('🔐 OTP for verification: $otp');
        window.location.href = '../public/verify-otp.php';
    </script>";
}
?>
