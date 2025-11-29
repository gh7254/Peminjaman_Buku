<?php
require 'config.php';
require_login(); require_admin();
$res = $mysqli->query("SELECT br.id, u.username, b.title, bc.barcode, br.borrowed_at, br.due_date, br.returned_at, br.status, br.fine_decimal FROM borrowings br JOIN users u ON br.user_id=u.id JOIN book_copies bc ON br.copy_id=bc.id JOIN books b ON bc.book_id=b.id ORDER BY br.borrowed_at DESC");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="borrowings_report.csv"');
$out = fopen('php://output','w');
fputcsv($out, ['ID','Username','Title','Barcode','Borrowed At','Due Date','Returned At','Status','Fine']);
while($r=$res->fetch_assoc()){
    fputcsv($out, [$r['id'],$r['username'],$r['title'],$r['barcode'],$r['borrowed_at'],$r['due_date'],$r['returned_at'],$r['status'],$r['fine_decimal']]);
}
fclose($out);
exit;
