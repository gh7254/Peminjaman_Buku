<?php
require 'config.php';
require_login(); require_admin();
$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute();
}
header('Location: books.php');
exit;
