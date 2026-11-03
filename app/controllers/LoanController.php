<?php

require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Member.php';

class LoanController
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
     * Memvalidasi CSRF token menggunakan perbandingan aman waktu konstan (timing-attack safe)
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
     * Menampilkan daftar transaksi peminjaman buku (Read / Index)
     * Mendukung filter status transaksi ('borrowed' atau 'returned') dan pencarian kata kunci
     */
    public function index(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        // 1. Parameter filter status, pencarian, & rentang tanggal
        $status      = isset($_GET['status']) && in_array($_GET['status'], ['borrowed', 'returned'], true)
            ? $_GET['status']
            : null;
        $keyword     = isset($_GET['q']) ? trim($_GET['q']) : null;
        $memberQuery = isset($_GET['member']) ? trim($_GET['member']) : null;
        $bookQuery   = isset($_GET['book']) ? trim($_GET['book']) : null;
        $startDate   = !empty($_GET['start_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['start_date'])
            ? $_GET['start_date']
            : null;
        $endDate     = !empty($_GET['end_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['end_date'])
            ? $_GET['end_date']
            : null;

        // Cek apakah ada filter yang sedang aktif
        $isFiltered = !empty($keyword) || !empty($status) || !empty($memberQuery) || !empty($bookQuery) || !empty($startDate) || !empty($endDate);

        // 2. Ambil data transaksi dari model Loan sesuai kriteria filter
        $loans = $this->loanModel->getAll($status, $keyword, $startDate, $endDate, $memberQuery, $bookQuery);

        // 3. Kalkulasi data statistik untuk kartu indikator transaksi
        $allLoans           = $this->loanModel->getAll();
        $totalLoans         = count($allLoans);
        $filteredLoansCount = count($loans);
        $activeLoans        = 0;
        $returnedLoans      = 0;
        $overdueLoans       = 0;
        $today              = date('Y-m-d');

        foreach ($allLoans as $l) {
            if ($l['status'] === 'borrowed') {
                $activeLoans++;
                if ($l['due_date'] < $today) {
                    $overdueLoans++;
                }
            } elseif ($l['status'] === 'returned') {
                $returnedLoans++;
            }
        }

        // Tandai setiap baris transaksi yang ditampilkan jika terlambat
        foreach ($loans as &$loan) {
            $loan['is_overdue'] = ($loan['status'] === 'borrowed' && $loan['due_date'] < $today);
            if ($loan['is_overdue']) {
                $diff = (strtotime($today) - strtotime($loan['due_date'])) / 86400;
                $loan['overdue_days'] = max(1, (int) round($diff));
            } else {
                $loan['overdue_days'] = 0;
            }
        }
        unset($loan);

        // 4. Ambil notifikasi flash session
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        // Data admin untuk navigasi
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        require_once __DIR__ . '/../views/loans/index.php';
    }

    /**
     * Menampilkan formulir transaksi peminjaman buku baru (Create Form)
     */
    public function create(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        // 1. Ambil data anggota untuk dropdown pilihan
        $members = $this->memberModel->getAll();

        // 2. Ambil data buku dengan stok yang tersedia (available_stock > 0)
        $allBooks = $this->bookModel->getAll();
        $availableBooks = array_filter($allBooks, function ($b) {
            return (int) ($b['available_stock'] ?? 0) > 0;
        });

        // 3. Ambil error validasi dan input lama dari session jika ada
        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old_input'] ?? [];
        $error  = $_SESSION['error'] ?? null;
        unset($_SESSION['errors'], $_SESSION['old_input'], $_SESSION['error']);

        // 4. Default tanggal: Pinjam hari ini, jatuh tempo 7 hari ke depan
        $defaultLoanDate = date('Y-m-d');
        $defaultDueDate  = date('Y-m-d', strtotime('+7 days'));

        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        require_once __DIR__ . '/../views/loans/create.php';
    }

    /**
     * Memproses penyimpanan transaksi peminjaman buku baru (Store / POST)
     */
    public function store(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.';
            header('Location: index.php?url=loans/create');
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $memberId = !empty($_POST['member_id']) ? (int) $_POST['member_id'] : 0;
        $bookId   = !empty($_POST['book_id']) ? (int) $_POST['book_id'] : 0;
        $loanDate = trim($_POST['loan_date'] ?? date('Y-m-d'));
        $dueDate  = trim($_POST['due_date'] ?? '');
        $userId   = (int) ($_SESSION['user_id'] ?? 0);

        // 3. Validasi Data
        $errors = [];

        // Validasi Anggota
        if ($memberId <= 0) {
            $errors['member_id'] = 'Anggota peminjam wajib dipilih.';
        } else {
            $member = $this->memberModel->getById($memberId);
            if (!$member) {
                $errors['member_id'] = 'Data anggota yang dipilih tidak valid atau tidak ditemukan.';
            }
        }

        // Validasi Buku & Stok
        $book = null;
        if ($bookId <= 0) {
            $errors['book_id'] = 'Buku yang dipinjam wajib dipilih.';
        } else {
            $book = $this->bookModel->getById($bookId);
            if (!$book) {
                $errors['book_id'] = 'Data buku yang dipilih tidak ditemukan.';
            } elseif ((int) ($book['available_stock'] ?? 0) <= 0) {
                $errors['book_id'] = 'Buku "' . htmlspecialchars($book['title']) . '" sedang habis (stok tersedia: 0).';
            }
        }

        // Validasi Tanggal Peminjaman
        if (empty($loanDate)) {
            $errors['loan_date'] = 'Tanggal peminjaman wajib diisi.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $loanDate)) {
            $errors['loan_date'] = 'Format tanggal peminjaman harus YYYY-MM-DD.';
        }

        // Validasi Tanggal Jatuh Tempo
        if (empty($dueDate)) {
            $errors['due_date'] = 'Tanggal jatuh tempo wajib diisi.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
            $errors['due_date'] = 'Format tanggal jatuh tempo harus YYYY-MM-DD.';
        } elseif ($dueDate < $loanDate) {
            $errors['due_date'] = 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal peminjaman.';
        }

        // Jika terdapat error validasi, redirect kembali ke form create
        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            $_SESSION['error']     = 'Mohon periksa kembali form peminjaman yang Anda isi.';
            header('Location: index.php?url=loans/create');
            exit;
        }

        // 4. Eksekusi Simpan Peminjaman via Model Loan
        $loanData = [
            'member_id' => $memberId,
            'book_id'   => $bookId,
            'user_id'   => $userId,
            'loan_date' => $loanDate,
            'due_date'  => $dueDate,
        ];

        if ($this->loanModel->create($loanData)) {
            $bookTitle = $book ? $book['title'] : 'Buku';
            $_SESSION['success'] = "Transaksi peminjaman buku '{$bookTitle}' berhasil dicatat. Stok buku telah dikurangi.";
            header('Location: index.php?url=loans');
        } else {
            $_SESSION['error']     = 'Gagal memproses transaksi peminjaman. Pastikan stok buku masih mencukupi.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=loans/create');
        }
        exit;
    }

    /**
     * Menampilkan detail lengkap suatu transaksi peminjaman (Detail / Read One)
     *
     * @param int|null $id ID transaksi peminjaman opsional
     */
    public function detail($id = null): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $loanId = $id !== null ? (int) $id : (!empty($_GET['id']) ? (int) $_GET['id'] : 0);
        $loan   = $this->loanModel->getById($loanId);

        if (!$loan) {
            $_SESSION['error'] = 'Data transaksi peminjaman tidak ditemukan atau telah dihapus.';
            header('Location: index.php?url=loans');
            exit;
        }

        // Hitung status keterlambatan jika masih berstatus 'borrowed'
        $today = date('Y-m-d');
        $loan['is_overdue'] = ($loan['status'] === 'borrowed' && $loan['due_date'] < $today);
        if ($loan['is_overdue']) {
            $diff = (strtotime($today) - strtotime($loan['due_date'])) / 86400;
            $loan['overdue_days'] = max(1, (int) round($diff));
        } else {
            $loan['overdue_days'] = 0;
        }

        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        if (file_exists(__DIR__ . '/../views/loans/detail.php')) {
            require_once __DIR__ . '/../views/loans/detail.php';
        } else {
            require_once __DIR__ . '/../views/loans/index.php';
        }
    }

    /**
     * Memproses pengembalian buku (Return Book / POST)
     *
     * @param int|null $id ID transaksi peminjaman opsional
     */
    public function returnBook($id = null): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=loans');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=loans');
            exit;
        }

        $loanId = $id !== null 
            ? (int) $id 
            : (!empty($_POST['id']) ? (int) $_POST['id'] : (!empty($_GET['id']) ? (int) $_GET['id'] : 0));

        if ($loanId <= 0) {
            $_SESSION['error'] = 'ID transaksi peminjaman tidak valid.';
            header('Location: index.php?url=loans');
            exit;
        }

        // 2. Ambil data transaksi peminjaman
        $loan = $this->loanModel->getById($loanId);

        if (!$loan) {
            $_SESSION['error'] = 'Data transaksi peminjaman tidak ditemukan.';
            header('Location: index.php?url=loans');
            exit;
        }

        // 3. Validasi status pinjaman saat ini
        if ($loan['status'] === 'returned') {
            $_SESSION['error'] = 'Buku ini sudah pernah dikembalikan sebelumnya pada tanggal ' . htmlspecialchars($loan['return_date'] ?? '') . '.';
            header('Location: index.php?url=loans');
            exit;
        }

        // 4. Eksekusi pengembalian buku via Model Loan
        if ($this->loanModel->returnBook($loanId)) {
            $bookTitle  = htmlspecialchars($loan['book_title'] ?? 'Buku');
            $memberName = htmlspecialchars($loan['member_name'] ?? 'Anggota');
            $_SESSION['success'] = "Buku '{$bookTitle}' yang dipinjam oleh {$memberName} berhasil dikembalikan. Stok buku telah ditambahkan kembali.";
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat memproses pengembalian buku.';
        }

        header('Location: index.php?url=loans');
        exit;
    }
}

