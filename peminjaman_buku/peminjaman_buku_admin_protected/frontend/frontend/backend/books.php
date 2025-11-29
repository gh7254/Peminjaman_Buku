<?php
require 'config.php';
require_login(); require_admin();

$search = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'title';
$page = max(1, (int)($_GET['page'] ?? 1));
$perpage = 10;
$offset = ($page-1)*$perpage;

$allowed_sort = ['title','year','publisher'];
if (!in_array($sort, $allowed_sort)) $sort = 'title';

$params = [];
$sql_base = "FROM books b LEFT JOIN categories c ON b.category_id=c.id WHERE 1=1 ";
if ($search) {
    $sql_base .= " AND (b.title LIKE ? OR b.isbn LIKE ?)";
    $params[] = '%'.$search.'%';
    $params[] = '%'.$search.'%';
}

$sql_count = "SELECT COUNT(*) as cnt $sql_base";
$stmtc = $mysqli->prepare($sql_count);
if ($search) $stmtc->bind_param(str_repeat('s', count($params)), ...$params);
$stmtc->execute();
$cnt = $stmtc->get_result()->fetch_assoc()['cnt'];
$total_pages = max(1, ceil($cnt / $perpage));

$sql = "SELECT b.*, c.name AS category $sql_base ORDER BY b.$sort LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($sql);
if ($search) {
    $stmt->bind_param('ssii', $params[0], $params[1], $perpage, $offset);
} else {
    $stmt->bind_param('ii', $perpage, $offset);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Books - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../frontend/index.php">Perpustakaan Mini</a>
    <div class="d-flex">
      <span class="navbar-text text-white me-3"><?php echo $_SESSION['role']; ?></span>
      <a class="btn btn-outline-light btn-sm" href="auth_logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Buku</h3>
    <a class="btn btn-success" href="book_form.php">Tambah Buku</a>
  </div>

  <form class="row g-2 mb-3">
    <div class="col-md-6"><input name="q" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search title/ISBN"></div>
    <div class="col-md-3">
      <select name="sort" class="form-select">
        <option value="title" <?php if($sort=='title') echo 'selected'; ?>>Title</option>
        <option value="year" <?php if($sort=='year') echo 'selected'; ?>>Year</option>
        <option value="publisher" <?php if($sort=='publisher') echo 'selected'; ?>>Publisher</option>
      </select>
    </div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Filter</button></div>
  </form>

  <table class="table table-bordered bg-white">
    <thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Year</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo htmlspecialchars($row['category']); ?></td>
        <td><?php echo $row['year']; ?></td>
        <td>
          <a class="btn btn-sm btn-primary" href="book_form.php?id=<?php echo $row['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="book_delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <nav><ul class="pagination">
    <?php if($page>1): ?>
      <li class="page-item"><a class="page-link" href="?page=<?php echo $page-1; ?>">Prev</a></li>
    <?php endif; ?>
    <li class="page-item disabled"><span class="page-link">Page <?php echo $page; ?> / <?php echo $total_pages; ?></span></li>
    <?php if($page<$total_pages): ?>
      <li class="page-item"><a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a></li>
    <?php endif; ?>
  </ul></nav>

  <p>
    <a class="btn btn-outline-secondary" href="borrowings.php">Manage Borrowings</a>
    <a class="btn btn-outline-secondary" href="report_csv.php">Export CSV</a>
  </p>
</div>
</body>
</html>
