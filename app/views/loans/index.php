<?php
/**
 * ========================================================
 * BookSpace - Sirkulasi Peminjaman & Pengembalian Buku
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'loans';

$adminName   = $adminName ?? $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken   = $csrfToken ?? $_SESSION['csrf_token'] ?? '';
$keyword     = $keyword ?? ($_GET['q'] ?? '');
$memberQuery = $memberQuery ?? ($_GET['member'] ?? '');
$bookQuery   = $bookQuery ?? ($_GET['book'] ?? '');
$status      = $status ?? ($_GET['status'] ?? '');
$startDate   = $startDate ?? ($_GET['start_date'] ?? '');
$endDate     = $endDate ?? ($_GET['end_date'] ?? '');
$isFiltered  = $isFiltered ?? (!empty($keyword) || !empty($status) || !empty($memberQuery) || !empty($bookQuery) || !empty($startDate) || !empty($endDate));
$totalLoans  = $totalLoans ?? count($loans ?? []);
$filteredLoansCount = $filteredLoansCount ?? count($loans ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sirkulasi Peminjaman Buku - BookSpace</title>
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

            <!-- 2.2 KONTEN UTAMA HALAMAN SIRKULASI -->
            <main class="app-content">

                <!-- Breadcrumb Navigasi -->
                <nav class="breadcrumb">
                    <a href="index.php?url=dashboard">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Sirkulasi Peminjaman Buku</span>
                </nav>

                <!-- Flash Message Notifikasi -->
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
                        <h1 class="page-title">Sirkulasi Peminjaman Buku</h1>
                        <p class="page-subtitle">Pantau seluruh aktivitas sirkulasi peminjaman, batas tanggal jatuh tempo, dan status peminjaman buku perpustakaan.</p>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="index.php?url=returns" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10"></polyline>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                            </svg>
                            Pengembalian Buku
                        </a>
                        <a href="index.php?url=loans/create" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah Peminjaman Baru
                        </a>
                    </div>
                </div>

                <!-- Kartu Indikator Statistik Peminjaman (4 Kartu) -->
                <div class="loan-stat-grid">
                    <!-- Total Transaksi -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Total Transaksi</span>
                            <span class="stat-num"><?= number_format($totalLoans ?? 0) ?></span>
                        </div>
                        <div class="loan-stat-icon icon-blue">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </div>
                    </div>

                    <!-- Sedang Dipinjam -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Sedang Dipinjam</span>
                            <span class="stat-num"><?= number_format($activeLoans ?? 0) ?></span>
                        </div>
                        <div class="loan-stat-icon icon-amber">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Sudah Kembali -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Sudah Kembali</span>
                            <span class="stat-num"><?= number_format($returnedLoans ?? 0) ?></span>
                        </div>
                        <div class="loan-stat-icon icon-emerald">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- Terlambat Kembali -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Terlambat Kembali</span>
                            <span class="stat-num" style="<?= ($overdueLoans ?? 0) > 0 ? 'color: var(--rose, #ef4444);' : '' ?>">
                                <?= number_format($overdueLoans ?? 0) ?>
                            </span>
                        </div>
                        <div class="loan-stat-icon icon-rose">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kartu Tabel & Filter -->
                <div class="crud-card">
                    <!-- Form Pencarian & Filter Multi-Kriteria -->
                    <div class="filter-multi-bar">
                        <form action="index.php" method="GET" style="margin: 0;">
                            <input type="hidden" name="url" value="loans">

                            <!-- Baris Atas: Pencarian Nama Anggota, Judul Buku, dan Filter Status -->
                            <div class="filter-row-top">
                                <!-- 1. Pencarian Nama Anggota -->
                                <div class="filter-input-wrap">
                                    <svg class="input-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <input 
                                        type="text" 
                                        name="member" 
                                        class="filter-text-input" 
                                        placeholder="Cari nama anggota..." 
                                        value="<?= htmlspecialchars($memberQuery ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                </div>

                                <!-- 2. Pencarian Judul Buku -->
                                <div class="filter-input-wrap">
                                    <svg class="input-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <input 
                                        type="text" 
                                        name="book" 
                                        class="filter-text-input" 
                                        placeholder="Cari judul buku..." 
                                        value="<?= htmlspecialchars($bookQuery ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                </div>

                                <!-- 3. Filter Status Peminjaman -->
                                <div>
                                    <select name="status" class="filter-select">
                                        <option value="">Semua Status Peminjaman</option>
                                        <option value="borrowed" <?= ($status === 'borrowed') ? 'selected' : '' ?>>Sedang Dipinjam</option>
                                        <option value="returned" <?= ($status === 'returned') ? 'selected' : '' ?>>Sudah Dikembalikan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Baris Bawah: Filter Tanggal & Tombol Aksi -->
                            <div class="filter-row-bottom">
                                <!-- 4. Filter Rentang Tanggal Peminjaman -->
                                <div class="date-filter-wrap">
                                    <span class="date-filter-label">
                                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        Tanggal Pinjam:
                                    </span>
                                    <input 
                                        type="date" 
                                        name="start_date" 
                                        class="filter-date-input" 
                                        value="<?= htmlspecialchars($startDate ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        title="Tanggal awal peminjaman"
                                    >
                                    <span style="color: var(--text-muted, #94a3b8); font-size: 0.825rem; font-weight: 600;">s/d</span>
                                    <input 
                                        type="date" 
                                        name="end_date" 
                                        class="filter-date-input" 
                                        value="<?= htmlspecialchars($endDate ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        title="Tanggal akhir peminjaman"
                                    >
                                </div>

                                <!-- Tombol Submit & Reset -->
                                <div class="filter-action-group">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        Filter & Cari
                                    </button>

                                    <?php if ($isFiltered): ?>
                                        <a href="index.php?url=loans" class="btn btn-secondary btn-sm" title="Hapus Semua Filter">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                            Reset Filter
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 5. Tampilan Ringkasan Jumlah Transaksi -->
                    <div class="count-summary-bar">
                        <div>
                            Menampilkan <strong><?= number_format(count($loans)) ?></strong> dari <strong><?= number_format($totalLoans) ?></strong> total transaksi peminjaman
                        </div>
                        <?php if ($isFiltered): ?>
                            <div>
                                <span class="badge badge-primary" style="font-size: 0.8rem;">
                                    Filter Aktif: <?= count($loans) ?> data ditemukan
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tabel Data Transaksi Peminjaman -->
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 100px;">ID Transaksi</th>
                                    <th>Peminjam (Anggota)</th>
                                    <th>Buku yang Dipinjam</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Tgl Kembali</th>
                                    <th>Status</th>
                                    <th style="width: 190px; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($loans)): ?>
                                    <tr>
                                        <td colspan="9">
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                        <line x1="9" y1="12" x2="15" y2="12"></line>
                                                    </svg>
                                                </div>
                                                <h3 class="empty-title">Tidak Ada Transaksi Peminjaman Ditemukan</h3>
                                                <p class="empty-desc">
                                                    <?php if ($isFiltered): ?>
                                                        Tidak ada data peminjaman yang cocok dengan kriteria pencarian dan filter Anda.
                                                    <?php else: ?>
                                                        Belum ada transaksi sirkulasi peminjaman buku yang tercatat di sistem BookSpace.
                                                    <?php endif; ?>
                                                </p>
                                                <a href="index.php?url=loans/create" class="btn btn-primary btn-sm">
                                                    Catat Peminjaman Pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($loans as $l): ?>
                                        <tr>
                                            <!-- Nomor Urut -->
                                            <td><?= $no++ ?></td>

                                            <!-- ID Transaksi (Format #TRX-0001) -->
                                            <td>
                                                <span class="badge badge-primary" style="font-family: monospace; font-size: 0.825rem;">
                                                    #TRX-<?= sprintf('%04d', $l['id']) ?>
                                                </span>
                                            </td>

                                            <!-- Nama Anggota & Kode -->
                                            <td>
                                                <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                                    <strong style="color: var(--navy, #0d172e); font-size: 0.925rem;">
                                                        <?= htmlspecialchars($l['member_name'], ENT_QUOTES, 'UTF-8') ?>
                                                    </strong>
                                                    <span style="font-size: 0.8rem; color: var(--text-secondary, #64748b); font-family: monospace;">
                                                        <?= htmlspecialchars($l['member_code'], ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Judul Buku & Pengarang -->
                                            <td>
                                                <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                                    <strong style="color: var(--navy, #0d172e); font-size: 0.925rem;">
                                                        <?= htmlspecialchars($l['book_title'], ENT_QUOTES, 'UTF-8') ?>
                                                    </strong>
                                                    <span style="font-size: 0.8rem; color: var(--text-secondary, #64748b);">
                                                        <?= htmlspecialchars($l['book_author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                                        <?php if (!empty($l['book_isbn'])): ?>
                                                            &bull; ISBN: <?= htmlspecialchars($l['book_isbn'], ENT_QUOTES, 'UTF-8') ?>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Tanggal Peminjaman -->
                                            <td>
                                                <span style="font-size: 0.875rem; color: var(--text-primary, #1e293b); font-weight: 500;">
                                                    <?= date('d/m/Y', strtotime($l['loan_date'])) ?>
                                                </span>
                                            </td>

                                            <!-- Tanggal Jatuh Tempo -->
                                            <td>
                                                <span style="font-size: 0.875rem; font-weight: 600; color: <?= !empty($l['is_overdue']) ? 'var(--rose, #ef4444)' : 'var(--text-primary, #1e293b)' ?>;">
                                                    <?= date('d/m/Y', strtotime($l['due_date'])) ?>
                                                </span>
                                            </td>

                                            <!-- Tanggal Pengembalian -->
                                            <td>
                                                <?php if (!empty($l['return_date'])): ?>
                                                    <span style="font-size: 0.875rem; color: var(--emerald, #10b981); font-weight: 600;">
                                                        <?= date('d/m/Y', strtotime($l['return_date'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: var(--text-muted, #94a3b8); font-style: italic;">Belum kembali</span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Status Transaksi -->
                                            <td>
                                                <?php if ($l['status'] === 'returned'): ?>
                                                    <span class="badge badge-success">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px;">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        Dikembalikan
                                                    </span>
                                                <?php elseif (!empty($l['is_overdue'])): ?>
                                                    <span class="badge badge-overdue" title="Terlambat <?= $l['overdue_days'] ?> hari dari tanggal jatuh tempo">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px;">
                                                            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                        </svg>
                                                        Terlambat (<?= $l['overdue_days'] ?> hr)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px;">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <polyline points="12 6 12 12 16 14"></polyline>
                                                        </svg>
                                                        Sedang Dipinjam
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Tombol Aksi -->
                                            <td style="text-align: right;">
                                                <div class="table-actions" style="justify-content: flex-end;">
                                                    <!-- Tombol Detail Transaksi -->
                                                    <a 
                                                        href="index.php?url=loans/detail&id=<?= (int) $l['id'] ?>" 
                                                        class="btn btn-sm btn-secondary" 
                                                        title="Lihat Detail Transaksi"
                                                    >
                                                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                        </svg>
                                                        Detail
                                                    </a>

                                                    <!-- Tombol Pengembalian (Hanya jika status 'borrowed') -->
                                                    <?php if ($l['status'] === 'borrowed'): ?>
                                                        <form action="index.php?url=loans/return" method="POST" style="margin: 0;" onsubmit="return confirm('Konfirmasi pengembalian buku \'<?= htmlspecialchars(addslashes($l['book_title']), ENT_QUOTES, 'UTF-8') ?>\' yang dipinjam oleh <?= htmlspecialchars(addslashes($l['member_name']), ENT_QUOTES, 'UTF-8') ?>? Stok buku akan otomatis ditambahkan kembali.');">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                                                            <button 
                                                                type="submit" 
                                                                class="btn btn-sm btn-success" 
                                                                title="Kembalikan Buku Ini"
                                                            >
                                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                                </svg>
                                                                Kembalikan
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
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

    <!-- Modal Popup Detail Transaksi -->
    <div id="loanDetailModal" class="modal-overlay" onclick="handleOverlayClick(event)">
        <div class="modal-container">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="modalTrxCode">Detail Peminjaman</h3>
                    <span id="modalStatusBadge"></span>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeLoanModal()" aria-label="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <span class="detail-label">Nama Peminjam</span>
                    <span class="detail-val" id="modalMemberName">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Kode Anggota</span>
                    <span class="detail-val" id="modalMemberCode" style="font-family: monospace;">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Kontak Peminjam</span>
                    <span class="detail-val" id="modalMemberContact">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Judul Buku</span>
                    <span class="detail-val" id="modalBookTitle">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pengarang / ISBN</span>
                    <span class="detail-val" id="modalBookInfo">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pinjam</span>
                    <span class="detail-val" id="modalLoanDate">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Batas Jatuh Tempo</span>
                    <span class="detail-val" id="modalDueDate">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pengembalian</span>
                    <span class="detail-val" id="modalReturnDate">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Petugas Pencatat</span>
                    <span class="detail-val" id="modalAdminName">-</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeLoanModal()">Tutup</button>
                <div id="modalReturnActionContainer"></div>
            </div>
        </div>
    </div>

    <!-- Script Interaktif Modal Detail -->
    <script>
        function openLoanDetail(data) {
            document.getElementById('modalTrxCode').innerText = 'Detail Transaksi ' + data.trx_code;
            document.getElementById('modalMemberName').innerText = data.member_name;
            document.getElementById('modalMemberCode').innerText = data.member_code;
            document.getElementById('modalMemberContact').innerText = data.member_phone + ' (' + data.member_email + ')';
            document.getElementById('modalBookTitle').innerText = data.book_title;
            document.getElementById('modalBookInfo').innerText = data.book_author + (data.book_isbn !== '-' ? ' (ISBN: ' + data.book_isbn + ')' : '');
            document.getElementById('modalLoanDate').innerText = data.loan_date;
            document.getElementById('modalDueDate').innerText = data.due_date;
            document.getElementById('modalReturnDate').innerText = data.return_date;
            document.getElementById('modalAdminName').innerText = data.admin_name;

            // Render Status Badge di Modal
            const badgeContainer = document.getElementById('modalStatusBadge');
            if (data.status === 'returned') {
                badgeContainer.innerHTML = '<span class="badge badge-success" style="margin-top: 4px; display: inline-block;">Dikembalikan</span>';
            } else if (data.is_overdue) {
                badgeContainer.innerHTML = '<span class="badge badge-overdue" style="margin-top: 4px; display: inline-block;">Terlambat (' + data.overdue_days + ' hari)</span>';
            } else {
                badgeContainer.innerHTML = '<span class="badge badge-warning" style="margin-top: 4px; display: inline-block;">Sedang Dipinjam</span>';
            }

            // Render Tombol Kembalikan di Modal jika buku masih dipinjam
            const actionContainer = document.getElementById('modalReturnActionContainer');
            if (data.status === 'borrowed') {
                actionContainer.innerHTML = `
                    <form action="index.php?url=loans/return" method="POST" style="margin: 0;" onsubmit="return confirm('Konfirmasi pengembalian buku ini? Stok akan otomatis bertambah.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="id" value="${data.id}">
                        <button type="submit" class="btn btn-success">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            Kembalikan Sekarang
                        </button>
                    </form>
                `;
            } else {
                actionContainer.innerHTML = '';
            }

            document.getElementById('loanDetailModal').classList.add('active');
        }

        function closeLoanModal() {
            document.getElementById('loanDetailModal').classList.remove('active');
        }

        function handleOverlayClick(e) {
            if (e.target.id === 'loanDetailModal') {
                closeLoanModal();
            }
        }

        // Tutup modal dengan tombol keyboard Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLoanModal();
            }
        });
    </script>

</body>
</html>
