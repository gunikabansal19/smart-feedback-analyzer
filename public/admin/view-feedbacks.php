<?php
session_start();

// ✅ Admin-only check
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@example.com') {
    echo "<script>alert('Admins only.'); window.location.href='../dashboard.php';</script>";
    exit();
}

require_once '../../backend/db.php';

// ✅ Fetch feedbacks with user info
$sql = "SELECT u.name, u.email, f.category, f.rating, f.sentiment, f.comment, f.created_at
        FROM feedbacks f
        INNER JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Feedbacks - Admin View</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../../assets/style.css" />
</head>
<body>
  <nav class="navbar navbar-dark bg-dark">
    <div class="container d-flex justify-content-between align-items-center">
      <span class="navbar-brand">📝 All Feedbacks</span>
      <a href="admin-dashboard.php" class="btn btn-light btn-sm">← Back to Analytics</a>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">User Feedback Log</h4>
      <a href="export-csv.php" class="btn btn-success btn-sm">📥 Export to CSV</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Category</th>
              <th>Rating</th>
              <th>Sentiment</th>
              <th>Comment</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= (int)$row['rating'] ?></td>
                <td>
                  <?php
                  $s = strtolower($row['sentiment']);
                  $badge = $s === 'positive' ? 'success' : ($s === 'negative' ? 'danger' : 'secondary');
                  echo "<span class='badge badge-$badge text-uppercase'>$s</span>";
                  ?>
                </td>
                <td><?= htmlspecialchars($row['comment']) ?></td>
                <td><?= $row['created_at'] ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center">No feedbacks found.</div>
    <?php endif; ?>
  </div>

  <footer class="text-center mt-5 mb-3">
    <p>© 2025 Smart Feedback System</p>
  </footer>
</body>
</html>
