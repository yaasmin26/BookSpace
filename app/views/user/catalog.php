<?php
/**
 * ========================================================
 * BookSpace - Katalog Buku Perpustakaan Anggota
 * Desain Sama Persis dengan Tampilan Admin (MindRise Style)
 * ========================================================
 */
$activeMenu = 'catalog';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - BookSpace Library</title>
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

            <!-- 2.2 KONTEN UTAMA KATALOG -->
            <main class="app-content">

                <!-- Header Halaman (MindRise Style) -->
                <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
                    <div class="page-title-box">
                        <h2 class="page-title" style="font-size: 1.65rem; font-weight: 800; color: var(--navy); letter-spacing: -0.5px;">
                            📚 Katalog Koleksi Buku
                        </h2>
                        <p class="page-subtitle" style="font-size: 0.875rem; color: var(--text-secondary);">
                            Telusuri <?= count($books) ?> judul buku perpustakaan yang dapat dipinjam secara mandiri
                        </p>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="index.php?url=my-loans" class="btn btn-outline" style="background: var(--white); border: 1px solid var(--border); color: var(--navy); font-weight: 700;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                                <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                            </svg>
                            <span>Peminjaman Saya (<?= (int)$activeLoansCount ?>/3)</span>
                        </a>
                    </div>
                </div>

                <!-- Toolbar Pencarian & Filter Kategori (MindRise Card) -->
                <section class="content-card" style="margin-bottom: 2rem; padding: 1.25rem 1.5rem;">
                    <form action="index.php" method="GET" style="display: flex; gap: 0.85rem; align-items: center; flex-wrap: wrap;">
                        <input type="hidden" name="url" value="catalog">

                        <!-- Input Pencarian -->
                        <div style="flex: 1; min-width: 260px; position: relative; display: flex; align-items: center;">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 1rem; color: var(--text-secondary); pointer-events: none;">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="Cari judul buku, nama penulis, atau nomor ISBN..." 
                                value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                style="width: 100%; padding: 0.65rem 1rem 0.65rem 2.65rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.875rem; background: var(--cream-subtle); color: var(--navy);"
                            >
                        </div>

                        <!-- Dropdown Kategori -->
                        <select name="category" style="padding: 0.65rem 1rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.875rem; background: var(--cream-subtle); color: var(--navy); font-weight: 600; min-width: 170px; cursor: pointer;">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option 
                                    value="<?= (int)$cat['id'] ?>" 
                                    <?= ($categoryId === (int)$cat['id']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Tombol Cari -->
                        <button type="submit" class="btn btn-primary" style="background: var(--navy); color: var(--white); font-weight: 700; padding: 0.65rem 1.25rem;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span>Cari</span>
                        </button>

                        <?php if (!empty($keyword) || !empty($categoryId)): ?>
                            <a href="index.php?url=catalog" class="btn" style="background: var(--cream-subtle); color: var(--text-secondary); border: 1px solid var(--border); padding: 0.65rem 1rem;">
                                &times; Reset
                            </a>
                        <?php endif; ?>
                    </form>
                </section>

                <!-- Grid Koleksi Buku -->
                <?php if (empty($books)): ?>
                    <div class="content-card">
                        <div class="empty-state" style="padding: 3.5rem 1.5rem; text-align: center;">
                            <div class="empty-icon" style="width: 54px; height: 54px; margin: 0 auto 0.85rem; background: var(--cream-subtle); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                                <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--navy); margin-bottom: 0.35rem;">Buku Tidak Ditemukan</h4>
                            <p style="font-size: 0.875rem; color: var(--text-secondary); max-width: 420px; margin: 0 auto 1.25rem;">
                                Tidak ada koleksi buku yang cocok dengan kata kunci atau kategori yang Anda pilih.
                            </p>
                            <a href="index.php?url=catalog" class="btn btn-primary" style="background: var(--navy); font-weight: 700;">
                                Tampilkan Semua Buku
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 1.35rem;">
                        <?php foreach ($books as $book): 
                            $isCurrentlyBorrowed = in_array((int)$book['id'], $activeLoanBookIds, true);
                            $isStockAvailable   = (int)$book['available_stock'] > 0;
                        ?>
                            <article class="content-card" style="display: flex; flex-direction: column; justify-content: space-between; padding: 1.25rem; transition: transform 0.15s ease, box-shadow 0.15s ease;">
                                <div>
                                    <!-- Cover Ilustrasi Buku MindRise -->
                                    <div style="width: 100%; height: 140px; background: linear-gradient(135deg, var(--navy), var(--navy-hover)); border-radius: var(--radius-sm); padding: 0.95rem; color: var(--white); margin-bottom: 0.85rem; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.15);">
                                        <span style="font-size: 0.65rem; font-weight: 800; background: rgba(245, 158, 11, 0.25); color: var(--gold-accent); padding: 0.15rem 0.5rem; border-radius: 4px; width: fit-content; text-transform: uppercase;">
                                            <?= htmlspecialchars($book['category_name'] ?? 'Umum', ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <div style="font-size: 1rem; font-weight: 800; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-shadow: 0 1px 2px rgba(0,0,0,0.4);">
                                            <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>

                                    <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--navy); margin-bottom: 0.35rem; line-height: 1.3;">
                                        <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </h4>
                                    <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 0.85rem;">
                                        Penulis: <strong><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <?php if (!empty($book['publication_year'])): ?>
                                            &bull; Tahun: <?= htmlspecialchars($book['publication_year'], ENT_QUOTES, 'UTF-8') ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.75rem; border-top: 1px solid var(--border); font-size: 0.75rem; margin-bottom: 0.85rem;">
                                        <span style="font-weight: 700; color: <?= $isStockAvailable ? 'var(--emerald-text)' : 'var(--rose-text)' ?>; background: <?= $isStockAvailable ? 'var(--emerald-light)' : 'var(--rose-light)' ?>; padding: 0.15rem 0.5rem; border-radius: 4px; border: 1px solid <?= $isStockAvailable ? 'var(--emerald-border)' : 'var(--rose-border)' ?>;">
                                            <?= $isStockAvailable ? 'Stok: ' . (int)$book['available_stock'] : 'Stok Habis' ?>
                                        </span>
                                        <?php if (!empty($book['isbn'])): ?>
                                            <span style="color: var(--text-secondary); font-family: monospace;">
                                                ISBN: <?= htmlspecialchars($book['isbn'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
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
                                                data-id="<?= (int)$book['id'] ?>"
                                                data-title="<?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-author="<?= htmlspecialchars($book['author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                                                onclick="openBorrowModal(this.dataset.id, this.dataset.title, this.dataset.author)"
                                            >
                                                Pinjam
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </main>

            <!-- 2.3 FOOTER MODULAR -->
            <?php require_once __DIR__ . '/../layouts/user_footer.php'; ?>

        </div>
    </div>

</body>
</html>
