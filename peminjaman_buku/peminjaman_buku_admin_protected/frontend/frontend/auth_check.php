<?php
// frontend/auth_check.php - include at top of member pages
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    // redirect to backend login
    header('Location: ../backend/auth_login.php');
    exit;
}
?>