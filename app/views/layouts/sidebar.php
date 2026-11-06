<?php
/**
 * ========================================================
 * BookSpace - Modular Sidebar Component (MindRise Style)
 * Warna: Navy #0d172e | Aksen Emas #f59e0b | Hover #18254b
 * ========================================================
 */

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$csrfToken = $_SESSION['csrf_token'] ?? '';
$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$adminEmail = $_SESSION['email'] ?? 'admin@bookspace.com';
$adminInitial = strtoupper(substr($adminName, 0, 1));

// Deteksi menu aktif
if (!isset($activeMenu)) {
    $currentRoute = trim($_GET['url'] ?? '', '/');

    if (empty($currentRoute) && isset($_SERVER['REQUEST_URI'])) {
        $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (strpos($uriPath, 'books') !== false) {
            $currentRoute = 'books';
        } elseif (strpos($uriPath, 'categories') !== false) {
            $currentRoute = 'categories';
        } elseif (strpos($uriPath, 'members') !== false) {
            $currentRoute = 'members';
        } elseif (strpos($uriPath, 'returns') !== false) {
            $currentRoute = 'returns';
        } elseif (strpos($uriPath, 'loans') !== false) {
            $currentRoute = 'loans';
        } else {
            $currentRoute = 'dashboard';
        }
    }

    if (empty($currentRoute) || $currentRoute === 'dashboard') {
        $activeMenu = 'dashboard';
    } elseif (strpos($currentRoute, 'books') === 0) {
        $activeMenu = 'books';
    } elseif (strpos($currentRoute, 'categories') === 0) {
        $activeMenu = 'categories';
    } elseif (strpos($currentRoute, 'members') === 0) {
        $activeMenu = 'members';
    } elseif (strpos($currentRoute, 'returns') === 0) {
        $activeMenu = 'returns';
    } elseif (strpos($currentRoute, 'loans') === 0) {
        $activeMenu = 'loans';
    } else {
        $activeMenu = 'dashboard';
    }
}
?>

<!-- Mobile Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

<!-- Sidebar Utama -->
<aside class="app-sidebar" id="appSidebar">

    <!-- Header Logo & Brand -->
    <div class="sidebar-header">
        <a href="index.php?url=dashboard" class="sidebar-brand">
            <div class="brand-icon-box">
                <svg viewBox="0 0 24 24" width="22" height="22"
                    stroke="currentColor" stroke-width="2.2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            </div>

            <div class="brand-info">
                <span class="brand-title">BookSpace</span>
                <span class="brand-badge">
                    <svg viewBox="0 0 24 24" width="10" height="10"
                        stroke="currentColor" stroke-width="2" fill="currentColor">
                        <circle cx="12" cy="12" r="6"></circle>
                    </svg>
                    Perpustakaan
                </span>
            </div>
        </a>

        <!-- Tombol Tutup Sidebar -->
        <button type="button" class="sidebar-close-btn"
            id="sidebarCloseBtn" aria-label="Tutup Navigasi">
            <svg viewBox="0 0 24 24" width="20" height="20"
                stroke="currentColor" stroke-width="2" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Navigasi Menu -->
    <nav class="sidebar-nav">

        <div class="nav-section-title">Menu Utama</div>

        <ul class="sidebar-menu-list">

            <!-- Dashboard -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=dashboard" class="sidebar-nav-link <?= ($activeMenu === 'dashboard') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Manajemen Buku -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=books" class="sidebar-nav-link <?= ($activeMenu === 'books') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </span>
                    <span>Manajemen Buku</span>
                </a>
            </li>

            <!-- Manajemen Kategori -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=categories" class="sidebar-nav-link <?= ($activeMenu === 'categories') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </span>
                    <span>Manajemen Kategori</span>
                </a>
            </li>

            <!-- Manajemen Anggota -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=members" class="sidebar-nav-link <?= ($activeMenu === 'members') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span>Manajemen Anggota</span>
                </a>
            </li>

        </ul>

        <!-- Layanan Sirkulasi -->
        <div class="nav-section-title">Layanan Sirkulasi</div>

        <ul class="sidebar-menu-list">

            <!-- Peminjaman -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=loans" class="sidebar-nav-link <?= ($activeMenu === 'loans') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                            <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                    </span>
                    <span>Peminjaman</span>
                </a>
            </li>

            <!-- Pengembalian -->
            <li class="sidebar-nav-item">
                <a href="index.php?url=returns" class="sidebar-nav-link <?= ($activeMenu === 'returns') ? 'active' : '' ?>">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                    </span>
                    <span>Pengembalian</span>
                </a>
            </li>

        </ul>

    </nav>

    <!-- Footer Sidebar -->
    <div class="sidebar-footer">

        <div class="sidebar-user-card">
            <div class="sidebar-avatar">
                <?= htmlspecialchars($adminInitial, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="sidebar-user-info">
                <span class="sidebar-user-name"
                    title="<?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>
                </span>

                <span class="sidebar-user-role">Administrator</span>
            </div>
        </div>

        <!-- Logout -->
        <form action="index.php?url=logout" method="POST" style="margin: 0;">
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <button type="submit" class="sidebar-logout-btn"
                title="Keluar dari sesi administrator">
                <svg viewBox="0 0 24 24" width="16" height="16"
                    stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>

    </div>

</aside>

<!-- Skrip Toggle Sidebar Responsif -->
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