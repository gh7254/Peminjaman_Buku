<?php
require_once 'auth_check.php';
$db = new mysqli('127.0.0.1','root','','sistem_peminjaman');
$user_id = $_SESSION['user_id'];
// fetch user's borrowings
$stmt = $db->prepare("SELECT br.*, b.title, bc.barcode FROM borrowings br JOIN book_copies bc ON br.copy_id=bc.id JOIN books b ON bc.book_id=b.id WHERE br.user_id=? ORDER BY br.borrowed_at DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$borrowings = $stmt->get_result();

// fetch available books (simple list)
$books = $db->query("SELECT b.id, b.title, (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id=b.id AND bc.status='available') as available_count FROM books b ORDER BY b.title ASC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Member Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container">
    <a class="navbar-brand" href="index.php">PerpusMini</a>
    <div class="ms-auto">
      <span class="me-2">Hello, <?php echo htmlspecialchars($_SESSION['role']); ?></span>
      <a class="btn btn-outline-secondary btn-sm" href="../backend/auth_logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <h3>Dashboard Member</h3>
  <div class="row">
    <div class="col-md-6">
      <h5>Peminjaman Saya</h5>
      <table class="table table-sm">
        <thead><tr><th>Book</th><th>Barcode</th><th>Borrowed</th><th>Due</th><th>Status</th></tr></thead>
        <tbody>
        <?php while($r = $borrowings->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['title']); ?></td>
            <td><?php echo htmlspecialchars($r['barcode']); ?></td>
            <td><?php echo $r['borrowed_at']; ?></td>
            <td><?php echo $r['due_date']; ?></td>
            <td><?php echo $r['status']; ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <div class="col-md-6">
      <h5>Request Borrow</h5>
      <p>Pilih buku lalu klik Request (sistem akan mengambil satu salinan tersedia jika ada).</p>
      <form method="post" action="request_borrow.php">
        <div class="mb-2">
          <select name="book_id" class="form-select" required>
            <option value="">--Pilih buku--</option>
            <?php while($b = $books->fetch_assoc()): ?>
              <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['title']); ?> (<?php echo $b['available_count']; ?> available)</option>
            <?php endwhile; ?>
          </select>
        </div>
        <button class="btn btn-primary">Request Borrow</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
