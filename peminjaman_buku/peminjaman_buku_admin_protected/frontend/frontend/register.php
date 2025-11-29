<?php
// frontend/register.php - simple registration for members
$db = new mysqli('127.0.0.1','root','','sistem_peminjaman');
$err = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['full_name'] ?? '');

    if ($username === '' || $password === '') {
        $err = 'Isi username dan password.';
    } else {
        // check exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s',$username);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $err = 'Username sudah digunakan.';
        } else {
            $h = hash('sha256', $password);
            $stmt2 = $db->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?,?,?, 'member')");
            $stmt2->bind_param('sss', $username, $h, $fullname);
            $stmt2->execute();
            $success = 'Pendaftaran berhasil. Silakan login.';
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Daftar Member</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Daftar Member</h4>
          <?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
          <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
          <form method="post" action="">
            <div class="mb-2"><input name="username" class="form-control" placeholder="Username" required></div>
            <div class="mb-2"><input name="full_name" class="form-control" placeholder="Nama lengkap (opsional)"></div>
            <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
            <div class="d-grid gap-2">
              <button class="btn btn-success">Daftar</button>
              <a class="btn btn-outline-secondary" href="index.php">Kembali ke katalog</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
