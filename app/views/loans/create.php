<?php
/**
 * BookSpace - Library Management System
 * View: Form Tambah Transaksi Peminjaman Buku (app/views/loans/create.php)
 * Tanggung Jawab: Anggota 3
 */

$adminName = $adminName ?? $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
$csrfToken = $csrfToken ?? $_SESSION['csrf_token'] ?? '';
$defaultLoanDate = $defaultLoanDate ?? date('Y-m-d');
$defaultDueDate  = $defaultDueDate ?? date('Y-m-d', strtotime('+7 days'));
$errors = $errors ?? [];
$old = $old ?? [];
$members = $members ?? [];
$availableBooks = $availableBooks ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman Baru - BookSpace</title>
    <!-- CSS Terpisah -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud.css">
    <style>
        .book-preview-card {
            background: var(--slate-50);
            border: 1px dashed var(--slate-300);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            margin-top: 0.5rem;
            display: none;
            font-size: 0.85rem;
            color: var(--slate-700);
        }

        .book-preview-card.active {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .book-preview-title {
            font-weight: 700;
            color: var(--slate-900);
            font-size: 0.95rem;
        }

        .book-preview-meta {
            color: var(--slate-500);
            font-size: 0.8rem;
        }
    </style>
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
                    <!-- Menu Transaksi (Tanggung Jawab Anggota 3) -->
                    <a href="index.php?url=loans" class="nav-link active">
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
            <a href="index.php?url=loans">Sirkulasi Peminjaman</a>
            <span class="breadcrumb-separator">/</span>
            <span>Tambah Peminjaman Baru</span>
        </nav>

        <!-- Header Halaman -->
        <div class="page-header">
            <div class="page-title-box">
                <h1 class="page-title">Tambah Peminjaman Buku Baru</h1>
                <p class="page-subtitle">Pilih anggota peminjam, buku yang tersedia, dan tentukan batas tanggal jatuh tempo pengembalian.</p>
            </div>
            <div>
                <a href="index.php?url=loans" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke Daftar Transaksi
                </a>
            </div>
        </div>

        <!-- Alert Error Validasi Global (jika ada) -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <svg class="alert-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="alert-content">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <button type="button" class="alert-close" aria-label="Tutup Notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <svg class="alert-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="alert-content">
                    <strong>Mohon periksa kembali formulir peminjaman Anda:</strong>
                    <ul class="alert-list">
                        <?php foreach ($errors as $fieldError): ?>
                            <li><?= htmlspecialchars($fieldError, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="alert-close" aria-label="Tutup Notifikasi">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Kartu Formulir Peminjaman -->
        <div class="crud-card form-card">
            <div class="crud-card-body">
                <!-- Terhubung langsung dengan LoanController::store() via POST /loans/create -->
                <form action="index.php?url=loans/create" method="POST" autocomplete="off" id="formLoan">
                    <!-- CSRF Token Keamanan -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="form-grid">

                        <!-- 1. Pilihan Anggota Perpustakaan -->
                        <div class="form-group form-group-full">
                            <label for="member_id" class="form-label">
                                Peminjam (Anggota Perpustakaan) <span class="required">*</span>
                            </label>
                            <select 
                                id="member_id" 
                                name="member_id" 
                                class="form-control <?= isset($errors['member_id']) ? 'is-invalid' : '' ?>" 
                                required
                                autofocus
                            >
                                <option value="">-- Pilih Anggota Peminjam --</option>
                                <?php foreach ($members as $m): ?>
                                    <option 
                                        value="<?= (int) $m['id'] ?>"
                                        <?= (isset($old['member_id']) && (int)$old['member_id'] === (int)$m['id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($m['member_code'], ENT_QUOTES, 'UTF-8') ?>) <?= !empty($m['phone']) ? '– ' . htmlspecialchars($m['phone'], ENT_QUOTES, 'UTF-8') : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="form-hint">Pilih anggota terdaftar yang meminjam buku.</span>

                            <?php if (empty($members)): ?>
                                <span class="form-hint" style="color: var(--amber); margin-top: 0.25rem; display: block;">
                                    Belum ada data anggota. Silakan <a href="index.php?url=members/create" style="color: var(--primary); font-weight: 600;">tambah anggota baru</a> terlebih dahulu.
                                </span>
                            <?php endif; ?>

                            <?php if (isset($errors['member_id'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['member_id'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- 2. Pilihan Buku (Hanya yang stoknya tersedia) -->
                        <div class="form-group form-group-full">
                            <label for="book_id" class="form-label">
                                Buku yang Dipinjam <span class="required">*</span>
                            </label>
                            <select 
                                id="book_id" 
                                name="book_id" 
                                class="form-control <?= isset($errors['book_id']) ? 'is-invalid' : '' ?>" 
                                required 
                                onchange="handleBookChange(this)"
                            >
                                <option value="">-- Pilih Buku yang Tersedia --</option>
                                <?php foreach ($availableBooks as $b): ?>
                                    <option 
                                        value="<?= (int) $b['id'] ?>"
                                        data-title="<?= htmlspecialchars($b['title'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-author="<?= htmlspecialchars($b['author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                                        data-stock="<?= (int) $b['available_stock'] ?>"
                                        data-isbn="<?= htmlspecialchars($b['isbn'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                                        <?= (isset($old['book_id']) && (int)$old['book_id'] === (int)$b['id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($b['title'], ENT_QUOTES, 'UTF-8') ?> (Stok Siap: <?= (int) $b['available_stock'] ?> | Pengarang: <?= htmlspecialchars($b['author'], ENT_QUOTES, 'UTF-8') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="form-hint">Hanya buku dengan stok fisik tersedia (available_stock &gt; 0) yang dapat dipinjam.</span>

                            <?php if (empty($availableBooks)): ?>
                                <span class="form-hint" style="color: var(--rose); margin-top: 0.25rem; display: block; font-weight: 600;">
                                    Tidak ada buku dengan stok tersedia saat ini. Silakan tambahkan buku atau tunggu pengembalian buku.
                                </span>
                            <?php endif; ?>

                            <!-- Preview Kartu Buku Pilihan -->
                            <div id="bookPreview" class="book-preview-card">
                                <div>
                                    <div class="book-preview-title" id="previewTitle">-</div>
                                    <div class="book-preview-meta" id="previewMeta">-</div>
                                </div>
                                <span class="badge badge-success" id="previewStock">Stok Tersedia</span>
                            </div>

                            <?php if (isset($errors['book_id'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['book_id'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- 3. Tanggal Peminjaman -->
                        <div class="form-group">
                            <label for="loan_date" class="form-label">
                                Tanggal Peminjaman <span class="required">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="loan_date" 
                                name="loan_date" 
                                class="form-control <?= isset($errors['loan_date']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($old['loan_date'] ?? $defaultLoanDate, ENT_QUOTES, 'UTF-8') ?>" 
                                required
                                onchange="handleLoanDateChange(this.value)"
                            >
                            <span class="form-hint">Tanggal resmi buku mulai dipinjam (default: hari ini).</span>
                            <?php if (isset($errors['loan_date'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['loan_date'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- 4. Tanggal Jatuh Tempo -->
                        <div class="form-group">
                            <label for="due_date" class="form-label">
                                Tanggal Jatuh Tempo <span class="required">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="due_date" 
                                name="due_date" 
                                class="form-control <?= isset($errors['due_date']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($old['due_date'] ?? $defaultDueDate, ENT_QUOTES, 'UTF-8') ?>" 
                                min="<?= htmlspecialchars($old['loan_date'] ?? $defaultLoanDate, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                            <span class="form-hint">Batas akhir pengembalian (tidak boleh mendahului tanggal pinjam).</span>
                            <?php if (isset($errors['due_date'])): ?>
                                <span class="invalid-feedback"><?= htmlspecialchars($errors['due_date'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- 5. Tombol Aksi Formulir (Simpan & Batal) -->
                    <div class="form-actions" style="margin-top: 2rem;">
                        <!-- Tombol Batal -->
                        <a href="index.php?url=loans" class="btn btn-secondary">
                            Batal
                        </a>

                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn btn-primary" id="btnSubmitLoan">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            Simpan Transaksi Peminjaman
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

    <!-- Validasi Interaktif Tanggal & Preview Buku di Sisi Klien -->
    <script>
        // Validasi interaktif: pastikan due_date >= loan_date
        function handleLoanDateChange(loanDateVal) {
            const dueDateInput = document.getElementById('due_date');
            if (loanDateVal) {
                dueDateInput.min = loanDateVal;
                if (dueDateInput.value && dueDateInput.value < loanDateVal) {
                    // Otomatis set due_date 7 hari setelah tanggal pinjam baru
                    const dateObj = new Date(loanDateVal);
                    dateObj.setDate(dateObj.getDate() + 7);
                    dueDateInput.value = dateObj.toISOString().split('T')[0];
                }
            }
        }

        // Tampilkan preview info buku saat dipilih
        function handleBookChange(selectElem) {
            const preview = document.getElementById('bookPreview');
            const selectedOption = selectElem.options[selectElem.selectedIndex];

            if (selectElem.value && selectedOption.dataset.title) {
                document.getElementById('previewTitle').innerText = selectedOption.dataset.title;
                document.getElementById('previewMeta').innerText = 'Penulis: ' + selectedOption.dataset.author + ' | ISBN: ' + selectedOption.dataset.isbn;
                document.getElementById('previewStock').innerText = 'Stok Tersedia: ' + selectedOption.dataset.stock + ' eksemplar';
                preview.classList.add('active');
            } else {
                preview.classList.remove('active');
            }
        }

        // Inisialisasi preview jika sudah ada buku terpilih dari input lama
        document.addEventListener('DOMContentLoaded', function() {
            const bookSelect = document.getElementById('book_id');
            if (bookSelect.value) {
                handleBookChange(bookSelect);
            }
        });

        // Validasi form sebelum dikirim
        document.getElementById('formLoan').addEventListener('submit', function(e) {
            const loanDate = document.getElementById('loan_date').value;
            const dueDate  = document.getElementById('due_date').value;

            if (dueDate < loanDate) {
                e.preventDefault();
                alert('Tanggal jatuh tempo tidak boleh lebih awal dari tanggal peminjaman.');
                document.getElementById('due_date').focus();
            }
        });
    </script>

    <!-- Shared Interactions JS -->
    <script src="js/app.js"></script>
</body>
</html>

