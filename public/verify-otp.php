<?php
session_start();
require_once '../backend/db.php';

$error = '';
if (!isset($_SESSION['otp_user'])) {
    echo "<script>alert('⏳ Session expired. Register again.'); window.location.href='signup.html';</script>";
    exit();
}

// Resend
if (isset($_GET['resend'])) {
    $_SESSION['otp_user']['otp'] = strval(rand(100000, 999999));
    $_SESSION['otp_user']['expires'] = time() + 300;
    echo "<script>alert('🔁 New OTP: {$_SESSION['otp_user']['otp']}'); window.location.href='verify-otp.php';</script>";
    exit();
}

// Verify
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = trim($_POST['otp']);
    $expected = $_SESSION['otp_user']['otp'];
    $expired = time() > $_SESSION['otp_user']['expires'];

    if ($expired) {
        $error = "⏰ OTP expired. Please resend.";
    } elseif ($entered === $expected) {
        $name  = $_SESSION['otp_user']['name'];
        $email = $_SESSION['otp_user']['email'];
        $pass  = $_SESSION['otp_user']['password'];

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_verified) VALUES (?, ?, ?, 1)");
        $stmt->bind_param("sss", $name, $email, $pass);

        if ($stmt->execute()) {
            $_SESSION['email']     = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_id']   = $conn->insert_id;
            $_SESSION['role']      = 'user';
            unset($_SESSION['otp_user']);

            echo "<script>alert('✅ Email verified!'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            $error = "❌ DB Error.";
        }
    } else {
        $error = "❌ Incorrect OTP.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Verify OTP</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body style="background:#f0f2f5">
  <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 420px;">
      <div class="card-body">
        <h4 class="text-center text-primary">🔐 Verify Email</h4>

        <?php if ($error): ?>
          <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <div class="alert alert-info text-center">
          [Demo OTP: <strong><?= $_SESSION['otp_user']['otp'] ?></strong>]<br>
          <small>Expires in <span id="countdown">300</span>s</small>
        </div>

        <form method="POST">
          <input type="text" name="otp" class="form-control mb-3" maxlength="6" placeholder="Enter OTP" required />
          <button class="btn btn-success btn-block">✅ Verify</button>
        </form>

        <div class="text-center mt-3">
          <a href="?resend=1" class="btn btn-link">🔁 Resend OTP</a><br>
          <a href="signup.html" class="text-muted">← Back</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    let left = <?= $_SESSION['otp_user']['expires'] - time() ?>;
    const c = document.getElementById("countdown");
    const timer = setInterval(() => {
      if (left <= 0) {
        c.innerText = "expired";
        clearInterval(timer);
      } else {
        c.innerText = left--;
      }
    }, 1000);
  </script>
</body>
</html>
