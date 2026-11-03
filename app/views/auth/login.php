<?php
// Pastikan CSRF token tersedia dari session
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookSpace - Login</title>
    <!-- CSS terpisah di public/css/ -->
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Header Judul BookSpace -->
            <div class="login-header">
                <h1 class="brand-title">BookSpace</h1>
                <p class="brand-subtitle">Library Management System</p>
            </div>

            <!-- Tempat untuk menampilkan pesan error -->
            <?php if (!empty($error)): ?>
                <div class="alert-error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- Form terhubung dengan route POST /login -->
            <form action="index.php?url=login" method="POST" autocomplete="off">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <!-- Input Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="contoh@bookspace.com" 
                        value="<?= htmlspecialchars($old_email ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                        required 
                        autofocus
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
                        placeholder="Masukkan password Anda" 
                        required
                    >
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="btn-submit">Login</button>
            </form>

            <div class="login-footer">
                &copy; <?= date('Y') ?> BookSpace. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
