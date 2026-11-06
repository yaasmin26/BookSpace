<?php
/**
 * ========================================================
 * BookSpace - Manajemen Kategori Buku
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'categories';

$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - BookSpace</title>
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

            <!-- 2.2 KONTEN UTAMA HALAMAN KATEGORI -->
            <main class="app-content">

                <!-- Flash Message Notifikasi Sukses -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <svg class="alert-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div class="alert-content">
                            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Flash Message Notifikasi Error -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <svg class="alert-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="alert-content">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Header Halaman -->
                <div class="page-header">
                    <div class="page-title-box">
                        <h1 class="page-title">Manajemen Kategori Buku</h1>
                        <p class="page-subtitle">Kelola klasifikasi genre, topik, dan bidang ilmu untuk pengelompokan koleksi katalog perpustakaan.</p>
                    </div>
                    <div>
                        <a href="index.php?url=categories/create" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Kategori Baru</span>
                        </a>
                    </div>
                </div>

                <!-- Kartu Tabel & Filter -->
                <div class="crud-card">
                    <!-- Header Filter & Pencarian -->
                    <div class="crud-card-header">
                        <form action="index.php" method="GET" class="filter-bar">
                            <input type="hidden" name="url" value="categories">

                            <!-- Kotak Pencarian -->
                            <div class="search-box">
                                <svg class="search-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input
                                    type="text"
                                    name="q"
                                    class="search-input"
                                    placeholder="Cari nama kategori atau deskripsi..."
                                    value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>

                            <!-- Tombol Cari -->
                            <button type="submit" class="btn btn-secondary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <span>Cari</span>
                            </button>

                            <!-- Tombol Reset jika filter aktif -->
                            <?php if (!empty($keyword)): ?>
                                <a href="index.php?url=categories" class="btn btn-secondary" title="Reset Pencarian">
                                    Reset
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Tabel Data Kategori -->
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 250px;">Nama Kategori</th>
                                    <th>Deskripsi Kategori</th>
                                    <th style="width: 150px; text-align: center;">Jumlah Buku</th>
                                    <th style="width: 140px; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="empty-title">Tidak Ada Kategori Ditemukan</h3>
                                                <p class="empty-desc">
                                                    <?php if (!empty($keyword)): ?>
                                                        Tidak ada kategori yang sesuai dengan kata kunci pencarian Anda.
                                                    <?php else: ?>
                                                        Belum ada kategori buku yang tersimpan di sistem. Buat kategori pertama Anda sekarang.
                                                    <?php endif; ?>
                                                </p>
                                                <a href="index.php?url=categories/create" class="btn btn-primary btn-sm">
                                                    Tambah Kategori Baru
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($categories as $cat): ?>
                                        <tr>
                                            <!-- Nomor Urut -->
                                            <td><?= $no++ ?></td>

                                            <!-- Nama Kategori -->
                                            <td>
                                                <strong style="color: var(--navy); font-size: 0.95rem;">
                                                    <?= htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                                </strong>
                                            </td>

                                            <!-- Deskripsi -->
                                            <td>
                                                <?php if (!empty($cat['description'])): ?>
                                                    <span style="color: var(--text-secondary); line-height: 1.45;">
                                                        <?= htmlspecialchars($cat['description'], ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: var(--text-muted); font-style: italic;">Tidak ada deskripsi</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Jumlah Buku Terkait -->
                                            <td style="text-align: center;">
                                                <?php $count = (int) ($cat['total_books'] ?? 0); ?>
                                                <?php if ($count > 0): ?>
                                                    <a href="index.php?url=books&category_id=<?= (int)$cat['id'] ?>" class="badge badge-primary" style="text-decoration: none;" title="Lihat buku di kategori ini">
                                                        <?= $count ?> Judul Buku
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge badge-slate">
                                                        0 Buku
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Tombol Aksi -->
                                            <td style="text-align: right;">
                                                <div class="table-actions" style="justify-content: flex-end;">
                                                    <!-- Tombol Edit -->
                                                    <a href="index.php?url=categories/edit&id=<?= $cat['id'] ?>" class="btn btn-sm btn-secondary" title="Edit Kategori">
                                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                        </svg>
                                                        Edit
                                                    </a>

                                                    <!-- Form Hapus (POST dengan CSRF) -->
                                                    <form action="index.php?url=categories/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori \'<?= htmlspecialchars(addslashes($cat['category_name']), ENT_QUOTES, 'UTF-8') ?>\'?');">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            title="<?= $count > 0 ? 'Kategori memiliki ' . $count . ' buku terkait (tidak dapat dihapus)' : 'Hapus Kategori' ?>"
                                                        >
                                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
