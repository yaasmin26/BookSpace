<?php
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
    <!-- CSS Terpisah -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud.css">
</head>
<body>

    <!-- Navigasi Atas (Header) -->
    <header class="navbar">
        <div class="navbar-container">
            <!-- Brand & Judul BookSpace -->
            <a href="index.php?url=dashboard" class="navbar-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <span class="brand-text">BookSpace</span>
                <span class="brand-badge">Perpustakaan</span>
            </a>

            <!-- Menu Navigasi Utama -->
            <ul class="navbar-nav">
                <li>
                    <a href="index.php?url=dashboard" class="nav-link">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="index.php?url=books" class="nav-link active">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                        Manajemen Buku
                    </a>
                </li>
                <li>
                    <a href="index.php?url=categories" class="nav-link">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                        Manajemen Kategori
                    </a>
                </li>
                <li>
                    <a href="index.php?url=members" class="nav-link">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        Manajemen Anggota
                    </a>
                </li>
                <li class="nav-item-dropdown">
                    <a href="index.php?url=loans" class="nav-link">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            <path d="M9 14l2 2 4-4"></path>
                        </svg>
                        Transaksi
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </a>
                    <div class="dropdown-menu">
                        <a href="index.php?url=loans/create" class="dropdown-item">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Peminjaman Buku
                        </a>
                        <a href="index.php?url=loans" class="dropdown-item">
                            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 11 12 14 22 4"></polyline>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                            Pengembalian Buku (Daftar Transaksi)
                        </a>
                    </div>
                </li>
            </ul>

            <!-- Profil Admin & Tombol Logout -->
            <div class="navbar-user">
                <div class="user-profile">
                    <div class="avatar-circle">
                        <?= strtoupper(substr($adminName, 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="user-role-badge">Admin Perpustakaan</span>
                    </div>
                </div>

                <form action="index.php?url=logout" method="POST" style="margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn-logout" title="Keluar dari akun">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="dashboard-main">

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
                    Kembali ke Daftar Buku
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
                                autofocus
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
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="dashboard-footer">
        &copy; <?= date('Y') ?> BookSpace – Library Management System. Hak Cipta Dilindungi.
    </footer>

    <!-- Shared Interactions JS -->
    <script src="js/app.js"></script>
</body>
</html>

