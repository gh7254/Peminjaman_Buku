<?php
require_once 'auth_check.php';
$db = new mysqli('127.0.0.1','root','','sistem_peminjaman');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = (int)($_POST['book_id'] ?? 0);
    if ($book_id <= 0) {
        header('Location: member_dashboard.php');
        exit;
    }
    // find available copy
    $stmt = $db->prepare("SELECT id FROM book_copies WHERE book_id=? AND status='available' LIMIT 1");
    $stmt->bind_param('i', $book_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $copy_id = $row['id'];
        // insert borrowing, due date +7 days
        $stmt2 = $db->prepare("INSERT INTO borrowings (user_id, copy_id, due_date) VALUES (?,?, DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY))");
        $stmt2->bind_param('ii', $user_id, $copy_id);
        $stmt2->execute();
        // mark copy borrowed
        $stmt3 = $db->prepare("UPDATE book_copies SET status='borrowed' WHERE id=?");
        $stmt3->bind_param('i', $copy_id);
        $stmt3->execute();
    }
}
header('Location: member_dashboard.php');
exit;
