<?php
/**
 * ========================================================
 * BookSpace - Modular User Sidebar Component (MindRise Style)
 * Warna: Navy #0d172e | Aksen Emas #f59e0b | Hover #18254b
 * ========================================================
 */

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$csrfToken   = $_SESSION['csrf_token'] ?? '';
$userName    = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';
$userEmail   = $_SESSION['email'] ?? '';
$userInitial = strtoupper(substr($userName, 0, 1));

// Deteksi menu aktif jika belum didefinisikan di controller
if (!isset($activeMenu)) {
    $currentRoute = trim($_GET['url'] ?? '', '/');
    if (strpos($currentRoute, 'catalog') === 0 || strpos($currentRoute, 'book') === 0) {
        $activeMenu = 'catalog';
    } elseif ($currentRoute === 'my-loans/history' || $currentRoute === 'riwayat-peminjaman') {
        $activeMenu = 'history';
    } elseif (strpos($currentRoute, 'my-loans') === 0 || strpos($currentRoute, 'peminjaman-saya') === 0) {
        $activeMenu = 'my_loans';
    } else {
        $activeMenu = 'dashboard';
    }
}
?>

<!-- Mobile Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

<!-- Sidebar Utama Pengguna -->
<aside class="app-sidebar" id="appSidebar">

    <!-- Header Logo & Brand -->
    <div class="sidebar-header">
        <a href="index.php?url=user/dashboard" class="sidebar-brand">
            <div class="brand-icon-box">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>

            <div class="brand-info">
                <span class="brand-title">BookSpace</span>
                <span class="brand-badge">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="currentColor">
                        <circle cx="12" cy="12" r="6"></circle>
                    </svg>
                    Perpustakaan
                </span>
            </div>
        </a>

        <!-- Tombol Tutup Sidebar Mobile -->
        <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Navigasi">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Navigasi Menu Anggota -->
    <nav class="sidebar-nav">

        <div class="nav-section-title">Menu Utama</div>

        <ul class="sidebar-menu-list">

            <!-- Beranda / Dashboard -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=user/dashboard" class="sidebar-nav-link <?= ($activeMenu === 'dashboard') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Katalog Buku -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=catalog" class="sidebar-nav-link <?= ($activeMenu === 'catalog') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span>
                    <span>Katalog Buku</span>
                </a>
            </li>

        </ul>

        <!-- Layanan Sirkulasi Anggota -->
        <div class="nav-section-title">Layanan Peminjaman</div>

        <ul class="sidebar-menu-list">

            <!-- Peminjaman Saya -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=my-loans" class="sidebar-nav-link <?= ($activeMenu === 'my_loans') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                            <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                    </span>
                    <span>Peminjaman Saya</span>
                </a>
            </li>

            <!-- Riwayat Peminjaman -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=my-loans/history" class="sidebar-nav-link <?= ($activeMenu === 'history') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </span>
                    <span>Riwayat Peminjaman</span>
                </a>
            </li>

        </ul>

    </nav>

    <!-- Footer Sidebar Pengguna -->
    <div class="sidebar-footer">

        <div class="sidebar-user-card">
            <div class="sidebar-avatar">
                <?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="sidebar-user-info">
                <span class="sidebar-user-name" title="<?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span class="sidebar-user-role">Anggota Perpustakaan</span>
            </div>
        </div>

        <!-- Tombol Logout -->
        <form action="index.php?url=logout" method="POST" style="margin: 0;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="sidebar-logout-btn" title="Keluar dari akun Anda">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>

    </div>

</aside>

<!-- Skrip Toggle Sidebar Responsif Mobile -->
<script>
(function() {
    function initSidebarToggle() {
        const openBtn = document.getElementById('mobileMenuBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const backdrop = document.getElementById('sidebarBackdrop');

        function openNav() {
            document.body.classList.add('sidebar-open');
        }

        function closeNav() {
            document.body.classList.remove('sidebar-open');
        }

        if (openBtn) openBtn.addEventListener('click', openNav);
        if (closeBtn) closeBtn.addEventListener('click', closeNav);
        if (backdrop) backdrop.addEventListener('click', closeNav);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarToggle);
    } else {
        initSidebarToggle();
    }
})();
</script>
