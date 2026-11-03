<?php
// Tentukan nilai input formulir (gunakan data input lama jika ada error validasi, atau data anggota saat ini)
$valCode    = $old['member_code'] ?? $member['member_code'] ?? '';
$valName    = $old['name'] ?? $member['name'] ?? '';
$valEmail   = $old['email'] ?? $member['email'] ?? '';
$valPhone   = $old['phone'] ?? $member['phone'] ?? '';
$valAddress = $old['address'] ?? $member['address'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota: <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?> - BookSpace</title>
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
                    <a href="index.php?url=books" class="nav-link">
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
                    <a href="index.php?url=members" class="nav-link active">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        Manajemen Anggota
                    </a>
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
            <a href="index.php?url=members">Manajemen Anggota</a>
            <span class="breadcrumb-separator">/</span>
            <span>Edit Anggota: <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <!-- Header Halaman -->
        <div class="page-header">
            <div class="page-title-box">
                <h1 class="page-title">Edit Data Anggota</h1>
                <p class="page-subtitle">Perbarui data identitas keanggotaan atau informasi kontak anggota perpustakaan.</p>
            </div>
            <div>
                <a href="index.php?url=members" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke Daftar Anggota
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

        <!-- Kartu Formulir Edit Anggota -->
        <div class="crud-card form-card">
            <div class="crud-card-body">
                <form action="index.php?url=members/edit" method="POST" autocomplete="off">
                    <!-- CSRF Token Keamanan & ID Anggota -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">

                    <div class="form-grid">

                        <!-- Kode Anggota -->
                        <div class="form-group">
                            <label for="member_code" class="form-label">
                                Kode Anggota <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="member_code" 
                                name="member_code" 
                                class="form-control <?= isset($errors['member_code']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($valCode, ENT_QUOTES, 'UTF-8') ?>" 
                                required
                            >
                            <span class="form-hint">Kode anggota unik (maks. 20 karakter).</span>
                            <?php if (isset($errors['member_code'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['member_code'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Nama Lengkap Anggota -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nama Lengkap <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($valName, ENT_QUOTES, 'UTF-8') ?>" 
                                required 
                                autofocus
                            >
                            <?php if (isset($errors['name'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Alamat Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($valEmail, ENT_QUOTES, 'UTF-8') ?>"
                            >
                            <span class="form-hint">Email harus unik jika diubah (opsional).</span>
                            <?php if (isset($errors['email'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($valPhone, ENT_QUOTES, 'UTF-8') ?>"
                            >
                            <span class="form-hint">Kontak telepon aktif anggota.</span>
                            <?php if (isset($errors['phone'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Alamat Domisili -->
                        <div class="form-group form-group-full">
                            <label for="address" class="form-label">Alamat Domisili</label>
                            <textarea 
                                id="address" 
                                name="address" 
                                rows="3" 
                                class="form-control" 
                            ><?= htmlspecialchars($valAddress, ENT_QUOTES, 'UTF-8') ?></textarea>
                            <span class="form-hint">Alamat tempat tinggal anggota perpustakaan.</span>
                        </div>

                    </div>

                    <!-- Tombol Aksi Formulir -->
                    <div class="form-actions">
                        <a href="index.php?url=members" class="btn btn-secondary">
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

</body>
</html>

