<?php
/**
 * ========================================================
 * BookSpace - Dashboard Utama Perpustakaan
 * Desain Modern, Elegan & Minimalis (Referensi MindRise)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'dashboard';

$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$adminEmail = $_SESSION['email'] ?? 'admin@bookspace.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BookSpace Library</title>
    <!-- Lembar Gaya Utama MindRise Design System -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud.css">
</head>
<body>

    <!-- Tata Letak Aplikasi 2 Kolom (Sidebar + Wrapper) -->
    <div class="app-layout">

        <!-- 1. SIDEBAR MODULAR (app/views/layouts/sidebar.php) -->
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

        <!-- 2. KONTEN UTAMA WRAPPER -->
        <div class="app-wrapper">

            <!-- 2.1 HEADER MODULAR (app/views/layouts/header.php) -->
            <?php require_once __DIR__ . '/../layouts/header.php'; ?>

            <!-- 2.2 KONTEN UTAMA DASHBOARD -->
            <main class="app-content">

                <!-- ==============================================
                     1. HERO SECTION (MindRise Inspired)
                     ============================================== -->
                <section class="hero-card">
                    <div class="hero-body">
                        <div class="hero-pill">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            Sistem Manajemen Perpustakaan Digital
                        </div>
                        <h2 class="hero-title">
                            Selamat Datang di <span class="highlight-gold">BookSpace</span>, <?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?>!
                        </h2>
                        <p class="hero-subtitle">
                            Kelola inventaris katalog buku, pantau aktivitas sirkulasi peminjaman, serta data keanggotaan perpustakaan secara terpusat, cepat, dan profesional.
                        </p>
                        <div class="hero-actions">
                            <a href="index.php?url=books" class="btn hero-btn-primary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                <span>Katalog Buku</span>
                            </a>
                            <a href="index.php?url=books/create" class="btn hero-btn-secondary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>Tambah Buku</span>
                            </a>
                            <a href="index.php?url=members" class="btn hero-btn-secondary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>Data Anggota</span>
                            </a>
                        </div>
                    </div>
                    <!-- Elemen Visual Ilustrasi Buku -->
                    <div class="hero-illustration">
                        <div class="illustration-box" title="BookSpace Digital Library">
                            <svg viewBox="0 0 24 24" width="70" height="70" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                <line x1="9" y1="7" x2="15" y2="7"></line>
                                <line x1="9" y1="11" x2="13" y2="11"></line>
                            </svg>
                        </div>
                    </div>
                </section>

                <!-- ==============================================
                     2. STATISTIK PERPUSTAKAAN (4 KARTU)
                     ============================================== -->
                <div class="section-header-box">
                    <div class="section-title-group">
                        <h3 class="section-main-title">Ringkasan Statistik Sistem</h3>
                        <p class="section-sub-title">Data riil terkini perpustakaan BookSpace</p>
                    </div>
                </div>

                <section class="stats-grid">
                    <!-- Kartu 1: Total Buku -->
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
                        <div class="stat-value"><?= number_format($statistics['total_books'] ?? 0) ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                            Judul buku terdaftar di katalog
                        </div>
                    </article>

                    <!-- Kartu 2: Total Anggota -->
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
                        <div class="stat-value"><?= number_format($statistics['total_members'] ?? 0) ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Anggota perpustakaan aktif
                        </div>
                    </article>

                    <!-- Kartu 3: Peminjaman Aktif -->
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
                        <div class="stat-value"><?= number_format($statistics['active_loans'] ?? 0) ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Buku dalam status sirkulasi
                        </div>
                    </article>

                    <!-- Kartu 4: Buku Tersedia -->
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
                        <div class="stat-value"><?= number_format($statistics['available_stock'] ?? 0) ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Eksemplar siap dipinjam
                        </div>
                    </article>
                </section>

                <!-- ==============================================
                     3. KATEGORI BUKU
                     ============================================== -->
                <section class="categories-section">
                    <div class="section-header-box">
                        <div class="section-title-group">
                            <h3 class="section-main-title">Kategori Koleksi Buku</h3>
                            <p class="section-sub-title">Cakupan bidang materi dan klasifikasi buku di perpustakaan</p>
                        </div>
                        <a href="index.php?url=categories" class="section-action-link">
                            <span>Kelola Semua Kategori</span>
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>

                    <?php if (empty($categories)): ?>
                        <!-- Empty State Kategori -->
                        <div class="dashboard-card">
                            <div class="dashboard-empty">
                                <div class="dashboard-empty-icon">
                                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="dashboard-empty-title">Belum Ada Kategori Buku</h4>
                                <p class="dashboard-empty-subtitle">Kategori membantu mengelompokkan koleksi buku secara tematik agar mudah ditemukan oleh anggota.</p>
                                <a href="index.php?url=categories/create" class="btn btn-sm btn-gold" style="margin-top: 0.5rem;">
                                    + Tambah Kategori Pertama
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Grid Kategori Buku -->
                        <div class="category-grid">
                            <?php foreach ($categories as $cat): ?>
                                <a href="index.php?url=books&category_id=<?= (int) $cat['id'] ?>" class="category-card" title="Klik untuk memfilter buku kategori <?= htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="category-card-icon">
                                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                        </svg>
                                    </div>
                                    <h4 class="category-card-name"><?= htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <span class="category-card-count">
                                        <?= (int) ($cat['total_books'] ?? 0) ?> Judul Buku
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- ==============================================
                     4. KOLEKSI BUKU TERBARU & INFORMASI PERPUSTAKAAN
                     ============================================== -->
                <div class="dashboard-split-layout">

                    <!-- Kolom Kiri: Koleksi Buku Terbaru -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="var(--gold-accent)" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                Koleksi Buku Terbaru
                            </h3>
                            <a href="index.php?url=books" class="section-action-link" style="font-size: 0.8rem;">
                                <span>Lihat Katalog Lengkap</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        </div>

                        <div class="dashboard-card-body">
                            <?php if (empty($recentBooks)): ?>
                                <!-- Empty State Buku -->
                                <div class="dashboard-empty">
                                    <div class="dashboard-empty-icon">
                                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="dashboard-empty-title">Belum Ada Koleksi Buku</h4>
                                    <p class="dashboard-empty-subtitle">Katalog buku masih kosong. Tambahkan buku baru untuk mulai menyusun koleksi perpustakaan.</p>
                                    <a href="index.php?url=books/create" class="btn btn-sm btn-primary">
                                        + Tambah Buku Baru
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="dashboard-table">
                                        <thead>
                                            <tr>
                                                <th>Informasi Buku</th>
                                                <th>Kategori</th>
                                                <th>Tahun</th>
                                                <th>Ketersediaan Stok</th>
                                                <th style="text-align: right;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentBooks as $b): ?>
                                                <tr>
                                                    <!-- Judul & Pengarang -->
                                                    <td>
                                                        <div class="book-info-cell">
                                                            <span class="book-title-text"><?= htmlspecialchars($b['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                                            <span class="book-meta-text">
                                                                Penulis: <strong><?= htmlspecialchars($b['author'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                                <?php if (!empty($b['publisher'])): ?>
                                                                    • <?= htmlspecialchars($b['publisher'], ENT_QUOTES, 'UTF-8') ?>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </td>

                                                    <!-- Kategori -->
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            <?= htmlspecialchars($b['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                                                        </span>
                                                    </td>

                                                    <!-- Tahun Terbit -->
                                                    <td>
                                                        <span style="font-weight: 600; color: var(--text-secondary); font-size: 0.85rem;">
                                                            <?= !empty($b['publication_year']) ? (int) $b['publication_year'] : '-' ?>
                                                        </span>
                                                    </td>

                                                    <!-- Stok -->
                                                    <td>
                                                        <?php
                                                            $availStock = (int) ($b['available_stock'] ?? $b['stock'] ?? 0);
                                                            $totStock   = (int) ($b['total_stock'] ?? $b['stock'] ?? 0);
                                                        ?>
                                                        <div class="stock-pill">
                                                            <div class="stock-numbers">
                                                                <span class="stock-avail"><?= $availStock ?></span>
                                                                <span class="stock-total">/ <?= $totStock ?> fisik</span>
                                                            </div>
                                                            <?php if ($availStock > 0): ?>
                                                                <span class="badge badge-success" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">Tersedia</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">Stok Kosong</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>

                                                    <!-- Tombol Aksi Edit -->
                                                    <td style="text-align: right;">
                                                        <a href="index.php?url=books/edit&id=<?= (int) $b['id'] ?>" class="btn btn-sm btn-secondary" title="Edit Buku">
                                                            Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Informasi Perpustakaan & Sirkulasi -->
                    <div class="library-info-panel">
                        <div class="info-panel-header">
                            <div class="info-panel-icon">
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                            </div>
                            <h4 class="info-panel-title">Informasi Perpustakaan</h4>
                        </div>

                        <!-- Info Operasional & Sistem -->
                        <div class="info-rows">
                            <!-- Jam Layanan -->
                            <div class="info-row-item">
                                <div class="info-bullet">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                </div>
                                <div class="info-row-content">
                                    <span class="info-row-label">Jam Layanan Fisik</span>
                                    <span class="info-row-text">Senin – Jumat (08.00 – 16.30 WIB)<br>Sabtu (09.00 – 14.00 WIB)</span>
                                </div>
                            </div>

                            <!-- Status Server Database -->
                            <div class="info-row-item">
                                <div class="info-bullet" style="color: var(--emerald-text);">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                    </svg>
                                </div>
                                <div class="info-row-content">
                                    <span class="info-row-label">Status Database</span>
                                    <span class="info-row-text" style="color: var(--emerald-text); font-weight: 700;">
                                        ● Terhubung (MySQL PDO)
                                    </span>
                                </div>
                            </div>

                            <!-- Administrator Aktif -->
                            <div class="info-row-item">
                                <div class="info-bullet">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="info-row-content">
                                    <span class="info-row-label">Akun Administrator</span>
                                    <span class="info-row-text"><?= htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Ketentuan Sirkulasi -->
                        <div class="circulation-highlight">
                            <span class="circulation-title">
                                <svg viewBox="0 0 24 24" width="15" height="15" stroke="var(--gold-accent)" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                Ketentuan Sirkulasi Buku
                            </span>
                            <p class="circulation-desc">
                                Batas waktu peminjaman adalah 14 hari kerja per eksemplar. Setiap anggota perpustakaan dapat meminjam maksimal 3 judul buku secara bersamaan.
                            </p>
                        </div>
                    </div>

                </div>

            </main>

            <!-- 2.3 FOOTER -->
            <footer class="app-footer">
                &copy; <?= date('Y') ?> BookSpace – Modern Library Management System. Seluruh Hak Cipta Dilindungi.
            </footer>

        </div>

    </div>

</body>
</html>
