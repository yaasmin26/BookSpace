<?php
$csrfToken = $_SESSION['csrf_token'] ?? '';
$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$adminEmail = $_SESSION['email'] ?? 'admin@bookspace.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSpace - Dashboard Admin Perpustakaan</title>
    <!-- CSS Terpisah -->
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <!-- Navigasi Atas (Header) -->
    <header class="navbar">
        <div class="navbar-container">
            <!-- Brand & Judul BookSpace -->
            <a href="index.php?url=dashboard" class="navbar-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <span class="brand-text">BookSpace</span>
                <span class="brand-badge">Perpustakaan</span>
            </a>

            <!-- Info Profil Admin & Tombol Logout -->
            <div class="navbar-user">
                <div class="user-profile">
                    <div class="avatar-circle">
                        <?= strtoupper(substr($adminName, 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <!-- Nama admin yang sedang login -->
                        <span class="user-name"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="user-role-badge">Admin Perpustakaan</span>
                    </div>
                </div>

                <!-- Tombol Logout (Metode POST dengan Proteksi CSRF) -->
                <form action="index.php?url=logout" method="POST" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn-logout" title="Keluar dari akun">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Konten Utama Dashboard -->
    <main class="dashboard-main">

        <!-- Kartu Sambutan (Hero Banner) -->
        <section class="welcome-card">
            <h1 class="welcome-title">Selamat Datang, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p class="welcome-subtitle">
                Panel kendali administrasi perpustakaan BookSpace. Pantau ketersediaan katalog buku, keanggotaan aktif, serta status sirkulasi peminjaman secara terpusat.
            </p>
        </section>

        <!-- Header Seksi Statistik -->
        <div class="section-header">
            <h2 class="section-title">Ringkasan Statistik Perpustakaan</h2>
            <span class="section-date">Data terkini sistem BookSpace</span>
        </div>

        <!-- Grid 4 Kartu Statistik (Data dari DashboardController) -->
        <section class="stats-grid">

            <!-- 1. Total Buku -->
            <article class="stat-card books">
                <div class="stat-header">
                    <span class="stat-title">Total Buku</span>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value"><?= number_format($statistics['total_books'] ?? $totalBooks ?? 0) ?></div>
                <div class="stat-desc">Judul buku terdaftar</div>
            </article>

            <!-- 2. Total Anggota -->
            <article class="stat-card members">
                <div class="stat-header">
                    <span class="stat-title">Total Anggota</span>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value"><?= number_format($statistics['total_members'] ?? $totalMembers ?? 0) ?></div>
                <div class="stat-desc">Anggota perpustakaan aktif</div>
            </article>

            <!-- 3. Peminjaman Aktif (Status: borrowed) -->
            <article class="stat-card loans">
                <div class="stat-header">
                    <span class="stat-title">Peminjaman Aktif</span>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="stat-value"><?= number_format($statistics['active_loans'] ?? $activeLoans ?? 0) ?></div>
                <div class="stat-desc">Buku sedang dipinjam</div>
            </article>

            <!-- 4. Buku Tersedia (available_stock) -->
            <article class="stat-card stock">
                <div class="stat-header">
                    <span class="stat-title">Buku Tersedia</span>
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                    </div>
                </div>
                <div class="stat-value"><?= number_format($statistics['available_stock'] ?? $availableStock ?? 0) ?></div>
                <div class="stat-desc">Eksemplar siap dipinjam</div>
            </article>

        </section>

        <!-- Informasi Sistem Operasional Perpustakaan -->
        <section class="info-card">
            <div class="info-header">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Informasi Sistem & Administrasi</span>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Email Administrator</div>
                    <div class="info-val"><?= htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Server Database</div>
                    <div class="info-val" style="color: #059669;">Terhubung (MySQL PDO)</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Aplikasi</div>
                    <div class="info-val">BookSpace MVC v1.0</div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="dashboard-footer">
        &copy; <?= date('Y') ?> BookSpace – Library Management System. Hak Cipta Dilindungi.
    </footer>

</body>
</html>
