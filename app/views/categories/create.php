<?php
/**
 * ========================================================
 * BookSpace - Tambah Kategori Baru
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
    <title>Tambah Kategori Baru - BookSpace</title>
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

            <!-- 2.2 KONTEN UTAMA -->
            <main class="app-content">

                <!-- Breadcrumb Navigasi -->
                <nav class="breadcrumb">
                    <a href="index.php?url=dashboard">Dashboard</a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="index.php?url=categories">Manajemen Kategori</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Tambah Kategori Baru</span>
                </nav>

                <!-- Header Halaman -->
                <div class="page-header">
                    <div class="page-title-box">
                        <h1 class="page-title">Tambah Kategori Baru</h1>
                        <p class="page-subtitle">Daftarkan nama kategori buku baru untuk memperluas klasifikasi katalog perpustakaan.</p>
                    </div>
                    <div>
                        <a href="index.php?url=categories" class="btn btn-secondary">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Kembali ke Daftar Kategori
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
                            <strong>Mohon periksa kembali formulir Anda:</strong>
                            <ul class="alert-list">
                                <?php foreach ($errors as $fieldError): ?>
                                    <li><?= htmlspecialchars($fieldError, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Kartu Formulir Tambah Kategori -->
                <div class="crud-card form-card">
                    <div class="crud-card-body">
                        <form action="index.php?url=categories/create" method="POST" autocomplete="off">
                            <!-- CSRF Token Keamanan -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                            <div class="form-grid">

                                <!-- Nama Kategori -->
                                <div class="form-group form-group-full">
                                    <label for="category_name" class="form-label">
                                        Nama Kategori <span class="required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="category_name"
                                        name="category_name"
                                        class="form-control <?= isset($errors['category_name']) ? 'is-invalid' : '' ?>"
                                        placeholder="Contoh: Pemrograman & Komputer, Fiksi Ilmiah, Sejarah Nasional"
                                        value="<?= htmlspecialchars($old['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        required
                                        autofocus
                                    >
                                    <span class="form-hint">Nama kategori harus unik dan belum pernah digunakan sebelumnya.</span>
                                    <?php if (isset($errors['category_name'])): ?>
                                        <span class="invalid-feedback"><?= htmlspecialchars($errors['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Deskripsi Kategori -->
                                <div class="form-group form-group-full">
                                    <label for="description" class="form-label">Deskripsi Kategori</label>
                                    <textarea
                                        id="description"
                                        name="description"
                                        rows="4"
                                        class="form-control"
                                        placeholder="Tuliskan gambaran ringkas mengenai ruang lingkup koleksi buku pada kategori ini (opsional)..."
                                    ><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                    <span class="form-hint">Penjelasan opsional untuk mempermudah identifikasi tema kategori.</span>
                                </div>

                            </div>

                            <!-- Tombol Aksi Formulir -->
                            <div class="form-actions">
                                <a href="index.php?url=categories" class="btn btn-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Simpan Kategori Baru
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
