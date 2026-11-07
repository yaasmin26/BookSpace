<?php
/**
 * ========================================================
 * BookSpace - Halaman Detail Buku Anggota
 * Desain Sama Persis dengan Tampilan Admin (MindRise Style)
 * ========================================================
 */
$activeMenu = 'catalog';
$isStockAvailable = (int)$book['available_stock'] > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?> - BookSpace</title>
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

            <!-- 2.2 KONTEN UTAMA DETAIL BUKU -->
            <main class="app-content">

                <!-- Breadcrumb Navigasi -->
                <div style="margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                    <a href="index.php?url=catalog" style="color: var(--navy); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                        &larr; Kembali ke Katalog
                    </a>
                    <span style="color: var(--border);">&bull;</span>
                    <span style="color: var(--text-secondary);"><?= htmlspecialchars($book['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?></span>
                    <span style="color: var(--border);">&bull;</span>
                    <span style="color: var(--navy); font-weight: 600;"><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <!-- Layout Dua Kolom: Cover Kiri & Detail Kanan -->
                <div style="display: grid; grid-template-columns: 320px 1fr; gap: 2rem; align-items: start;">

                    <!-- Kolom Kiri: Cover Buku Besar & Status Stok -->
                    <div class="content-card" style="padding: 1.5rem; text-align: center;">
                        <div style="width: 100%; height: 280px; background: linear-gradient(135deg, var(--navy), var(--navy-hover)); border-radius: var(--radius-sm); padding: 1.5rem; color: var(--white); display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; box-shadow: var(--shadow-lg); margin-bottom: 1.25rem;">
                            <span style="align-self: flex-start; font-size: 0.7rem; font-weight: 800; background: rgba(245, 158, 11, 0.3); color: var(--gold-accent); padding: 0.2rem 0.6rem; border-radius: 4px; text-transform: uppercase;">
                                <?= htmlspecialchars($book['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <div style="text-align: left;">
                                <h3 style="font-size: 1.25rem; font-weight: 800; line-height: 1.3; color: var(--white); text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                                    <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p style="font-size: 0.8125rem; color: #cbd5e1; margin-top: 0.35rem;">
                                    Penulis: <?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; background: var(--cream-subtle); border-radius: var(--radius-sm); border: 1px solid var(--border);">
                            <span style="font-size: 0.8125rem; color: var(--text-secondary); font-weight: 700;">Ketersediaan:</span>
                            <?php if ($isStockAvailable): ?>
                                <span style="display: inline-block; padding: 0.2rem 0.6rem; background: var(--emerald-light); color: var(--emerald-text); border: 1px solid var(--emerald-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 800;">
                                    Tersedia (<?= (int)$book['available_stock'] ?> Buku)
                                </span>
                            <?php else: ?>
                                <span style="display: inline-block; padding: 0.2rem 0.6rem; background: var(--rose-light); color: var(--rose-text); border: 1px solid var(--rose-border); border-radius: var(--radius-pill); font-size: 0.75rem; font-weight: 800;">
                                    Stok Habis
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Detail Informasi Lengkap & Aksi Peminjaman -->
                    <div class="content-card" style="padding: 2rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); padding: 0.2rem 0.6rem; border-radius: 4px; display: inline-block; margin-bottom: 0.75rem; text-transform: uppercase;">
                            <?= htmlspecialchars($book['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--navy); margin-bottom: 0.35rem; line-height: 1.25; letter-spacing: -0.5px;">
                            <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h1>

                        <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 1.75rem;">
                            Karya: <strong><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </p>

                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--navy); border-bottom: 1px solid var(--border); padding-bottom: 0.65rem; margin-bottom: 1.25rem;">
                            Informasi Bibliografi Koleksi
                        </h4>

                        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; margin-bottom: 1.75rem;">
                            <tbody>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="width: 200px; padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Kategori Buku</th>
                                    <td style="padding: 0.65rem 0; color: var(--navy); font-weight: 700;"><?= htmlspecialchars($book['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Penulis</th>
                                    <td style="padding: 0.65rem 0; color: var(--navy); font-weight: 700;"><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Penerbit</th>
                                    <td style="padding: 0.65rem 0; color: var(--navy); font-weight: 700;"><?= htmlspecialchars(!empty($book['publisher']) ? $book['publisher'] : 'Perpustakaan BookSpace', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Tahun Terbit</th>
                                    <td style="padding: 0.65rem 0; color: var(--navy); font-weight: 700;"><?= htmlspecialchars(!empty($book['publication_year']) ? $book['publication_year'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Nomor ISBN</th>
                                    <td style="padding: 0.65rem 0; color: var(--navy); font-weight: 700; font-family: monospace;"><?= htmlspecialchars(!empty($book['isbn']) ? $book['isbn'] : 'Belum tercatat', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <th style="padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Total Koleksi Fisik</th>
                                    <td style="padding: 0.65rem 0; color: var(--navy); font-weight: 700;"><?= (int)$book['total_stock'] ?> Eksemplar</td>
                                </tr>
                                <tr>
                                    <th style="padding: 0.65rem 0; color: var(--text-secondary); font-weight: 600; text-align: left;">Stok Tersedia di Rak</th>
                                    <td style="padding: 0.65rem 0; color: var(--emerald-text); font-weight: 800; font-size: 0.95rem;"><?= (int)$book['available_stock'] ?> Eksemplar</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Panel Peminjaman -->
                        <div style="background: var(--cream-subtle); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.35rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--navy);">Ingin meminjam buku ini?</h4>
                                <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.2rem;">
                                    Durasi peminjaman berlaku selama <strong>14 hari kerja</strong> dengan batas kuota maksimal 3 buku aktif.
                                </p>
                            </div>

                            <div>
                                <?php if ($isCurrentlyBorrowed): ?>
                                    <button type="button" class="btn" style="background: var(--gold-light); color: var(--gold-text); border: 1px solid var(--gold-border); padding: 0.7rem 1.5rem; font-weight: 700; cursor: not-allowed;" disabled>
                                        Buku Sedang Anda Pinjam
                                    </button>
                                <?php elseif (!$isStockAvailable): ?>
                                    <button type="button" class="btn" style="background: var(--cream-subtle); color: var(--text-secondary); border: 1px solid var(--border); padding: 0.7rem 1.5rem; font-weight: 700; cursor: not-allowed;" disabled>
                                        Stok Buku Sedang Habis
                                    </button>
                                <?php elseif ($activeLoansCount >= 3): ?>
                                    <button type="button" class="btn" style="background: var(--cream-subtle); color: var(--text-secondary); border: 1px solid var(--border); padding: 0.7rem 1.5rem; font-weight: 700; cursor: not-allowed;" title="Maksimal 3 pinjaman aktif" disabled>
                                        Batas Kuota Pinjam Tercapai
                                    </button>
                                <?php else: ?>
                                    <button 
                                        type="button" 
                                        class="btn btn-primary"
                                        style="background: var(--gold-accent); color: var(--navy); border: none; padding: 0.75rem 1.75rem; font-size: 0.925rem; font-weight: 800; cursor: pointer; box-shadow: var(--shadow-gold);"
                                        data-id="<?= (int)$book['id'] ?>"
                                        data-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-author="<?= htmlspecialchars($book['author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                                        onclick="openBorrowModal(this.dataset.id, this.dataset.title, this.dataset.author)"
                                    >
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                        </svg>
                                        <span>Pinjam Buku Ini</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                </div>

            </main>

            <!-- 2.3 FOOTER MODULAR -->
            <?php require_once __DIR__ . '/../layouts/user_footer.php'; ?>

        </div>
    </div>

</body>
</html>
