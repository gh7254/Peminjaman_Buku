<?php
require 'config.php';
require_login(); require_admin();

if (isset($_GET['return_id'])) {
    $id = (int)$_GET['return_id'];
    $stmt = $mysqli->prepare("UPDATE borrowings SET returned_at=NOW(), status='returned' WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute();
    $stmt2 = $mysqli->prepare("SELECT copy_id FROM borrowings WHERE id=?");
    $stmt2->bind_param('i',$id); $stmt2->execute();
    $copy = $stmt2->get_result()->fetch_assoc()['copy_id'];
    $stmt3 = $mysqli->prepare("UPDATE book_copies SET status='available' WHERE id=?");
    $stmt3->bind_param('i',$copy); $stmt3->execute();
    header('Location: borrowings.php');
    exit;
}

$today = date('Y-m-d');
$mysqli->query("UPDATE borrowings SET status='overdue' WHERE status='borrowed' AND due_date < '$today'");

$res = $mysqli->query("SELECT br.*, u.username, bc.barcode, b.title FROM borrowings br JOIN users u ON br.user_id=u.id JOIN book_copies bc ON br.copy_id=bc.id JOIN books b ON bc.book_id=b.id ORDER BY br.borrowed_at DESC");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Borrowings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container my-4">
  <h3>Daftar Peminjaman</h3>
  <p><a class="btn btn-outline-secondary" href="report_csv.php">Export CSV</a></p>
  <table class="table table-striped bg-white">
    <thead><tr><th>ID</th><th>User</th><th>Book</th><th>Barcode</th><th>Borrowed At</th><th>Due Date</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php while($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?php echo $r['id']; ?></td>
          <td><?php echo htmlspecialchars($r['username']); ?></td>
          <td><?php echo htmlspecialchars($r['title']); ?></td>
          <td><?php echo htmlspecialchars($r['barcode']); ?></td>
          <td><?php echo $r['borrowed_at']; ?></td>
          <td><?php echo $r['due_date']; ?></td>
          <td><?php echo $r['status']; ?></td>
          <td>
            <?php if($r['status']!='returned'): ?>
            <a class="btn btn-sm btn-success" href="?return_id=<?php echo $r['id']; ?>" onclick="return confirm('Tandai returned?')">Mark Returned</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body></html>
