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
     * Menampilkan dashboard dengan data statistik, kategori, dan buku terbaru perpustakaan
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

        // 2. Inisialisasi nilai default statistik & data pendukung
        $totalBooks     = 0;
        $totalMembers   = 0;
        $activeLoans    = 0;
        $availableStock = 0;
        $categories     = [];
        $recentBooks    = [];

        // 3. Pengambilan data statistik dan konten dashboard dari database
        try {
            if ($this->db instanceof PDO) {
                // Statistik 1: Total judul buku
                $stmtBooks = $this->db->query("SELECT COUNT(*) FROM books");
                $totalBooks = (int) ($stmtBooks->fetchColumn() ?: 0);

                // Statistik 2: Total anggota perpustakaan terdaftar
                $stmtMembers = $this->db->query("SELECT COUNT(*) FROM members");
                $totalMembers = (int) ($stmtMembers->fetchColumn() ?: 0);

                // Statistik 3: Total peminjaman aktif dengan status 'borrowed'
                $stmtLoans = $this->db->prepare("SELECT COUNT(*) FROM loans WHERE status = :status");
                $stmtLoans->execute([':status' => 'borrowed']);
                $activeLoans = (int) ($stmtLoans->fetchColumn() ?: 0);

                // Statistik 4: Total eksemplar buku yang tersedia untuk dipinjam
                $stmtStock = $this->db->query("SELECT COALESCE(SUM(available_stock), 0) FROM books");
                $availableStock = (int) ($stmtStock->fetchColumn() ?: 0);

                // Data Kategori Buku (dilengkapi sub-kueri jumlah buku per kategori)
                $stmtCategories = $this->db->query(
                    "SELECT c.id, c.category_name, c.description,
                            (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS total_books
                     FROM categories c
                     ORDER BY total_books DESC, c.category_name ASC
                     LIMIT 6"
                );
                $categories = $stmtCategories->fetchAll() ?: [];

                // Data Koleksi Buku Terbaru (5 buku paling terkini yang ditambahkan)
                $stmtRecent = $this->db->query(
                    "SELECT b.id, b.category_id, b.title, b.author, b.publisher,
                            b.publication_year, b.isbn, b.total_stock, b.available_stock,
                            c.category_name
                     FROM books b
                     LEFT JOIN categories c ON b.category_id = c.id
                     ORDER BY b.id DESC
                     LIMIT 5"
                );
                $recentBooks = $stmtRecent->fetchAll() ?: [];
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

        // 4. Muat View Dashboard Utama
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
