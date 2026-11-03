<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota - BookSpace</title>
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

    <!-- Konten Utama Halaman Anggota -->
    <main class="dashboard-main">

        <!-- Flash Message Notifikasi -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <svg class="alert-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div class="alert-content">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <button type="button" class="alert-close" aria-label="Tutup Notifikasi">&times;</button>
            </div>
        <?php endif; ?>

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

        <!-- Header Halaman -->
        <div class="page-header">
            <div class="page-title-box">
                <h1 class="page-title">Manajemen Anggota Perpustakaan</h1>
                <p class="page-subtitle">Kelola data keanggotaan peminjam buku, informasi kontak, dan status peminjaman aktif.</p>
            </div>
            <div>
                <a href="index.php?url=members/create" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Anggota Baru
                </a>
            </div>
        </div>

        <!-- Kartu Tabel & Filter -->
        <div class="crud-card">
            <!-- Header Filter & Pencarian -->
            <div class="crud-card-header">
                <form action="index.php" method="GET" class="filter-bar">
                    <input type="hidden" name="url" value="members">

                    <!-- Kotak Pencarian -->
                    <div class="search-box">
                        <svg class="search-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input 
                            type="text" 
                            name="q" 
                            class="search-input" 
                            placeholder="Cari nama, kode anggota, email, atau no. telepon..." 
                            value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>

                    <!-- Tombol Cari -->
                    <button type="submit" class="btn btn-secondary">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Cari
                    </button>

                    <!-- Tombol Reset jika filter aktif -->
                    <?php if (!empty($keyword)): ?>
                        <a href="index.php?url=members" class="btn btn-secondary" title="Reset Pencarian">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabel Data Anggota -->
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 140px;">Kode Anggota</th>
                            <th>Nama Lengkap</th>
                            <th>Kontak (Email / Telp)</th>
                            <th>Alamat Domisili</th>
                            <th>Status Peminjaman</th>
                            <th>Terdaftar</th>
                            <th style="width: 140px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="9" cy="7" r="4"></circle>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                            </svg>
                                        </div>
                                        <h3 class="empty-title">Tidak Ada Anggota Ditemukan</h3>
                                        <p class="empty-desc">
                                            <?php if (!empty($keyword)): ?>
                                                Tidak ada data anggota yang cocok dengan kata kunci pencarian Anda.
                                            <?php else: ?>
                                                Belum ada anggota perpustakaan yang terdaftar di sistem. Mulai tambahkan anggota baru.
                                            <?php endif; ?>
                                        </p>
                                        <a href="index.php?url=members/create" class="btn btn-primary btn-sm">
                                            Tambah Anggota Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($members as $m): ?>
                                <tr>
                                    <!-- Nomor Urut -->
                                    <td><?= $no++ ?></td>

                                    <!-- Kode Anggota -->
                                    <td>
                                        <span class="badge badge-primary" style="font-family: monospace; font-size: 0.85rem;">
                                            <?= htmlspecialchars($m['member_code'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <!-- Nama Anggota -->
                                    <td>
                                        <strong style="color: var(--slate-900); font-size: 0.925rem;">
                                            <?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </strong>
                                    </td>

                                    <!-- Kontak -->
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.2rem; font-size: 0.825rem;">
                                            <?php if (!empty($m['email'])): ?>
                                                <span style="color: var(--slate-700);">
                                                    <?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--slate-400); font-style: italic;">Tanpa email</span>
                                            <?php endif; ?>

                                            <?php if (!empty($m['phone'])): ?>
                                                <span style="color: var(--slate-500);">
                                                    <?= htmlspecialchars($m['phone'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Alamat Domisili -->
                                    <td>
                                        <?php if (!empty($m['address'])): ?>
                                            <span style="color: var(--slate-600); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($m['address'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($m['address'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--slate-400); font-style: italic;">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status Peminjaman Aktif -->
                                    <td>
                                        <?php $activeLoans = (int) ($m['active_loans_count'] ?? 0); ?>
                                        <?php if ($activeLoans > 0): ?>
                                            <span class="badge badge-warning" title="Sedang meminjam <?= $activeLoans ?> buku">
                                                <?= $activeLoans ?> Buku Dipinjam
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success">
                                                Bebas Pinjaman
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Tanggal Terdaftar -->
                                    <td>
                                        <span style="font-size: 0.8rem; color: var(--slate-500);">
                                            <?= !empty($m['created_at']) ? date('d/m/Y', strtotime($m['created_at'])) : '-' ?>
                                        </span>
                                    </td>

                                    <!-- Tombol Aksi -->
                                    <td style="text-align: right;">
                                        <div class="table-actions" style="justify-content: flex-end;">
                                            <!-- Tombol Edit -->
                                            <a href="index.php?url=members/edit&id=<?= $m['id'] ?>" class="btn btn-sm btn-secondary" title="Edit Anggota">
                                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Edit
                                            </a>

                                            <!-- Form Hapus (POST dengan CSRF) -->
                                            <form action="index.php?url=members/delete" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota \'<?= htmlspecialchars(addslashes($m['name']), ENT_QUOTES, 'UTF-8') ?>\' (<?= htmlspecialchars($m['member_code'], ENT_QUOTES, 'UTF-8') ?>)?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Hapus Anggota"
                                                >
                                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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

