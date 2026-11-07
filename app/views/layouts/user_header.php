<?php
/**
 * ========================================================
 * BookSpace - Modular User Header Component (MindRise Style)
 * Menampilkan: Tombol Hamburger Mobile, Sapaan Anggota, & Tanggal
 * ========================================================
 */

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$userName    = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';
$userInitial = strtoupper(substr($userName, 0, 1));

// Format Tanggal Bahasa Indonesia Murni
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

    <!-- Sisi Kiri: Tombol Hamburger Mobile & Sapaan Anggota -->
    <div class="header-left">
        <!-- Tombol Hamburger Mobile -->
        <button type="button" class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Buka Menu Navigasi">
            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <!-- Sapaan Anggota Perpustakaan -->
        <div class="header-greeting-box">
            <h1 class="header-greeting-title">
                Halo, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                <span style="font-weight: normal; margin-left: 0.25rem;">👋</span>
            </h1>
            <p class="header-greeting-subtitle">Panel layanan mandiri anggota perpustakaan BookSpace</p>
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
                <?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <span class="header-user-name"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>

</header>

<!-- Notifikasi Alert Flash Messages (Jika Ada) -->
<?php if (!empty($_SESSION['flash_success']) || !empty($_SESSION['flash_error']) || !empty($_SESSION['flash_warning'])): ?>
    <div style="padding: 1.5rem 2.25rem 0; max-width: 1400px; width: 100%; margin: 0 auto;">
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert-box alert-success" role="alert">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert-box alert-danger" role="alert">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_warning'])): ?>
            <div class="alert-box alert-warning" role="alert">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span><?= htmlspecialchars($_SESSION['flash_warning'], ENT_QUOTES, 'UTF-8') ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
            <?php unset($_SESSION['flash_warning']); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
