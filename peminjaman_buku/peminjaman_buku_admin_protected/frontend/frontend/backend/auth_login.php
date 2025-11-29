<?php
require 'config.php';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = 'Isi username dan password.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, password, role FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            if (hash('sha256', $password) === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                // redirect based on role
                if ($row['role'] === 'admin') {
                    $base = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                    header('Location: ' . $base . '/books.php');
                    exit;
                } else {
                    // member -> redirect to frontend member dashboard (relative from project root)
                    header('Location: ../frontend/member_dashboard.php');
                    exit;
                }
            }
        }
        $err = 'Login gagal. Periksa username/password.';
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login - Admin/Member</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Login Admin / Member</h4>
          <?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
          <form method="post" action="">
            <div class="mb-2"><input name="username" class="form-control" placeholder="Username" required></div>
            <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary">Login</button>
              <a class="btn btn-outline-secondary" href="../frontend/register.php">Daftar sebagai Member</a>
            </div>
          </form>
          <p class="mt-3 text-muted small">Sample admin: admin / admin123 | member: rizky / password</p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
