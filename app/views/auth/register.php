<?php
// Pastikan CSRF token tersedia dari session
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSpace - Sign Up</title>
    <!-- CSS terpisah di public/css/ -->
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Header Judul BookSpace -->
            <div class="login-header">
                <h1 class="brand-title">BookSpace</h1>
                <p class="brand-subtitle">Pendaftaran Akun Anggota Baru</p>
            </div>

            <!-- Tempat untuk menampilkan pesan error -->
            <?php if (!empty($error)): ?>
                <div class="alert-error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- Form Pendaftaran terhubung dengan route POST /register -->
            <form action="index.php?url=register" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <!-- Input Nama Lengkap -->
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        placeholder="Masukkan nama lengkap Anda" 
                        value="<?= htmlspecialchars($old_input['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                        required 
                        autofocus
                    >
                </div>

                <!-- Input Username -->
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="Pilih username unik (contoh: budi_santoso)" 
                        value="<?= htmlspecialchars($old_input['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                        required
                    >
                </div>

                <!-- Input Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="contoh@domain.com" 
                        value="<?= htmlspecialchars($old_input['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                        required
                    >
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Minimal 6 karakter" 
                        required
                    >
                </div>

                <!-- Input Konfirmasi Password -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-control" 
                        placeholder="Ulangi password Anda" 
                        required
                    >
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </form>

            <!-- Tautan ke Halaman Login -->
            <div class="auth-switch">
                Sudah punya akun? <a href="index.php?url=login">Login</a>
            </div>

            <div class="login-footer">
                &copy; <?= date('Y') ?> BookSpace. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
