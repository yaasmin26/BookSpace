<?php
/**
 * ========================================================
 * BookSpace - Pengembalian Buku (Dedicated Return Management)
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'returns';

$adminName     = $adminName ?? $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken     = $csrfToken ?? $_SESSION['csrf_token'] ?? '';
$keyword       = $keyword ?? ($_GET['q'] ?? '');
$memberQuery   = $memberQuery ?? ($_GET['member'] ?? '');
$bookQuery     = $bookQuery ?? ($_GET['book'] ?? '');
$startDate     = $startDate ?? ($_GET['start_date'] ?? '');
$endDate       = $endDate ?? ($_GET['end_date'] ?? '');
$activeTab     = $activeTab ?? ($_GET['tab'] ?? 'queue');
$isFiltered    = $isFiltered ?? (!empty($keyword) || !empty($memberQuery) || !empty($bookQuery) || !empty($startDate) || !empty($endDate));

$totalPending  = $totalPending ?? count($pendingLoans ?? []);
$totalOverdue  = $totalOverdue ?? 0;
$returnedToday = $returnedToday ?? 0;
$totalReturned = $totalReturned ?? count($historyLoans ?? []);

$pendingCount  = count($pendingLoans ?? []);
$historyCount  = count($historyLoans ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - BookSpace</title>
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

            <!-- 2.2 KONTEN UTAMA HALAMAN PENGEMBALIAN BUKU -->
            <main class="app-content">

                <!-- Breadcrumb Navigasi -->
                <nav class="breadcrumb">
                    <a href="index.php?url=dashboard">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="index.php?url=loans">Sirkulasi</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Pengembalian Buku</span>
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
                        <h1 class="page-title">Pengembalian Buku</h1>
                        <p class="page-subtitle">Kelola penerimaan kembali buku pinjaman, verifikasi tanggal jatuh tempo dan denda keterlambatan, serta perbarui stok buku secara otomatis.</p>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="index.php?url=loans" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                                <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                                <path d="M9 14l2 2 4-4"></path>
                            </svg>
                            Sirkulasi Peminjaman
                        </a>
                        <a href="index.php?url=loans/create" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Peminjaman Baru
                        </a>
                    </div>
                </div>

                <!-- Kartu Indikator Statistik Pengembalian (4 Kartu) -->
                <div class="loan-stat-grid">
                    <!-- 1. Antrean Belum Kembali -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Antrean Belum Kembali</span>
                            <span class="stat-num" style="<?= ($totalPending > 0) ? 'color: var(--navy, #0d172e);' : '' ?>">
                                <?= number_format($totalPending) ?>
                            </span>
                        </div>
                        <div class="loan-stat-icon icon-amber">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- 2. Terlambat Kembali -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Terlambat Kembali</span>
                            <span class="stat-num" style="<?= ($totalOverdue > 0) ? 'color: var(--rose, #ef4444);' : '' ?>">
                                <?= number_format($totalOverdue) ?>
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

                    <!-- 3. Dikembalikan Hari Ini -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Kembali Hari Ini</span>
                            <span class="stat-num" style="<?= ($returnedToday > 0) ? 'color: var(--emerald, #10b981);' : '' ?>">
                                <?= number_format($returnedToday) ?>
                            </span>
                        </div>
                        <div class="loan-stat-icon icon-emerald">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>

                    <!-- 4. Total Selesai Dikembalikan -->
                    <div class="loan-stat-item">
                        <div class="stat-info">
                            <span class="stat-label">Total Selesai Kembali</span>
                            <span class="stat-num"><?= number_format($totalReturned) ?></span>
                        </div>
                        <div class="loan-stat-icon icon-blue">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10"></polyline>
                                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Kartu Utama: Filter, Tab Navigasi, dan Tabel -->
                <div class="crud-card">

                    <!-- Form Pencarian Nama Anggota dan Judul Buku -->
                    <div class="filter-multi-bar">
                        <form action="index.php" method="GET" style="margin: 0;" id="returnFilterForm">
                            <input type="hidden" name="url" value="returns">
                            <input type="hidden" name="tab" id="filterTabInput" value="<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>">

                            <!-- Baris Atas: Pencarian Nama Anggota dan Judul Buku -->
                            <div class="filter-row-top" style="grid-template-columns: 1fr 1fr 200px;">
                                <!-- 1. Pencarian Nama Anggota / Kode -->
                                <div class="filter-input-wrap">
                                    <svg class="input-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <input 
                                        type="text" 
                                        name="member" 
                                        class="filter-text-input" 
                                        placeholder="Cari nama anggota atau kode anggota..." 
                                        value="<?= htmlspecialchars($memberQuery ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                </div>

                                <!-- 2. Pencarian Judul Buku / ISBN -->
                                <div class="filter-input-wrap">
                                    <svg class="input-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <input 
                                        type="text" 
                                        name="book" 
                                        class="filter-text-input" 
                                        placeholder="Cari judul buku atau ISBN..." 
                                        value="<?= htmlspecialchars($bookQuery ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                </div>

                                <!-- Tombol Aksi Filter & Reset -->
                                <div class="filter-action-group" style="justify-content: flex-end;">
                                    <button type="submit" class="btn btn-primary" style="height: 42px; width: 100%;">
                                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        Cari Data
                                    </button>
                                </div>
                            </div>

                            <!-- Baris Bawah: Filter Rentang Tanggal Pinjam (Opsional) -->
                            <div class="filter-row-bottom">
                                <div class="date-filter-wrap">
                                    <span class="date-filter-label">
                                        <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        Rentang Tanggal Pinjam:
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

                                <div class="filter-action-group">
                                    <?php if ($isFiltered): ?>
                                        <a href="index.php?url=returns&tab=<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary btn-sm" title="Hapus filter pencarian">
                                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                            Reset Pencarian
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Navigasi Antara Antrean dan Riwayat Selesai -->
                    <div class="return-tabs-nav" role="tablist">
                        <!-- Tab 1: Antrean Pengembalian (Buku Belum Kembali) -->
                        <button 
                            type="button" 
                            class="return-tab-btn <?= ($activeTab === 'queue') ? 'active' : '' ?>" 
                            id="tabBtnQueue" 
                            onclick="switchReturnTab('queue')"
                            role="tab"
                            aria-selected="<?= ($activeTab === 'queue') ? 'true' : 'false' ?>"
                        >
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Antrean Belum Kembali</span>
                            <span class="return-tab-pill <?= ($pendingCount > 0) ? 'pill-amber' : '' ?>">
                                <?= number_format($pendingCount) ?>
                            </span>
                        </button>

                        <!-- Tab 2: Riwayat Pengembalian Selesai -->
                        <button 
                            type="button" 
                            class="return-tab-btn <?= ($activeTab === 'history') ? 'active' : '' ?>" 
                            id="tabBtnHistory" 
                            onclick="switchReturnTab('history')"
                            role="tab"
                            aria-selected="<?= ($activeTab === 'history') ? 'true' : 'false' ?>"
                        >
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span>Riwayat Selesai Dikembalikan</span>
                            <span class="return-tab-pill <?= ($historyCount > 0) ? 'pill-emerald' : '' ?>">
                                <?= number_format($historyCount) ?>
                            </span>
                        </button>
                    </div>

                    <!-- ========================================================
                         TAB 1 PANE: ANTREAN PENGEMBALIAN (BUKU BELUM KEMBALI)
                         ======================================================== -->
                    <div class="tab-pane <?= ($activeTab === 'queue') ? 'active' : '' ?>" id="paneQueue">

                        <!-- Ringkasan Jumlah Antrean -->
                        <div class="count-summary-bar">
                            <div>
                                Menampilkan <strong><?= number_format($pendingCount) ?></strong> transaksi peminjaman aktif yang menunggu pengembalian buku.
                            </div>
                            <?php if ($totalOverdue > 0): ?>
                                <div>
                                    <span class="badge badge-overdue" style="font-size: 0.8rem;">
                                        Perhatian: <?= $totalOverdue ?> transaksi telah melewati tanggal jatuh tempo!
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tabel Antrean Belum Kembali -->
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 105px;">ID Transaksi</th>
                                        <th>Peminjam (Anggota)</th>
                                        <th>Buku yang Dipinjam</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Status Keterlambatan</th>
                                        <th style="width: 200px; text-align: right;">Aksi Pengembalian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pendingLoans)): ?>
                                        <tr>
                                            <td colspan="8">
                                                <div class="empty-state">
                                                    <div class="empty-icon" style="background-color: var(--emerald-light, #ecfdf5); color: var(--emerald, #10b981);">
                                                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                        </svg>
                                                    </div>
                                                    <h3 class="empty-title">Semua Buku Telah Dikembalikan!</h3>
                                                    <p class="empty-desc">
                                                        <?php if ($isFiltered): ?>
                                                            Tidak ada data transaksi peminjaman aktif yang sesuai dengan kata kunci pencarian Anda.
                                                        <?php else: ?>
                                                            Saat ini tidak ada antrean buku yang sedang dipinjam. Seluruh koleksi perpustakaan aman di rak.
                                                        <?php endif; ?>
                                                    </p>
                                                    <div style="display: flex; gap: 0.75rem; justify-content: center;">
                                                        <?php if ($isFiltered): ?>
                                                            <a href="index.php?url=returns&tab=queue" class="btn btn-secondary btn-sm">Reset Pencarian</a>
                                                        <?php endif; ?>
                                                        <a href="index.php?url=loans/create" class="btn btn-primary btn-sm">Catat Peminjaman Baru</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $noQueue = 1; foreach ($pendingLoans as $l): ?>
                                            <tr>
                                                <!-- Nomor Urut -->
                                                <td><?= $noQueue++ ?></td>

                                                <!-- ID Transaksi -->
                                                <td>
                                                    <span class="badge badge-primary" style="font-family: monospace; font-size: 0.825rem;">
                                                        #TRX-<?= sprintf('%04d', $l['id']) ?>
                                                    </span>
                                                </td>

                                                <!-- Anggota / Peminjam -->
                                                <td>
                                                    <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                                        <strong style="color: var(--navy, #0d172e); font-size: 0.925rem;">
                                                            <?= htmlspecialchars($l['member_name'], ENT_QUOTES, 'UTF-8') ?>
                                                        </strong>
                                                        <span style="font-size: 0.8rem; color: var(--text-secondary, #64748b); font-family: monospace;">
                                                            <?= htmlspecialchars($l['member_code'], ENT_QUOTES, 'UTF-8') ?>
                                                            <?php if (!empty($l['member_phone'])): ?>
                                                                &bull; <?= htmlspecialchars($l['member_phone'], ENT_QUOTES, 'UTF-8') ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                </td>

                                                <!-- Judul Buku & Info -->
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
                                                    <span style="font-size: 0.875rem; font-weight: 700; color: <?= !empty($l['is_overdue']) ? 'var(--rose, #ef4444)' : 'var(--text-primary, #1e293b)' ?>;">
                                                        <?= date('d/m/Y', strtotime($l['due_date'])) ?>
                                                    </span>
                                                </td>

                                                <!-- Status Keterlambatan -->
                                                <td>
                                                    <?php if (!empty($l['is_overdue'])): ?>
                                                        <span class="badge badge-overdue" title="Jatuh tempo pada <?= date('d/m/Y', strtotime($l['due_date'])) ?>">
                                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                            </svg>
                                                            Terlambat (<?= (int) $l['overdue_days'] ?> hr)
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">
                                                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <polyline points="12 6 12 12 16 14"></polyline>
                                                            </svg>
                                                            Tepat Waktu
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <!-- Tombol Aksi Pengembalian -->
                                                <td style="text-align: right;">
                                                    <div class="table-actions" style="justify-content: flex-end;">
                                                        <!-- Tombol Detail Transaksi -->
                                                        <button 
                                                            type="button" 
                                                            class="btn btn-sm btn-secondary" 
                                                            title="Lihat Detail Transaksi"
                                                            onclick="openLoanDetail(<?= htmlspecialchars(json_encode([
                                                                'id' => (int) $l['id'],
                                                                'member_name' => $l['member_name'],
                                                                'member_code' => $l['member_code'],
                                                                'member_contact' => ($l['member_phone'] ?? '-') . ' / ' . ($l['member_email'] ?? '-'),
                                                                'book_title' => $l['book_title'],
                                                                'book_isbn' => $l['book_isbn'] ?? '-',
                                                                'book_author' => $l['book_author'] ?? '-',
                                                                'loan_date' => date('d/m/Y', strtotime($l['loan_date'])),
                                                                'due_date' => date('d/m/Y', strtotime($l['due_date'])),
                                                                'return_date' => '-',
                                                                'status' => 'borrowed',
                                                                'is_overdue' => !empty($l['is_overdue']),
                                                                'overdue_days' => $l['overdue_days'] ?? 0
                                                            ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                        >
                                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                            </svg>
                                                            Detail
                                                        </button>

                                                        <!-- Tombol Proses Pengembalian (POST Form) -->
                                                        <form action="index.php?url=returns/process" method="POST" style="margin: 0;" onsubmit="return confirm('Konfirmasi pengembalian buku \'<?= htmlspecialchars(addslashes($l['book_title']), ENT_QUOTES, 'UTF-8') ?>\' oleh anggota <?= htmlspecialchars(addslashes($l['member_name']), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars(addslashes($l['member_code']), ENT_QUOTES, 'UTF-8') ?>)?\n\nStok fisik buku akan otomatis ditambahkan kembali ke katalog perpustakaan.');">
                                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                            <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                                                            <button 
                                                                type="submit" 
                                                                class="btn btn-sm btn-success" 
                                                                title="Proses Pengembalian Buku Ini"
                                                            >
                                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <polyline points="1 4 1 10 7 10"></polyline>
                                                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                                                </svg>
                                                                Proses Pengembalian
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

                    <!-- ========================================================
                         TAB 2 PANE: RIWAYAT PENGEMBALIAN SELESAI
                         ======================================================== -->
                    <div class="tab-pane <?= ($activeTab === 'history') ? 'active' : '' ?>" id="paneHistory">

                        <!-- Ringkasan Jumlah Riwayat -->
                        <div class="count-summary-bar">
                            <div>
                                Menampilkan <strong><?= number_format($historyCount) ?></strong> riwayat pengembalian buku yang telah selesai diproses.
                            </div>
                            <div>
                                <span class="badge badge-success" style="font-size: 0.8rem;">
                                    Status Selesai: Stok buku telah dikembalikan
                                </span>
                            </div>
                        </div>

                        <!-- Tabel Riwayat Selesai Dikembalikan -->
                        <div class="table-responsive">
                            <table class="table-custom">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th style="width: 105px;">ID Transaksi</th>
                                        <th>Peminjam (Anggota)</th>
                                        <th>Buku yang Dikembalikan</th>
                                        <th>Tgl Pinjam</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Tgl Dikembalikan</th>
                                        <th>Status Pengembalian</th>
                                        <th style="width: 100px; text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($historyLoans)): ?>
                                        <tr>
                                            <td colspan="9">
                                                <div class="empty-state">
                                                    <div class="empty-icon">
                                                        <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                                            <polyline points="10 9 9 9 8 9"></polyline>
                                                        </svg>
                                                    </div>
                                                    <h3 class="empty-title">Belum Ada Riwayat Pengembalian</h3>
                                                    <p class="empty-desc">
                                                        <?php if ($isFiltered): ?>
                                                            Tidak ada riwayat pengembalian yang sesuai dengan kriteria filter Anda.
                                                        <?php else: ?>
                                                            Belum ada buku pinjaman yang tercatat selesai dikembalikan di sistem BookSpace.
                                                        <?php endif; ?>
                                                    </p>
                                                    <?php if ($isFiltered): ?>
                                                        <a href="index.php?url=returns&tab=history" class="btn btn-secondary btn-sm">Reset Pencarian</a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $noHist = 1; foreach ($historyLoans as $l): ?>
                                            <tr>
                                                <!-- Nomor Urut -->
                                                <td><?= $noHist++ ?></td>

                                                <!-- ID Transaksi -->
                                                <td>
                                                    <span class="badge badge-primary" style="font-family: monospace; font-size: 0.825rem;">
                                                        #TRX-<?= sprintf('%04d', $l['id']) ?>
                                                    </span>
                                                </td>

                                                <!-- Anggota / Peminjam -->
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

                                                <!-- Judul Buku & Info -->
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
                                                    <span style="font-size: 0.875rem; color: var(--text-secondary, #64748b);">
                                                        <?= date('d/m/Y', strtotime($l['due_date'])) ?>
                                                    </span>
                                                </td>

                                                <!-- Tanggal Pengembalian -->
                                                <td>
                                                    <span style="font-size: 0.875rem; color: var(--emerald, #10b981); font-weight: 700;">
                                                        <?= !empty($l['return_date']) ? date('d/m/Y', strtotime($l['return_date'])) : '-' ?>
                                                    </span>
                                                </td>

                                                <!-- Status Selesai -->
                                                <td>
                                                    <span class="badge badge-success">
                                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px;">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        Dikembalikan
                                                    </span>
                                                </td>

                                                <!-- Tombol Detail -->
                                                <td style="text-align: right;">
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-sm btn-secondary" 
                                                        title="Lihat Detail Transaksi"
                                                        onclick="openLoanDetail(<?= htmlspecialchars(json_encode([
                                                            'id' => (int) $l['id'],
                                                            'member_name' => $l['member_name'],
                                                            'member_code' => $l['member_code'],
                                                            'member_contact' => ($l['member_phone'] ?? '-') . ' / ' . ($l['member_email'] ?? '-'),
                                                            'book_title' => $l['book_title'],
                                                            'book_isbn' => $l['book_isbn'] ?? '-',
                                                            'book_author' => $l['book_author'] ?? '-',
                                                            'loan_date' => date('d/m/Y', strtotime($l['loan_date'])),
                                                            'due_date' => date('d/m/Y', strtotime($l['due_date'])),
                                                            'return_date' => !empty($l['return_date']) ? date('d/m/Y', strtotime($l['return_date'])) : '-',
                                                            'status' => 'returned',
                                                            'is_overdue' => false,
                                                            'overdue_days' => 0
                                                        ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                    >
                                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                                        </svg>
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

    <!-- Modal Popup Detail Transaksi Pengembalian -->
    <div id="returnDetailModal" class="modal-overlay" onclick="handleModalOverlayClick(event)">
        <div class="modal-container">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="modalTrxCode">Detail Transaksi Sirkulasi</h3>
                    <span id="modalStatusBadge"></span>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeReturnModal()" aria-label="Tutup">&times;</button>
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
                    <span class="detail-label">Pengarang & ISBN</span>
                    <span class="detail-val" id="modalBookMeta">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pinjam</span>
                    <span class="detail-val" id="modalLoanDate">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jatuh Tempo</span>
                    <span class="detail-val" id="modalDueDate">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pengembalian</span>
                    <span class="detail-val" id="modalReturnDate">-</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeReturnModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Interaktif untuk Tab Switching & Modal Detail -->
    <script>
        function switchReturnTab(tabName) {
            // Perbarui class tombol tab
            var btnQueue = document.getElementById('tabBtnQueue');
            var btnHistory = document.getElementById('tabBtnHistory');
            var paneQueue = document.getElementById('paneQueue');
            var paneHistory = document.getElementById('paneHistory');
            var filterTabInput = document.getElementById('filterTabInput');

            if (tabName === 'history') {
                btnQueue.classList.remove('active');
                btnQueue.setAttribute('aria-selected', 'false');
                btnHistory.classList.add('active');
                btnHistory.setAttribute('aria-selected', 'true');

                paneQueue.classList.remove('active');
                paneHistory.classList.add('active');
                if (filterTabInput) filterTabInput.value = 'history';
            } else {
                btnHistory.classList.remove('active');
                btnHistory.setAttribute('aria-selected', 'false');
                btnQueue.classList.add('active');
                btnQueue.setAttribute('aria-selected', 'true');

                paneHistory.classList.remove('active');
                paneQueue.classList.add('active');
                if (filterTabInput) filterTabInput.value = 'queue';
            }

            // Update URL search query tanpa reload
            if (window.history && window.history.replaceState) {
                var url = new URL(window.location);
                url.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', url);
            }
        }

        function openLoanDetail(data) {
            var modal = document.getElementById('returnDetailModal');
            if (!modal || !data) return;

            document.getElementById('modalTrxCode').textContent = '#TRX-' + String(data.id).padStart(4, '0');
            document.getElementById('modalMemberName').textContent = data.member_name || '-';
            document.getElementById('modalMemberCode').textContent = data.member_code || '-';
            document.getElementById('modalMemberContact').textContent = data.member_contact || '-';
            document.getElementById('modalBookTitle').textContent = data.book_title || '-';
            document.getElementById('modalBookMeta').textContent = (data.book_author || '-') + ' | ISBN: ' + (data.book_isbn || '-');
            document.getElementById('modalLoanDate').textContent = data.loan_date || '-';
            document.getElementById('modalDueDate').textContent = data.due_date || '-';
            document.getElementById('modalReturnDate').textContent = data.return_date || '-';

            var badgeEl = document.getElementById('modalStatusBadge');
            if (data.status === 'returned') {
                badgeEl.className = 'badge badge-success';
                badgeEl.textContent = 'Dikembalikan';
            } else if (data.is_overdue) {
                badgeEl.className = 'badge badge-overdue';
                badgeEl.textContent = 'Terlambat (' + data.overdue_days + ' hari)';
            } else {
                badgeEl.className = 'badge badge-warning';
                badgeEl.textContent = 'Sedang Dipinjam';
            }

            modal.style.display = 'flex';
        }

        function closeReturnModal() {
            var modal = document.getElementById('returnDetailModal');
            if (modal) modal.style.display = 'none';
        }

        function handleModalOverlayClick(e) {
            if (e.target && e.target.id === 'returnDetailModal') {
                closeReturnModal();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeReturnModal();
            }
        });
    </script>

</body>
</html>
