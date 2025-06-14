<?php
session_start();
require_once 'db.php'; // Central DB connection

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // ✅ LOGIN
    if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $pass  = trim($_POST['pass']);

        // ✅ Admin shortcut (without DB)
        if ($email === 'admin@example.com' && $pass === 'admin123') {
            $_SESSION['email']     = $email;
            $_SESSION['user_name'] = 'Admin';
            $_SESSION['user_id']   = 0;
            header("Location: ../public/dashboard.php");
            exit();
        }

        // ✅ User login using DB
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($pass, $hashed_password)) {
                $_SESSION['email']     = $email;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_id']   = $id;
                header("Location: ../public/dashboard.php");
                exit();
            } else {
                echo "<script>alert('❌ Incorrect password.'); window.location.href='../public/index.html';</script>";
                exit();
            }
        } else {
            echo "<script>alert('❌ No account found with this email.'); window.location.href='../public/index.html';</script>";
            exit();
        }
    }

    // ✅ LOGOUT
    if ($action === 'logout') {
        session_destroy();
        header("Location: ../public/index.html");
        exit();
    }
}
?>
