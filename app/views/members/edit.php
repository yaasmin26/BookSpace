<?php
/**
 * ========================================================
 * BookSpace - Edit Data Anggota Perpustakaan
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'members';

$adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken = $_SESSION['csrf_token'] ?? '';

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

            <!-- 2.3 FOOTER -->
            <footer class="app-footer">
                &copy; <?= date('Y') ?> BookSpace – Modern Library Management System. Seluruh Hak Cipta Dilindungi.
            </footer>

        </div>

    </div>

</body>
</html>
