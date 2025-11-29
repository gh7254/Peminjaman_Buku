<?php
// frontend/book_detail.php
$id = $_GET['id'] ?? null;
$db = new mysqli('127.0.0.1','root','','sistem_peminjaman');
if (!$id) { header('Location: index.php'); exit; }

$stmt = $db->prepare("SELECT b.*, c.name as category FROM books b LEFT JOIN categories c ON b.category_id=c.id WHERE b.id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
if (!$book) { header('Location: index.php'); exit; }

$copies = $db->prepare("SELECT * FROM book_copies WHERE book_id=?");
$copies->bind_param('i',$id); $copies->execute(); $copies_res = $copies->get_result();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Detail Buku</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/custom.css">
</head><body class="bg-light">
<div class="container py-4">
  <a class="btn btn-link mb-3" href="index.php">&larr; Kembali</a>
  <div class="card">
    <div class="card-body">
      <h3><?php echo htmlspecialchars($book['title']); ?></h3>
      <p class="text-muted">Kategori: <?php echo htmlspecialchars($book['category']); ?> | ISBN: <?php echo htmlspecialchars($book['isbn']); ?></p>
      <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
      <hr>
      <h5>Salinan Buku</h5>
      <ul>
      <?php while($c = $copies_res->fetch_assoc()): ?>
        <li><?php echo htmlspecialchars($c['barcode']); ?> — <strong><?php echo $c['status']; ?></strong></li>
      <?php endwhile; ?>
      </ul>
    </div>
  </div>
</div>
</body></html>
