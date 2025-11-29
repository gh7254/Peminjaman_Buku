<?php
// frontend/index.php
$db = new mysqli('127.0.0.1','root','','sistem_peminjaman');
$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'title';
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 6; $offset = ($page-1)*$per;

$allowed = ['title','year'];
if(!in_array($sort,$allowed)) $sort='title';

$params = [];
$sql = "SELECT b.id, b.title, b.year, c.name as category,
       (SELECT COUNT(*) FROM book_copies bc WHERE bc.book_id=b.id AND bc.status='available') as available_count
       FROM books b 
       LEFT JOIN categories c ON b.category_id=c.id 
       WHERE 1=1";

if ($q) {
    $sql .= " AND (b.title LIKE ? OR b.isbn LIKE ?)";
    $params[] = '%'.$q.'%';
    $params[] = '%'.$q.'%';
}

$sql_count = "SELECT COUNT(*) AS cnt FROM ($sql) x";
$stmtc = $db->prepare($sql_count);
if ($q) $stmtc->bind_param('ss', $params[0], $params[1]);
$stmtc->execute(); $cnt = $stmtc->get_result()->fetch_assoc()['cnt'];
$total = max(1, ceil($cnt/$per));

$sql .= " ORDER BY b.$sort LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
if ($q) $stmt->bind_param('ssii', $params[0], $params[1], $per, $offset);
else $stmt->bind_param('ii', $per, $offset);
$stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html lang="en" class="h-100">
<head>
<meta charset="utf-8">
<title>Katalog Buku</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/custom.css">
<style> body { padding-top: 4.5rem; } </style>
</head>
<body class="d-flex flex-column h-100">

<nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">PerpusMini</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav me-auto mb-2 mb-md-0">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="backend/auth_login.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="flex-shrink-0">
<div class="container py-4">
  <h1 class="mb-3">Katalog Buku</h1>
  <form method="get" class="row g-2 mb-4">
    <div class="col-md-6"><input name="q" class="form-control" placeholder="Cari judul/ISBN" value="<?php echo htmlspecialchars($q); ?>"></div>
    <div class="col-md-3">
      <select name="sort" class="form-select">
        <option value="title" <?php if($sort=='title') echo 'selected'; ?>>Judul</option>
        <option value="year" <?php if($sort=='year') echo 'selected'; ?>>Tahun</option>
      </select>
    </div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Search</button></div>
  </form>

  <div class="row">
    <?php while($r=$res->fetch_assoc()): ?>
    <div class="col-md-4 mb-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?php echo htmlspecialchars($r['title']); ?></h5>
          <p class="mb-1 text-muted">Kategori: <?php echo htmlspecialchars($r['category']); ?></p>
          <p class="mb-1">Tahun: <?php echo $r['year']; ?></p>
          <p class="mt-auto fw-bold <?php echo $r['available_count']>0?'text-success':'text-danger'; ?>">
            Available: <?php echo $r['available_count']; ?>
          </p>
          <a href="book_detail.php?id=<?php echo $r['id']; ?>" class="btn btn-outline-primary btn-sm mt-2">Detail</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <nav><ul class="pagination justify-content-center">
    <?php if($page>1): ?><li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>">Prev</a></li><?php endif; ?>
    <li class="page-item disabled"><span class="page-link">Page <?php echo $page; ?> / <?php echo $total; ?></span></li>
    <?php if($page<$total): ?><li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a></li><?php endif; ?>
  </ul></nav>
</div>
</main>

<footer class="footer mt-auto py-3 bg-primary text-white">
  <div class="container text-center">© <?php echo date("Y"); ?> Perpustakaan Mini</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
