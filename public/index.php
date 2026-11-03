<?php

/**
 * BookSpace - Library Management System
 * Front Controller (public/index.php)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Panggil sistem routing terpisah
require_once __DIR__ . '/../routes/web.php';
