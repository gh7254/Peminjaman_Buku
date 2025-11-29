<?php
// backend/auth_logout.php

session_start();

// hapus semua data session
session_unset();

// hancurkan session
session_destroy();

// redirect kembali ke halaman login admin/member
header('Location: auth_login.php');
exit;
