<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$db_host='127.0.0.1'; $db_user='root'; $db_pass=''; $db_name='sistem_peminjaman';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli=new mysqli($db_host,$db_user,$db_pass,$db_name);
if($mysqli->connect_errno){echo "DB error"; exit;}

function is_logged_in(){return isset($_SESSION['user_id']);}
function require_login(){
    if(!is_logged_in()){
        header('Location: auth_login.php'); exit;
    }
}
function require_admin(){
    if(!isset($_SESSION['role']) || $_SESSION['role']!=='admin'){
        header('Location: ../frontend/member_dashboard.php'); exit;
    }
}
?>
