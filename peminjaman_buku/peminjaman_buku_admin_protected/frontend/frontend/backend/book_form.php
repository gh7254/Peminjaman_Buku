<?php
require 'config.php';
require_login(); require_admin();

$id = $_GET['id'] ?? null;
$title=''; $isbn=''; $category_id=''; $publisher=''; $year=''; $description='';

if ($id) {
    $stmt = $mysqli->prepare("SELECT * FROM books WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) { $title=$row['title']; $isbn=$row['isbn']; $category_id=$row['category_id']; $publisher=$row['publisher']; $year=$row['year']; $description=$row['description']; }
}
$cats = $mysqli->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title']; $isbn = $_POST['isbn']; $category_id = $_POST['category_id']?:null; $publisher = $_POST['publisher']; $year = $_POST['year']; $description = $_POST['description'];
    if ($id) {
        $stmt = $mysqli->prepare("UPDATE books SET title=?, isbn=?, category_id=?, publisher=?, year=?, description=? WHERE id=?");
        $stmt->bind_param('ssisssi',$title,$isbn,$category_id,$publisher,$year,$description,$id);
        $stmt->execute();
    } else {
        $stmt = $mysqli->prepare("INSERT INTO books (title,isbn,category_id,publisher,year,description) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('ssisss',$title,$isbn,$category_id,$publisher,$year,$description);
        $stmt->execute();
        $id = $stmt->insert_id;
    }
    header('Location: books.php');
    exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Form Buku</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light">
<div class="container my-4">
  <h3><?php echo $id ? 'Edit' : 'Tambah'; ?> Buku</h3>
  <form method="post" class="row g-3">
    <div class="col-md-12"><input name="title" required class="form-control" placeholder="Title" value="<?php echo htmlspecialchars($title); ?>"></div>
    <div class="col-md-6"><input name="isbn" class="form-control" placeholder="ISBN" value="<?php echo htmlspecialchars($isbn); ?>"></div>
    <div class="col-md-6">
      <select name="category_id" class="form-select">
        <option value="">--Pilih Kategori--</option>
        <?php while($c = $cats->fetch_assoc()): ?>
          <option value="<?php echo $c['id'];?>" <?php if($c['id']==$category_id) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']);?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-6"><input name="publisher" class="form-control" placeholder="Publisher" value="<?php echo htmlspecialchars($publisher); ?>"></div>
    <div class="col-md-6"><input name="year" class="form-control" placeholder="Year" value="<?php echo htmlspecialchars($year); ?>"></div>
    <div class="col-12"><textarea name="description" class="form-control" placeholder="Description"><?php echo htmlspecialchars($description); ?></textarea></div>
    <div class="col-12">
      <button class="btn btn-primary"><?php echo $id ? 'Update' : 'Save'; ?></button>
      <a class="btn btn-secondary" href="books.php">Back</a>
    </div>
  </form>
</div>
</body></html>
