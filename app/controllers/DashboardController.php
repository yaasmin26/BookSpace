<?php

require_once __DIR__ . '/../../config/database.php';

class DashboardController
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Menampilkan dashboard dengan data statistik perpustakaan
     */
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Proteksi route: Hanya pengguna terautentikasi yang dapat mengakses dashboard
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }

        // 2. Inisialisasi nilai default statistik (menjamin angka 0 jika data kosong)
        $totalBooks     = 0;
        $totalMembers   = 0;
        $activeLoans    = 0;
        $availableStock = 0;

        // 3. Pengambilan data statistik menggunakan query COUNT() dan SUM()
        try {
            if ($this->db instanceof PDO) {
                // Statistik 1: Total buku menggunakan COUNT()
                $stmtBooks = $this->db->query("SELECT COUNT(*) FROM books");
                $totalBooks = (int) ($stmtBooks->fetchColumn() ?: 0);

                // Statistik 2: Total anggota menggunakan COUNT()
                $stmtMembers = $this->db->query("SELECT COUNT(*) FROM members");
                $totalMembers = (int) ($stmtMembers->fetchColumn() ?: 0);

                // Statistik 3: Total peminjaman aktif dengan status 'borrowed' menggunakan COUNT()
                $stmtLoans = $this->db->prepare("SELECT COUNT(*) FROM loans WHERE status = :status");
                $stmtLoans->execute([':status' => 'borrowed']);
                $activeLoans = (int) ($stmtLoans->fetchColumn() ?: 0);

                // Statistik 4: Total buku yang tersedia dari available_stock menggunakan SUM()
                $stmtStock = $this->db->query("SELECT COALESCE(SUM(available_stock), 0) FROM books");
                $availableStock = (int) ($stmtStock->fetchColumn() ?: 0);
            }
        } catch (PDOException $e) {
            error_log("DashboardController::index Database Error: " . $e->getMessage());
        }

        // Siapkan variabel array statistik untuk dikirim ke View
        $statistics = [
            'total_books'     => $totalBooks,
            'total_members'   => $totalMembers,
            'active_loans'    => $activeLoans,
            'available_stock' => $availableStock,
        ];

        // Pastikan token CSRF tersedia di session untuk tombol aksi (seperti logout)
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // 4. Pisahkan proses pengambilan data dengan View
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
