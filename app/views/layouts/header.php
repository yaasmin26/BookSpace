<?php
/**
 * ========================================================
 * BookSpace - Modular Header Component (MindRise Style)
 * Menampilkan: Tombol Menu Mobile, Sapaan Admin, & Tanggal
 * ========================================================
 */

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$adminInitial = strtoupper(substr($adminName, 0, 1));

// Format Tanggal Bahasa Indonesia Murni (Tanpa ketergantungan ekstensi intl)
$daftarHari = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu'
];

$daftarBulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$hariIni = $daftarHari[date('l')] ?? date('l');
$tgl     = date('j');
$bln     = $daftarBulan[(int) date('n')] ?? date('F');
$thn     = date('Y');
$tanggalFormatted = "{$hariIni}, {$tgl} {$bln} {$thn}";
?>

<!-- Top Header Bar (White Card over Cream Canvas) -->
<header class="app-header">

    <!-- Sisi Kiri: Tombol Hamburger Mobile & Sapaan Admin -->
    <div class="header-left">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Buka Menu Navigasi">
            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <!-- Sapaan Administrator -->
        <div class="header-greeting-box">
            <h1 class="header-greeting-title">
                Halo, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>
                <span style="font-weight: normal; margin-left: 0.25rem;">👋</span>
            </h1>
            <p class="header-greeting-subtitle">Panel kendali & administrasi perpustakaan BookSpace</p>
        </div>
    </div>

    <!-- Sisi Kanan: Kapsul Tanggal & Profil Singkat -->
    <div class="header-right">
        <!-- Kapsul Tanggal -->
        <div class="date-capsule" title="Tanggal Sistem Hari Ini">
            <svg class="date-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span><?= $tanggalFormatted ?></span>
        </div>

        <!-- Profil Avatar Ringkas -->
        <div class="header-user-badge">
            <div class="header-user-avatar">
                <?= htmlspecialchars($adminInitial, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <span class="header-user-name"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

</header>
