<?php
// includes/auth_check.php
// Pastikan session_start() sudah dipanggil di file yang me-require file ini
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect ke halaman login jika belum ada session user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
