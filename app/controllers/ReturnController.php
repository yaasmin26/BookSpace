<?php

require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Member.php';

class ReturnController
{
    private Loan $loanModel;
    private Book $bookModel;
    private Member $memberModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $this->loanModel   = new Loan();
        $this->bookModel   = new Book();
        $this->memberModel = new Member();
    }

    /**
     * Memeriksa autentikasi admin sebelum mengakses aksi controller
     */
    private function checkAuth(): void
    {
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
    }

    /**
     * Memvalidasi CSRF token menggunakan perbandingan aman waktu konstan
     *
     * @param string|null $token
     * @return bool
     */
    private function validateCsrf(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Memastikan CSRF token tersedia di session
     */
    private function ensureCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Menampilkan halaman pengelolaan Pengembalian Buku (Read / Index)
     * Fokus utama: Daftar buku yang sedang dipinjam dan harus dikembalikan (status: borrowed)
     * Serta menyediakan seksi riwayat pengembalian yang sudah selesai (status: returned)
     */
    public function index(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        // 1. Parameter pencarian & filter
        $keyword     = !empty($_GET['q']) ? trim($_GET['q']) : null;
        $memberQuery = !empty($_GET['member']) ? trim($_GET['member']) : null;
        $bookQuery   = !empty($_GET['book']) ? trim($_GET['book']) : null;
        $startDate   = !empty($_GET['start_date']) ? trim($_GET['start_date']) : null;
        $endDate     = !empty($_GET['end_date']) ? trim($_GET['end_date']) : null;
        $activeTab   = isset($_GET['tab']) && in_array($_GET['tab'], ['queue', 'history'], true)
            ? $_GET['tab']
            : 'queue';

        $isFiltered = !empty($keyword) || !empty($memberQuery) || !empty($bookQuery) || !empty($startDate) || !empty($endDate);

        // 2. Ambil antrean pengembalian (Buku yang masih dipinjam / belum kembali)
        $pendingLoans = $this->loanModel->getAll('borrowed', $keyword, $startDate, $endDate, $memberQuery, $bookQuery);

        // 3. Ambil riwayat pengembalian yang sudah selesai
        $historyLoans = $this->loanModel->getAll('returned', $keyword, $startDate, $endDate, $memberQuery, $bookQuery);

        // 4. Kalkulasi statistik pengembalian
        $allLoans        = $this->loanModel->getAll();
        $totalPending    = 0;
        $totalOverdue    = 0;
        $returnedToday   = 0;
        $totalReturned   = 0;
        $today           = date('Y-m-d');

        foreach ($allLoans as $l) {
            if ($l['status'] === 'borrowed') {
                $totalPending++;
                if ($l['due_date'] < $today) {
                    $totalOverdue++;
                }
            } elseif ($l['status'] === 'returned') {
                $totalReturned++;
                if (!empty($l['return_date']) && $l['return_date'] === $today) {
                    $returnedToday++;
                }
            }
        }

        // Tandai baris transaksi antrean jika terlambat & hitung selisih hari
        foreach ($pendingLoans as &$loan) {
            $loan['is_overdue'] = ($loan['due_date'] < $today);
            if ($loan['is_overdue']) {
                $diff = (strtotime($today) - strtotime($loan['due_date'])) / 86400;
                $loan['overdue_days'] = max(1, (int) round($diff));
            } else {
                $loan['overdue_days'] = 0;
            }
        }
        unset($loan);

        // 5. Flash notification
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        require_once __DIR__ . '/../views/returns/index.php';
    }

    /**
     * Memproses pengembalian buku (Return Book Action / POST)
     * Mengubah status menjadi 'returned', mencatat tanggal pengembalian, dan menambah stok buku kembali
     */
    public function process(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=returns');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.';
            header('Location: index.php?url=returns');
            exit;
        }

        $loanId = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($loanId <= 0) {
            $_SESSION['error'] = 'ID transaksi peminjaman tidak valid.';
            header('Location: index.php?url=returns');
            exit;
        }

        // 2. Ambil data transaksi saat ini untuk memvalidasi status
        $loan = $this->loanModel->getById($loanId);
        if (!$loan) {
            $_SESSION['error'] = 'Data transaksi peminjaman tidak ditemukan.';
            header('Location: index.php?url=returns');
            exit;
        }

        // 3. Validasi status: Cegah pemrosesan ganda jika sudah berstatus 'returned'
        if ($loan['status'] !== 'borrowed') {
            $_SESSION['error'] = "Buku '{$loan['book_title']}' sudah pernah dikembalikan sebelumnya pada tanggal " . date('d/m/Y', strtotime($loan['return_date'])) . ". Transaksi tidak dapat diproses dua kali.";
            header('Location: index.php?url=returns');
            exit;
        }

        // 4. Proses pengembalian secara atomik di database
        $success = $this->loanModel->returnBook($loanId);

        if ($success) {
            $_SESSION['success'] = "Pengembalian buku '{$loan['book_title']}' oleh anggota {$loan['member_name']} ({$loan['member_code']}) berhasil diproses! Stok fisik buku telah ditambahkan kembali ke katalog perpustakaan.";
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat memproses pengembalian buku. Silakan coba lagi.';
        }

        header('Location: index.php?url=returns');
        exit;
    }
}
