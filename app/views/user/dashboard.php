<?php
/**
 * ========================================================
 * BookSpace - Dashboard Anggota Perpustakaan
 * Desain Sama Persis dengan Dashboard Admin (MindRise Style)
 * ========================================================
 */
$activeMenu = 'dashboard';
$userName   = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota - BookSpace</title>
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

            <!-- 2.2 KONTEN UTAMA DASHBOARD -->
            <main class="app-content">

                <!-- 1. HERO SECTION (MindRise Inspired) -->
                <section class="hero-card">
                    <div class="hero-body">
                        <div class="hero-pill">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            Panel Layanan Mandiri Anggota
                        </div>
                        <h2 class="hero-title">
                            Selamat Datang di <span class="highlight-gold">BookSpace</span>, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>!
                        </h2>
                        <p class="hero-subtitle">
                            Jelajahi ribuan koleksi katalog buku digital, lakukan peminjaman mandiri secara langsung, dan pantau masa jatuh tempo peminjaman Anda secara transparan.
                        </p>
                        <div class="hero-actions">
                            <a href="index.php?url=catalog" class="btn hero-btn-primary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <span>Katalog Buku</span>
                            </a>
                            <a href="index.php?url=my-loans" class="btn hero-btn-secondary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                                    <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                                    <path d="M9 14l2 2 4-4"></path>
                                </svg>
                                <span>Peminjaman Saya</span>
                            </a>
                            <a href="index.php?url=my-loans/history" class="btn hero-btn-secondary">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>Riwayat Selesai</span>
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

                <!-- 2. STATISTIK ANGGOTA (4 KARTU) -->
                <div class="section-header-box">
                    <div class="section-title-group">
                        <h3 class="section-main-title">Status Sirkulasi Saya</h3>
                        <p class="section-sub-title">Ringkasan aktivitas peminjaman dan ketersediaan koleksi</p>
                    </div>
                </div>

                <section class="stats-grid">
                    <!-- Kartu 1: Pinjaman Aktif -->
                    <article class="stat-card loans">
                        <div class="stat-header">
                            <span class="stat-title">Buku Sedang Dipinjam</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                                    <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                                    <path d="M9 14l2 2 4-4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?= (int) $activeLoansCount ?> <span style="font-size: 1rem; color: var(--text-secondary); font-weight: 600;">/ 3</span></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                            Buku dalam masa pinjaman aktif
                        </div>
                    </article>

                    <!-- Kartu 2: Total Riwayat -->
                    <article class="stat-card members">
                        <div class="stat-header">
                            <span class="stat-title">Total Riwayat Pinjam</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?= (int) $totalLoansCount ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Total transaksi peminjaman selesai
                        </div>
                    </article>

                    <!-- Kartu 3: Koleksi Tersedia -->
                    <article class="stat-card stock">
                        <div class="stat-header">
                            <span class="stat-title">Koleksi Siap Dipinjam</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value"><?= (int) $availableBooksCount ?></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 14 14"></polyline>
                            </svg>
                            Judul buku dengan stok tersedia
                        </div>
                    </article>

                    <!-- Kartu 4: Kuota Peminjaman -->
                    <article class="stat-card books">
                        <div class="stat-header">
                            <span class="stat-title">Batas Maksimal Kuota</span>
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-value">3 <span style="font-size: 1rem; color: var(--text-secondary); font-weight: 600;">Buku</span></div>
                        <div class="stat-desc">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Maksimal 3 buku aktif per anggota
                        </div>
                    </article>
                </section>

                <!-- 3. PEMINJAMAN AKTIF SAYA -->
                <section class="content-card" style="margin-bottom: 2rem;">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="card-title-group">
                            <h4 class="card-title">📖 Peminjaman Aktif Saya</h4>
                            <p class="card-subtitle">Buku yang sedang Anda bawa beserta batas waktu pengembaliannya</p>
                        </div>
                        <?php if (!empty($activeLoans)): ?>
                            <a href="index.php?url=my-loans" class="card-action-link">
                                <span>Lihat Selengkapnya</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($activeLoans)): ?>
                        <div class="empty-state" style="padding: 2.5rem 1.5rem; text-align: center;">
                            <div class="empty-icon" style="width: 50px; height: 50px; margin: 0 auto 0.75rem; background: var(--cream-subtle); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--gold-accent);">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                            </div>
                            <h4 style="font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 0.25rem;">Tidak Ada Buku yang Sedang Dipinjam</h4>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); max-width: 400px; margin: 0 auto 1rem;">
                                Anda saat ini tidak memiliki pinjaman aktif. Buka katalog untuk memilih buku bacaan favorit Anda!
                            </p>
                            <a href="index.php?url=catalog" class="btn btn-primary" style="background: var(--gold-accent); color: var(--navy); border: none; font-weight: 800;">
                                Jelajahi Katalog Buku
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
                                        <th style="padding: 0.85rem 1.25rem; font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase;">Jatuh Tempo</th>
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
                    <?php endif; ?>
                </section>

                <!-- 4. KOLEKSI BUKU TERBARU -->
                <section class="content-card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="card-title-group">
                            <h4 class="card-title">📚 Koleksi Siap Dipinjam</h4>
                            <p class="card-subtitle">Pilih buku bacaan menarik dan lakukan peminjaman mandiri secara online</p>
                        </div>
                        <a href="index.php?url=catalog" class="card-action-link">
                            <span>Buka Katalog Lengkap</span>
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    </div>

                    <div style="padding: 1.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
                            <?php foreach ($featuredBooks as $book): 
                                $isCurrentlyBorrowed = in_array((int)$book['id'], $activeLoanBookIds, true);
                                $isStockAvailable   = (int)$book['available_stock'] > 0;
                            ?>
                                <article style="background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-sm); transition: transform 0.15s ease;">
                                    <div>
                                        <!-- Cover Ilustrasi Buku MindRise -->
                                        <div style="width: 100%; height: 130px; background: linear-gradient(135deg, var(--navy), var(--navy-hover)); border-radius: var(--radius-sm); padding: 0.85rem; color: var(--white); margin-bottom: 0.85rem; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.15);">
                                            <span style="font-size: 0.65rem; font-weight: 800; background: rgba(245, 158, 11, 0.25); color: var(--gold-accent); padding: 0.15rem 0.45rem; border-radius: 4px; width: fit-content; text-transform: uppercase;">
                                                <?= htmlspecialchars($book['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <div style="font-size: 0.95rem; font-weight: 800; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        </div>

                                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--navy); margin-bottom: 0.35rem; line-height: 1.3;">
                                            <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </h4>
                                        <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 0.85rem;">
                                            Penulis: <strong><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        </p>
                                    </div>

                                    <div>
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid var(--border); font-size: 0.75rem; margin-bottom: 0.75rem;">
                                            <span style="font-weight: 700; color: var(--emerald-text); background: var(--emerald-light); padding: 0.15rem 0.45rem; border-radius: 4px;">
                                                Stok: <?= (int)$book['available_stock'] ?>
                                            </span>
                                            <span style="color: var(--text-secondary); font-family: monospace;">
                                                ISBN: <?= htmlspecialchars($book['isbn'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>

                                        <div style="display: flex; gap: 0.45rem;">
                                            <a href="index.php?url=catalog/detail&id=<?= (int)$book['id'] ?>" class="btn btn-sm" style="flex: 1; background: var(--cream-subtle); color: var(--navy); border: 1px solid var(--border); font-weight: 700; text-align: center;">
                                                Detail
                                            </a>

                                            <?php if ($isCurrentlyBorrowed): ?>
                                                <button type="button" class="btn btn-sm" style="flex: 1; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); cursor: not-allowed; font-weight: 700;" disabled>
                                                    Dipinjam
                                                </button>
                                            <?php elseif (!$isStockAvailable): ?>
                                                <button type="button" class="btn btn-sm" style="flex: 1; background: var(--cream-subtle); color: var(--text-secondary); border: 1px solid var(--border); cursor: not-allowed;" disabled>
                                                    Habis
                                                </button>
                                            <?php elseif ($activeLoansCount >= 3): ?>
                                                <button type="button" class="btn btn-sm" style="flex: 1; background: var(--cream-subtle); color: var(--text-secondary); border: 1px solid var(--border); cursor: not-allowed;" title="Maksimal 3 pinjaman aktif" disabled>
                                                    Kuota Penuh
                                                </button>
                                            <?php else: ?>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm" 
                                                    style="flex: 1; background: var(--gold-accent); color: var(--navy); border: none; font-weight: 800; cursor: pointer;"
                                                    onclick="openBorrowModal(<?= (int)$book['id'] ?>, '<?= htmlspecialchars(addslashes($book['title']), ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars(addslashes($book['author']), ENT_QUOTES, 'UTF-8') ?>')"
                                                >
                                                    Pinjam
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

            </main>

            <!-- 2.3 FOOTER MODULAR -->
            <?php require_once __DIR__ . '/../layouts/user_footer.php'; ?>

        </div>
    </div>

</body>
</html>
