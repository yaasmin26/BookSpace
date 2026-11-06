<?php
/**
 * ========================================================
 * BookSpace - Tambah Peminjaman Buku Baru
 * Desain Modern, Elegan & Minimalis (MindRise Style)
 * Integrasi: layouts/sidebar.php & layouts/header.php
 * ========================================================
 */

// Tentukan menu aktif untuk penanda navigasi sidebar
$activeMenu = 'loans';

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
    <!-- Lembar Gaya Utama MindRise Design System -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/crud.css">
    <link rel="stylesheet" href="css/loans.css">
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

            <!-- 2.2 KONTEN UTAMA HALAMAN FORM PEMINJAMAN -->
            <main class="app-content">

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
                                        <span class="form-hint" style="color: var(--gold-text, #92400e); margin-top: 0.35rem; display: block; font-weight: 600;">
                                            Belum ada data anggota. Silakan <a href="index.php?url=members/create" style="color: var(--gold-accent, #f59e0b); text-decoration: underline;">tambah anggota baru</a> terlebih dahulu.
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
                                                data-stock="<?= (int) ($b['available_stock'] ?? $b['stock'] ?? 0) ?>"
                                                data-isbn="<?= htmlspecialchars($b['isbn'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                                                <?= (isset($old['book_id']) && (int)$old['book_id'] === (int)$b['id']) ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($b['title'], ENT_QUOTES, 'UTF-8') ?> (Stok Siap: <?= (int) ($b['available_stock'] ?? $b['stock'] ?? 0) ?> | Penulis: <?= htmlspecialchars($b['author'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="form-hint">Hanya buku dengan stok fisik tersedia (available_stock &gt; 0) yang dapat dipinjam.</span>

                                    <?php if (empty($availableBooks)): ?>
                                        <span class="form-hint" style="color: var(--rose, #ef4444); margin-top: 0.35rem; display: block; font-weight: 600;">
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

            <!-- 2.3 FOOTER -->
            <footer class="app-footer">
                &copy; <?= date('Y') ?> BookSpace – Modern Library Management System. Seluruh Hak Cipta Dilindungi.
            </footer>

        </div>

    </div>

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
            if (bookSelect && bookSelect.value) {
                handleBookChange(bookSelect);
            }
        });

        // Validasi form sebelum dikirim
        const formLoan = document.getElementById('formLoan');
        if (formLoan) {
            formLoan.addEventListener('submit', function(e) {
                const loanDate = document.getElementById('loan_date').value;
                const dueDate  = document.getElementById('due_date').value;

                if (dueDate < loanDate) {
                    e.preventDefault();
                    alert('Tanggal jatuh tempo tidak boleh lebih awal dari tanggal peminjaman.');
                    document.getElementById('due_date').focus();
                }
            });
        }
    </script>

</body>
</html>
