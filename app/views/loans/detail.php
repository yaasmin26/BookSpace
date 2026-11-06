<?php
/**
 * ========================================================
 * BookSpace - Detail Transaksi Peminjaman Buku
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'loans';

$adminName = $adminName ?? $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken = $csrfToken ?? $_SESSION['csrf_token'] ?? '';
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #TRX-<?= sprintf('%04d', $loan['id']) ?> - BookSpace</title>
    <!-- Lembar Gaya Utama MindRise Design System -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud.css">
    <link rel="stylesheet" href="css/loans.css">
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

            <!-- 2.2 KONTEN UTAMA HALAMAN DETAIL -->
            <main class="app-content">

                <!-- Breadcrumb Navigasi -->
                <nav class="breadcrumb">
                    <a href="index.php?url=dashboard">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="index.php?url=loans">Sirkulasi Peminjaman</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Detail Transaksi #TRX-<?= sprintf('%04d', $loan['id']) ?></span>
                </nav>

                <!-- Header Halaman -->
                <div class="page-header">
                    <div class="page-title-box">
                        <h1 class="page-title">Detail Transaksi Peminjaman #TRX-<?= sprintf('%04d', $loan['id']) ?></h1>
                        <p class="page-subtitle">Informasi lengkap status sirkulasi, identitas anggota peminjam, buku, dan batas waktu pengembalian.</p>
                    </div>
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <!-- Tombol Kembali ke Daftar Transaksi -->
                        <a href="index.php?url=loans" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Kembali ke Daftar Transaksi
                        </a>

                        <!-- Tombol Kembalikan Buku (Jika status 'borrowed') -->
                        <?php if ($loan['status'] === 'borrowed'): ?>
                            <form action="index.php?url=loans/return" method="POST" style="margin: 0;" onsubmit="return confirm('Konfirmasi pengembalian buku \'<?= htmlspecialchars(addslashes($loan['book_title']), ENT_QUOTES, 'UTF-8') ?>\' yang dipinjam oleh <?= htmlspecialchars(addslashes($loan['member_name']), ENT_QUOTES, 'UTF-8') ?>? Stok buku akan bertambah kembali.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id" value="<?= (int) $loan['id'] ?>">
                                <button type="submit" class="btn btn-success">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    Kembalikan Buku
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Banner Status Peminjaman -->
                <?php if ($loan['status'] === 'returned'): ?>
                    <div class="status-hero status-hero-returned">
                        <div class="status-hero-left">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <div>
                                <div class="status-hero-title">Transaksi Telah Selesai (Buku Sudah Dikembalikan)</div>
                                <div class="status-hero-desc">Buku telah dikembalikan pada tanggal <?= date('d F Y', strtotime($loan['return_date'])) ?>. Stok buku telah kembali ke rak perpustakaan.</div>
                            </div>
                        </div>
                        <span class="badge badge-success" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">Status: Dikembalikan</span>
                    </div>
                <?php elseif (!empty($loan['is_overdue'])): ?>
                    <div class="status-hero status-hero-overdue">
                        <div class="status-hero-left">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <div>
                                <div class="status-hero-title">Peminjaman Telah Melewati Batas Jatuh Tempo (Terlambat <?= $loan['overdue_days'] ?> Hari)</div>
                                <div class="status-hero-desc">Batas akhir pengembalian adalah tanggal <?= date('d F Y', strtotime($loan['due_date'])) ?>. Segera hubungi anggota untuk memproses pengembalian.</div>
                            </div>
                        </div>
                        <span class="badge badge-overdue" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">Terlambat <?= $loan['overdue_days'] ?> Hari</span>
                    </div>
                <?php else: ?>
                    <div class="status-hero status-hero-borrowed">
                        <div class="status-hero-left">
                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <div>
                                <div class="status-hero-title">Peminjaman Aktif (Sedang Dipinjam)</div>
                                <div class="status-hero-desc">Buku sedang dipinjam oleh anggota. Batas waktu pengembalian sampai tanggal <?= date('d F Y', strtotime($loan['due_date'])) ?>.</div>
                            </div>
                        </div>
                        <span class="badge badge-warning" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">Status: Sedang Dipinjam</span>
                    </div>
                <?php endif; ?>

                <!-- Grid Kartu Informasi Detail -->
                <div class="detail-grid">

                    <!-- 1. Data Transaksi & Waktu -->
                    <div class="detail-card">
                        <div class="detail-card-header">
                            <h3 class="detail-card-title">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Informasi Waktu & Transaksi
                            </h3>
                            <span class="badge badge-primary" style="font-family: monospace;">#TRX-<?= sprintf('%04d', $loan['id']) ?></span>
                        </div>
                        <div class="detail-card-body">
                            <div class="info-row">
                                <span class="info-title">ID Transaksi</span>
                                <span class="info-data" style="font-family: monospace; font-weight: 700;">#TRX-<?= sprintf('%04d', $loan['id']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Tanggal Peminjaman</span>
                                <span class="info-data"><?= date('d F Y', strtotime($loan['loan_date'])) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Tanggal Jatuh Tempo</span>
                                <span class="info-data" style="<?= !empty($loan['is_overdue']) ? 'color: var(--rose, #ef4444); font-weight: 700;' : '' ?>">
                                    <?= date('d F Y', strtotime($loan['due_date'])) ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Tanggal Pengembalian</span>
                                <span class="info-data">
                                    <?php if (!empty($loan['return_date'])): ?>
                                        <span style="color: var(--emerald, #10b981); font-weight: 700;"><?= date('d F Y', strtotime($loan['return_date'])) ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted, #94a3b8); font-style: italic;">Belum dikembalikan</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Status Transaksi</span>
                                <span class="info-data">
                                    <?php if ($loan['status'] === 'returned'): ?>
                                        <span class="badge badge-success">Dikembalikan</span>
                                    <?php elseif (!empty($loan['is_overdue'])): ?>
                                        <span class="badge badge-overdue">Terlambat (<?= $loan['overdue_days'] ?> hr)</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Sedang Dipinjam</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Petugas Pencatat</span>
                                <span class="info-data"><?= htmlspecialchars($loan['admin_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Data Anggota Peminjam -->
                    <div class="detail-card">
                        <div class="detail-card-header">
                            <h3 class="detail-card-title">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Identitas Peminjam
                            </h3>
                            <a href="index.php?url=members" style="font-size: 0.8rem; color: var(--gold-accent, #f59e0b); text-decoration: none; font-weight: 600;">Lihat Anggota &rarr;</a>
                        </div>
                        <div class="detail-card-body">
                            <div class="info-row">
                                <span class="info-title">Nama Lengkap</span>
                                <span class="info-data" style="color: var(--navy, #0d172e); font-weight: 700; font-size: 0.95rem;">
                                    <?= htmlspecialchars($loan['member_name'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Kode Anggota</span>
                                <span class="info-data">
                                    <span class="badge badge-primary" style="font-family: monospace;">
                                        <?= htmlspecialchars($loan['member_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Nomor Telepon</span>
                                <span class="info-data"><?= htmlspecialchars($loan['member_phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-title">Alamat Email</span>
                                <span class="info-data"><?= htmlspecialchars($loan['member_email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Data Buku yang Dipinjam -->
                    <div class="detail-card" style="grid-column: 1 / -1;">
                        <div class="detail-card-header">
                            <h3 class="detail-card-title">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                                Informasi Buku yang Dipinjam
                            </h3>
                            <a href="index.php?url=books" style="font-size: 0.8rem; color: var(--gold-accent, #f59e0b); text-decoration: none; font-weight: 600;">Katalog Buku &rarr;</a>
                        </div>
                        <div class="detail-card-body" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                            <div>
                                <div class="info-row">
                                    <span class="info-title">Judul Buku</span>
                                    <span class="info-data" style="font-size: 1rem; color: var(--navy, #0d172e); font-weight: 700;">
                                        <?= htmlspecialchars($loan['book_title'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-title">Pengarang / Penulis</span>
                                    <span class="info-data"><?= htmlspecialchars($loan['book_author'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                            <div>
                                <div class="info-row">
                                    <span class="info-title">Nomor ISBN</span>
                                    <span class="info-data" style="font-family: monospace;"><?= htmlspecialchars($loan['book_isbn'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-title">Stok Fisik Tersedia</span>
                                    <span class="info-data">
                                        <span class="badge badge-success"><?= (int) ($loan['available_stock'] ?? 0) ?> dari <?= (int) ($loan['total_stock'] ?? 0) ?> Eksemplar</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Aksi Bawah -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <a href="index.php?url=loans" class="btn btn-secondary">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Kembali ke Daftar Transaksi
                    </a>

                    <?php if ($loan['status'] === 'borrowed'): ?>
                        <form action="index.php?url=loans/return" method="POST" style="margin: 0;" onsubmit="return confirm('Konfirmasi pengembalian buku \'<?= htmlspecialchars(addslashes($loan['book_title']), ENT_QUOTES, 'UTF-8') ?>\'? Stok buku akan ditambahkan kembali ke inventaris.');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $loan['id'] ?>">
                            <button type="submit" class="btn btn-success">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                Proses Pengembalian Buku Sekarang
                            </button>
                        </form>
                    <?php endif; ?>
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
