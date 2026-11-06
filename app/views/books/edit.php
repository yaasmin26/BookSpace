<?php
/**
 * ========================================================
 * BookSpace - Edit Data Buku
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'books';

$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken = $_SESSION['csrf_token'] ?? '';

// Tentukan nilai input formulir (gunakan data input lama jika ada error validasi, atau data buku saat ini)
$valTitle        = $old['title'] ?? $book['title'] ?? '';
$valCategoryId   = $old['category_id'] ?? $book['category_id'] ?? 0;
$valAuthor       = $old['author'] ?? $book['author'] ?? '';
$valPublisher    = $old['publisher'] ?? $book['publisher'] ?? '';
$valYear         = $old['publication_year'] ?? $book['publication_year'] ?? '';
$valIsbn         = $old['isbn'] ?? $book['isbn'] ?? '';
$valTotalStock   = $old['total_stock'] ?? $book['total_stock'] ?? 0;
$valAvailStock   = $old['available_stock'] ?? $book['available_stock'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?> - BookSpace</title>
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

            <!-- 2.2 KONTEN FORM EDIT BUKU -->
            <main class="app-content">

                <!-- Breadcrumb Navigasi -->
                <nav class="breadcrumb">
                    <a href="index.php?url=dashboard">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="index.php?url=books">Katalog Buku</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Edit Buku: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </nav>

                <!-- Header Halaman -->
                <div class="page-header">
                    <div class="page-title-box">
                        <h1 class="page-title">Edit Data Buku</h1>
                        <p class="page-subtitle">Perbarui informasi bibliografi, kategori, atau kuantitas stok buku.</p>
                    </div>
                    <div>
                        <a href="index.php?url=books" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            <span>Kembali ke Daftar Buku</span>
                        </a>
                    </div>
                </div>

                <!-- Alert Error Validasi (jika ada) -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <svg class="alert-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="alert-content">
                            <strong>Terdapat kesalahan pada input Anda:</strong>
                            <ul class="alert-list">
                                <?php foreach ($errors as $fieldError): ?>
                                    <li><?= htmlspecialchars($fieldError, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Kartu Formulir Edit Buku -->
                <div class="crud-card form-card">
                    <div class="crud-card-body">
                        <form action="index.php?url=books/edit" method="POST" autocomplete="off">
                            <!-- CSRF Token Keamanan & ID Buku -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="id" value="<?= (int) $book['id'] ?>">

                            <div class="form-grid">

                                <!-- Judul Buku -->
                                <div class="form-group form-group-full">
                                    <label for="title" class="form-label">
                                        Judul Buku <span class="required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($valTitle, ENT_QUOTES, 'UTF-8') ?>"
                                        required
                                        autofocu
                                    >
                                    <?php if (isset($errors['title'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Kategori Buku -->
                                <div class="form-group">
                                    <label for="category_id" class="form-label">
                                        Kategori Buku <span class="required">*</span>
                                    </label>
                                    <select
                                        id="category_id"
                                        name="category_id"
                                        class="form-control <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option
                                                value="<?= $cat['id'] ?>"
                                                <?= ((int)$valCategoryId === (int)$cat['id']) ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['category_id'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Pengarang / Penulis -->
                                <div class="form-group">
                                    <label for="author" class="form-label">
                                        Pengarang / Penulis <span class="required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="author"
                                        name="author"
                                        class="form-control <?= isset($errors['author']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($valAuthor, ENT_QUOTES, 'UTF-8') ?>"
                                        required
                                    >
                                    <?php if (isset($errors['author'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['author'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Penerbit -->
                                <div class="form-group">
                                    <label for="publisher" class="form-label">Penerbit</label>
                                    <input
                                        type="text"
                                        id="publisher"
                                        name="publisher"
                                        class="form-control <?= isset($errors['publisher']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($valPublisher, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                    <?php if (isset($errors['publisher'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['publisher'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Tahun Terbit -->
                                <div class="form-group">
                                    <label for="publication_year" class="form-label">Tahun Terbit</label>
                                    <input
                                        type="number"
                                        id="publication_year"
                                        name="publication_year"
                                        min="1000"
                                        max="<?= date('Y') + 1 ?>"
                                        class="form-control <?= isset($errors['publication_year']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars((string) $valYear, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                    <?php if (isset($errors['publication_year'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['publication_year'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Nomor ISBN -->
                                <div class="form-group form-group-full">
                                    <label for="isbn" class="form-label">Nomor ISBN</label>
                                    <input
                                        type="text"
                                        id="isbn"
                                        name="isbn"
                                        class="form-control <?= isset($errors['isbn']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($valIsbn, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                    <span class="form-hint">Nomor ISBN harus unik pada setiap buku.</span>
                                    <?php if (isset($errors['isbn'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['isbn'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Total Stok Buku -->
                                <div class="form-group">
                                    <label for="total_stock" class="form-label">
                                        Total Stok Buku <span class="required">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="total_stock"
                                        name="total_stock"
                                        min="0"
                                        class="form-control <?= isset($errors['total_stock']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars((string) $valTotalStock, ENT_QUOTES, 'UTF-8') ?>"
                                        required
                                    >
                                    <span class="form-hint">Total keseluruhan eksemplar buku di perpustakaan.</span>
                                    <?php if (isset($errors['total_stock'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['total_stock'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Stok Tersedia -->
                                <div class="form-group">
                                    <label for="available_stock" class="form-label">Stok Tersedia</label>
                                    <input
                                        type="number"
                                        id="available_stock"
                                        name="available_stock"
                                        min="0"
                                        class="form-control <?= isset($errors['available_stock']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars((string) $valAvailStock, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                    <span class="form-hint">Jumlah buku yang siap dipinjam (tidak boleh melebihi total stok).</span>
                                    <?php if (isset($errors['available_stock'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['available_stock'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <!-- Tombol Aksi Formulir -->
                            <div class="form-actions">
                                <a href="index.php?url=books" class="btn btn-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>

                        </form>
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
