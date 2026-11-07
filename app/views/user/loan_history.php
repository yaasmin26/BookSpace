<?php
/**
 * ========================================================
 * BookSpace - Halaman Riwayat Peminjaman Lengkap
 * Desain Sama Persis dengan Tampilan Admin (MindRise Style)
 * ========================================================
 */
$activeMenu = 'history';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - BookSpace Library</title>
    <!-- Lembar Gaya Utama MindRise Design System (Sama dengan Admin) -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud.css">
</head>
<body>

    <!-- Tata Letak Aplikasi 2 Kolom (Sidebar + Wrapper) -->
    <div class="app-layout">

        <!-- 1. SIDEBAR ANGGOTA (Modular) -->
        <?php require_once __DIR__ . '/../layouts/user_sidebar.php'; ?>

        <!-- 2. KONTEN UTAMA WRAPPER -->
        <div class="app-wrapper">

            <!-- 2.1 HEADER ANGGOTA (Modular) -->
            <?php require_once __DIR__ . '/../layouts/user_header.php'; ?>

            <!-- 2.2 KONTEN UTAMA RIWAYAT -->
            <main class="app-content">

                <!-- Header Halaman -->
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
                    <div class="page-title-box">
                        <h2 class="page-title" style="font-size: 1.65rem; font-weight: 800; color: var(--navy); letter-spacing: -0.5px;">
                            🕘 Riwayat Peminjaman Buku
                        </h2>
                        <p class="page-subtitle" style="font-size: 0.875rem; color: var(--text-secondary);">
                            Arsip seluruh transaksi sirkulasi peminjaman dan pengembalian yang pernah Anda lakukan
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="index.php?url=my-loans" class="btn btn-outline" style="background: var(--white); border: 1px solid var(--border); color: var(--navy); font-weight: 700;">
                            &larr; Peminjaman Aktif
                        </a>
                    </div>
                </div>

                <!-- 3 Kartu Statistik Riwayat -->
                <section class="stats-grid" style="margin-bottom: 2rem;">
                    <!-- Total Transaksi -->
                    <article class="stat-card loans">
                        <div class="stat-header">
                            <span class="stat-title">Total Transaksi</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?= count($allLoans) ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Total transaksi peminjaman tercatat
                        </div>
                    </article>

                    <!-- Sedang Dipinjam -->
                    <article class="stat-card books">
                        <div class="stat-header">
                            <span class="stat-title">Sedang Dipinjam</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?= (int) $activeCount ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                            Buku dalam sirkulasi peminjaman aktif
                        </div>
                    </article>

                    <!-- Sudah Dikembalikan -->
                    <article class="stat-card stock">
                        <div class="stat-header">
                            <span class="stat-title">Sudah Dikembalikan</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?= (int) $returnedCount ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Buku telah dikembalikan ke perpustakaan
                        </div>
                    </article>
                </section>

                <!-- Tabel Riwayat Peminjaman Lengkap -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title-group">
                            <h4 class="card-title">Daftar Arsip Peminjaman</h4>
                            <p class="card-subtitle">Urutan kronologis peminjaman dari yang terbaru</p>
                        </div>
                    </div>

                    <?php if (empty($allLoans)): ?>
                        <div class="empty-state" style="padding: 3.5rem 1.5rem; text-align: center;">
                            <div class="empty-icon" style="width: 50px; height: 50px; margin: 0 auto 0.75rem; background: var(--cream-subtle); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gold-accent);">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <h4 style="font-size: 1rem; font-weight: 800; color: var(--navy); margin-bottom: 0.25rem;">Belum Ada Riwayat Transaksi</h4>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); max-width: 400px; margin: 0 auto 1.25rem;">
                                Anda belum pernah meminjam buku di BookSpace. Mulai jelajahi katalog kami sekarang!
                            </p>
                            <a href="index.php?url=catalog" class="btn btn-primary" style="background: var(--gold-accent); color: var(--navy); border: none; font-weight: 800;">
                                Buka Katalog Koleksi Buku
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background: var(--cream-subtle); border-bottom: 1px solid var(--border);">
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase; width: 50px; text-align: center;">No</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Judul Buku</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Tgl Pinjam</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Jatuh Tempo</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Tgl Dikembalikan</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Status Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($allLoans as $loan): 
                                        $isReturned = ($loan['status'] === 'returned');
                                        $isOverdue  = false;
                                        
                                        if ($isReturned && !empty($loan['return_date'])) {
                                            $isOverdue = strtotime($loan['return_date']) > strtotime($loan['due_date']);
                                        } elseif (!$isReturned) {
                                            $isOverdue = strtotime(date('Y-m-d')) > strtotime($loan['due_date']);
                                        }
                                    ?>
                                        <tr style="border-bottom: 1px solid var(--border);">
                                            <td style="padding: 1rem 1.25rem; text-align: center; color: var(--text-secondary); font-size: 0.85rem; font-weight: 600;">
                                                <?= $no++ ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem;">
                                                <strong style="color: var(--navy); font-size: 0.925rem;">
                                                    <?= htmlspecialchars($loan['book_title'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                                </strong>
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                                                    Penulis: <?= htmlspecialchars($loan['book_author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                                    &bull; Kategori: <?= htmlspecialchars($loan['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                                                </div>
                                            </td>
                                            <td style="padding: 1rem 1.25rem; font-size: 0.85rem; color: var(--text-secondary);">
                                                <?= htmlspecialchars(date('d M Y', strtotime($loan['loan_date'])), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem; font-size: 0.85rem; color: var(--text-secondary);">
                                                <?= htmlspecialchars(date('d M Y', strtotime($loan['due_date'])), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem; font-size: 0.85rem;">
                                                <?php if ($isReturned && !empty($loan['return_date'])): ?>
                                                    <strong style="color: var(--emerald-text);">
                                                        <?= htmlspecialchars(date('d M Y', strtotime($loan['return_date'])), ENT_QUOTES, 'UTF-8') ?>
                                                    </strong>
                                                <?php else: ?>
                                                    <span style="color: var(--text-secondary); font-style: italic;">
                                                        Belum Dikembalikan
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem;">
                                                <?php if ($isReturned): ?>
                                                    <?php if ($isOverdue): ?>
                                                        <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--emerald-light); color: var(--emerald-text); border: 1px solid var(--emerald-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700; margin-right: 0.25rem;">
                                                            Dikembalikan
                                                        </span>
                                                        <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--rose-light); color: var(--rose-text); border: 1px solid var(--rose-border); border-radius: var(--radius-pill); font-size: 0.7rem; font-weight: 700;">
                                                            Terlambat
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--emerald-light); color: var(--emerald-text); border: 1px solid var(--emerald-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                            Sudah Dikembalikan
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if ($isOverdue): ?>
                                                        <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--rose-light); color: var(--rose-text); border: 1px solid var(--rose-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                            Terlambat
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                            Sedang Dipinjam
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            </main>

            <!-- 2.3 FOOTER MODULAR -->
            <?php require_once __DIR__ . '/../layouts/user_footer.php'; ?>

        </div>
    </div>

</body>
</html>
