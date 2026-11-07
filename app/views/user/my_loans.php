<?php
/**
 * ========================================================
 * BookSpace - Halaman Peminjaman Saya (Buku Sedang Dipinjam)
 * Desain Sama Persis dengan Tampilan Admin (MindRise Style)
 * ========================================================
 */
$activeMenu = 'my_loans';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - BookSpace Library</title>
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

            <!-- 2.2 KONTEN UTAMA PEMINJAMAN SAYA -->
            <main class="app-content">

                <!-- Header Halaman -->
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
                    <div class="page-title-box">
                        <h2 class="page-title" style="font-size: 1.65rem; font-weight: 800; color: var(--navy); letter-spacing: -0.5px;">
                            📖 Peminjaman Aktif Saya
                        </h2>
                        <p class="page-subtitle" style="font-size: 0.875rem; color: var(--text-secondary);">
                            Daftar buku perpustakaan yang sedang Anda bawa dan batas waktu pengembaliannya
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="index.php?url=my-loans/history" class="btn btn-outline" style="background: var(--white); border: 1px solid var(--border); color: var(--navy); font-weight: 700;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Lihat Riwayat Selesai &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Banner Informasi Kuota Peminjaman -->
                <div class="content-card" style="padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="stat-icon" style="width: 44px; height: 44px; background: var(--cream-subtle); color: var(--navy); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                        <div>
                            <strong style="color: var(--navy); font-size: 0.95rem;">Status Kuota Peminjaman:</strong>
                            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.15rem;">
                                Anda sedang meminjam <strong><?= (int) $activeLoansCount ?></strong> dari batas maksimal <strong>3 buku aktif</strong>.
                            </p>
                        </div>
                    </div>

                    <?php if ($activeLoansCount < 3): ?>
                        <a href="index.php?url=catalog" class="btn btn-primary" style="background: var(--gold-accent); color: var(--navy); font-weight: 800; border: none; padding: 0.55rem 1.25rem;">
                            + Tambah Pinjaman Buku
                        </a>
                    <?php else: ?>
                        <span style="display: inline-block; padding: 0.35rem 0.85rem; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); border-radius: var(--radius-pill); font-size: 0.8rem; font-weight: 800;">
                            Batas Maksimal Kuota Tercapai
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Konten Tabel Peminjaman Aktif -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title-group">
                            <h4 class="card-title">Daftar Buku Sedang Dipinjam</h4>
                            <p class="card-subtitle">Pengembalian fisik dilakukan di meja sirkulasi perpustakaan</p>
                        </div>
                    </div>

                    <?php if (empty($activeLoans)): ?>
                        <div class="empty-state" style="padding: 3.5rem 1.5rem; text-align: center;">
                            <div class="empty-icon" style="width: 50px; height: 50px; margin: 0 auto 0.75rem; background: var(--cream-subtle); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gold-accent);">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                            <h4 style="font-size: 1rem; font-weight: 800; color: var(--navy); margin-bottom: 0.25rem;">Tidak Ada Buku yang Sedang Dipinjam</h4>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); max-width: 400px; margin: 0 auto 1.25rem;">
                                Anda saat ini tidak memiliki pinjaman buku yang aktif. Buka katalog untuk memilih buku bacaan favorit Anda!
                            </p>
                            <a href="index.php?url=catalog" class="btn btn-primary" style="background: var(--gold-accent); color: var(--navy); border: none; font-weight: 800;">
                                Buka Katalog Koleksi
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background: var(--cream-subtle); border-bottom: 1px solid var(--border);">
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Judul Buku</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Penulis</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Tgl Pinjam</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Batas Kembali</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Sisa Waktu</th>
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activeLoans as $loan): 
                                        $dueDateTs = strtotime($loan['due_date']);
                                        $todayTs   = strtotime(date('Y-m-d'));
                                        $diffDays  = (int) round(($dueDateTs - $todayTs) / 86400);
                                    ?>
                                        <tr style="border-bottom: 1px solid var(--border);">
                                            <td style="padding: 1rem 1.25rem;">
                                                <strong style="color: var(--navy); font-size: 0.925rem;">
                                                    <?= htmlspecialchars($loan['book_title'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                                </strong>
                                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                                                    Kategori: <?= htmlspecialchars($loan['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                                                    <?php if (!empty($loan['isbn'])): ?>
                                                        &bull; ISBN: <?= htmlspecialchars($loan['isbn'], ENT_QUOTES, 'UTF-8') ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td style="padding: 1rem 1.25rem; color: var(--text-primary); font-size: 0.875rem;">
                                                <?= htmlspecialchars($loan['book_author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem; font-size: 0.85rem; color: var(--text-secondary);">
                                                <?= htmlspecialchars(date('d M Y', strtotime($loan['loan_date'])), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem; font-size: 0.85rem; font-weight: 700; color: <?= $diffDays < 0 ? 'var(--rose)' : 'var(--navy)' ?>;">
                                                <?= htmlspecialchars(date('d M Y', $dueDateTs), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem;">
                                                <?php if ($diffDays < 0): ?>
                                                    <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--rose-light); color: var(--rose-text); border: 1px solid var(--rose-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                        Terlambat <?= abs($diffDays) ?> hari
                                                    </span>
                                                <?php elseif ($diffDays === 0): ?>
                                                    <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                        Jatuh tempo hari ini
                                                    </span>
                                                <?php elseif ($diffDays <= 3): ?>
                                                    <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                        Sisa <?= $diffDays ?> hari lagi
                                                    </span>
                                                <?php else: ?>
                                                    <span style="display: inline-block; padding: 0.2rem 0.55rem; background: var(--cream-subtle); color: var(--text-secondary); border: 1px solid var(--border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                        Sisa <?= $diffDays ?> hari
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 1rem 1.25rem;">
                                                <span style="display: inline-block; padding: 0.25rem 0.65rem; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 700;">
                                                    Sedang Dipinjam
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Petunjuk Sirkulasi Pengembalian Buku -->
                        <div style="padding: 1.25rem 1.5rem; background: var(--cream-subtle); border-top: 1px solid var(--border); font-size: 0.8125rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.75rem;">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--navy);">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span>
                                <strong>Petunjuk Pengembalian Buku:</strong> Untuk mengembalikan buku fisik, silakan serahkan ke meja sirkulasi perpustakaan BookSpace. Petugas/Administrator akan memeriksa kondisi fisik buku dan status transaksi akun Anda akan diperbarui secara otomatis.
                            </span>
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
