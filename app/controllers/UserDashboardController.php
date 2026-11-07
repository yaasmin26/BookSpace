<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Book.php';

class UserDashboardController
{
    private ?PDO $db;
    private Member $memberModel;
    private Loan $loanModel;
    private Book $bookModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
        $this->db          = Database::getConnection();
        $this->memberModel = new Member();
        $this->loanModel   = new Loan();
        $this->bookModel   = new Book();
    }

    /**
     * Menampilkan dashboard utama untuk pengguna biasa (User / Member)
     */
    public function index(): void
    {
        // 1. Validasi Autentikasi: Hanya user yang sudah login
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }

        // Jika admin membuka halaman ini, arahkan ke dashboard admin
        if (($_SESSION['role'] ?? '') === 'admin') {
            header('Location: index.php?url=dashboard');
            exit;
        }

        $userId    = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? '';
        $userName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';

        // 2. Hubungkan atau buatkan profil data member untuk user ini
        $member = $this->memberModel->getOrCreateForUser($userId, $userName, $userEmail);
        $memberId = (int) ($member['id'] ?? 0);

        // 3. Ambil data statistik ringkas untuk kartu indikator
        $activeLoansCount    = $memberId > 0 ? $this->loanModel->getActiveLoansCountForMember($memberId) : 0;
        $activeLoans         = $memberId > 0 ? $this->loanModel->getActiveLoansByMember($memberId) : [];
        $allLoans            = $memberId > 0 ? $this->loanModel->getAllLoansByMember($memberId) : [];
        $totalLoansCount     = count($allLoans);
        $activeLoanBookIds   = $memberId > 0 ? $this->loanModel->getActiveLoanBookIds($memberId) : [];

        // 4. Ambil buku terbaru yang siap dipinjam (stok > 0)
        $featuredBooks = [];
        if ($this->db instanceof PDO) {
            try {
                $stmtBooks = $this->db->query("
                    SELECT b.*, c.category_name 
                    FROM books b 
                    LEFT JOIN categories c ON b.category_id = c.id 
                    WHERE b.available_stock > 0 
                    ORDER BY b.id DESC 
                    LIMIT 6
                ");
                $featuredBooks = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

                // Total koleksi buku tersedia
                $stmtCount = $this->db->query("SELECT COUNT(*) FROM books WHERE available_stock > 0");
                $availableBooksCount = (int) ($stmtCount->fetchColumn() ?: 0);
            } catch (PDOException $e) {
                error_log("UserDashboardController Error: " . $e->getMessage());
                $availableBooksCount = 0;
            }
        } else {
            $availableBooksCount = 0;
        }

        // Pastikan CSRF token tersedia
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $pageTitle = 'Beranda - BookSpace Perpustakaan';
        $activeNav = 'dashboard';

        require_once __DIR__ . '/../views/user/dashboard.php';
    }
}
