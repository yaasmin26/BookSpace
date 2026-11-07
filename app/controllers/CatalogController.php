<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Loan.php';

class CatalogController
{
    private ?PDO $db;
    private Book $bookModel;
    private Category $categoryModel;
    private Member $memberModel;
    private Loan $loanModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $this->db            = Database::getConnection();
        $this->bookModel     = new Book();
        $this->categoryModel = new Category();
        $this->memberModel   = new Member();
        $this->loanModel     = new Loan();
    }

    /**
     * Memeriksa autentikasi pengguna
     */
    private function checkAuth(): void
    {
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
    }

    /**
     * Memvalidasi CSRF token
     */
    private function validateCsrf(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Menampilkan katalog buku dengan fitur pencarian dan filter kategori
     */
    public function index(): void
    {
        $this->checkAuth();

        $userId    = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? '';
        $userName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';

        // Hubungkan atau dapatkan data member untuk user yang login
        $member    = $this->memberModel->getOrCreateForUser($userId, $userName, $userEmail);
        $memberId  = (int) ($member['id'] ?? 0);

        // Ambil parameter pencarian dan filter
        $keyword    = isset($_GET['q']) ? trim($_GET['q']) : null;
        $categoryId = !empty($_GET['category']) ? (int) $_GET['category'] : null;

        // Ambil daftar buku yang sesuai filter
        $books = $this->bookModel->getAll($keyword, $categoryId);

        // Ambil seluruh kategori untuk pilihan filter dropdown
        $categories = $this->categoryModel->getAll();

        // Ambil data peminjaman aktif user untuk status tombol di katalog
        $activeLoansCount  = $memberId > 0 ? $this->loanModel->getActiveLoansCountForMember($memberId) : 0;
        $activeLoanBookIds = $memberId > 0 ? $this->loanModel->getActiveLoanBookIds($memberId) : [];

        $pageTitle = 'Katalog Buku - BookSpace';
        $activeNav = 'catalog';

        require_once __DIR__ . '/../views/user/catalog.php';
    }

    /**
     * Menampilkan detail informasi lengkap satu buku
     */
    public function detail(): void
    {
        $this->checkAuth();

        $bookId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($bookId <= 0) {
            $_SESSION['flash_error'] = 'Buku yang Anda cari tidak ditemukan.';
            header('Location: index.php?url=catalog');
            exit;
        }

        $book = $this->bookModel->getById($bookId);
        if (!$book) {
            $_SESSION['flash_error'] = 'Data buku tidak ditemukan dalam sistem.';
            header('Location: index.php?url=catalog');
            exit;
        }

        $userId    = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? '';
        $userName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';

        $member    = $this->memberModel->getOrCreateForUser($userId, $userName, $userEmail);
        $memberId  = (int) ($member['id'] ?? 0);

        $activeLoansCount    = $memberId > 0 ? $this->loanModel->getActiveLoansCountForMember($memberId) : 0;
        $isCurrentlyBorrowed = $memberId > 0 ? $this->loanModel->hasActiveLoan($memberId, $bookId) : false;

        $pageTitle = htmlspecialchars($book['title']) . ' - Detail Buku BookSpace';
        $activeNav = 'catalog';

        require_once __DIR__ . '/../views/user/book_detail.php';
    }

    /**
     * Memproses peminjaman buku secara online oleh anggota (Metode POST)
     */
    public function borrow(): void
    {
        $this->checkAuth();

        // 1. Validasi Metode Request harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Location: index.php?url=catalog');
            exit;
        }

        // 2. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrf($csrfToken)) {
            $_SESSION['flash_error'] = 'Sesi keamanan kedaluwarsa. Silakan ulangi proses peminjaman.';
            header('Location: index.php?url=catalog');
            exit;
        }

        $bookId = isset($_POST['book_id']) ? (int) $_POST['book_id'] : 0;
        if ($bookId <= 0) {
            $_SESSION['flash_error'] = 'Buku yang ingin dipinjam tidak valid.';
            header('Location: index.php?url=catalog');
            exit;
        }

        $userId    = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? '';
        $userName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';

        // 3. Dapatkan atau buat data member yang terhubung dengan akun user
        $member = $this->memberModel->getOrCreateForUser($userId, $userName, $userEmail);
        $memberId = (int) ($member['id'] ?? 0);

        if ($memberId <= 0) {
            $_SESSION['flash_error'] = 'Gagal memverifikasi keanggotaan perpustakaan Anda. Silakan hubungi petugas.';
            header('Location: index.php?url=catalog');
            exit;
        }

        // 4. Validasi Batas Maksimal Peminjaman: Maksimal 3 buku aktif per user
        $activeLoansCount = $this->loanModel->getActiveLoansCountForMember($memberId);
        if ($activeLoansCount >= 3) {
            $_SESSION['flash_error'] = 'Anda sudah mencapai batas maksimal peminjaman (maksimal 3 buku aktif). Harap kembalikan buku yang sedang dipinjam terlebih dahulu.';
            header('Location: index.php?url=my-loans');
            exit;
        }

        // 5. Validasi Cegah Duplikasi: User tidak boleh meminjam buku yang sama jika masih dipinjam
        if ($this->loanModel->hasActiveLoan($memberId, $bookId)) {
            $_SESSION['flash_error'] = 'Anda sedang meminjam buku ini. Harap kembalikan buku tersebut sebelum meminjam kembali.';
            header('Location: index.php?url=my-loans');
            exit;
        }

        // 6. Eksekusi Peminjaman dengan Database Transaction & Row Locking Atomik
        try {
            $this->db->beginTransaction();

            // Kunci baris buku untuk mencegah race condition (stok minus)
            $stmtBook = $this->db->prepare("SELECT id, title, available_stock, total_stock FROM books WHERE id = :id FOR UPDATE");
            $stmtBook->execute([':id' => $bookId]);
            $book = $stmtBook->fetch();

            if (!$book) {
                $this->db->rollBack();
                $_SESSION['flash_error'] = 'Buku yang ingin dipinjam tidak ditemukan.';
                header('Location: index.php?url=catalog');
                exit;
            }

            if ((int) $book['available_stock'] <= 0) {
                $this->db->rollBack();
                $_SESSION['flash_error'] = "Maaf, stok buku '{$book['title']}' sedang habis.";
                header('Location: index.php?url=catalog');
                exit;
            }

            // Tanggal peminjaman dan jatuh tempo (14 hari kerja standar perpustakaan)
            $loanDate = date('Y-m-d');
            $dueDate  = date('Y-m-d', strtotime('+14 days'));

            // Insert ke tabel loans
            $stmtLoan = $this->db->prepare("
                INSERT INTO loans (member_id, book_id, user_id, loan_date, due_date, status)
                VALUES (:member_id, :book_id, :user_id, :loan_date, :due_date, 'borrowed')
            ");
            $successLoan = $stmtLoan->execute([
                ':member_id' => $memberId,
                ':book_id'   => $bookId,
                ':user_id'   => $userId,
                ':loan_date' => $loanDate,
                ':due_date'  => $dueDate,
            ]);

            if (!$successLoan) {
                $this->db->rollBack();
                $_SESSION['flash_error'] = 'Gagal mencatat transaksi peminjaman. Silakan coba beberapa saat lagi.';
                header('Location: index.php?url=catalog');
                exit;
            }

            // Kurangi stok buku secara atomik
            $stmtStock = $this->db->prepare("
                UPDATE books 
                SET available_stock = available_stock - 1 
                WHERE id = :id AND available_stock > 0
            ");
            $stmtStock->execute([':id' => $bookId]);

            if ($stmtStock->rowCount() === 0) {
                $this->db->rollBack();
                $_SESSION['flash_error'] = 'Gagal memperbarui stok buku. Silakan coba kembali.';
                header('Location: index.php?url=catalog');
                exit;
            }

            // Komit transaksi database
            $this->db->commit();

            $formattedDueDate = date('d M Y', strtotime($dueDate));
            $_SESSION['flash_success'] = "Selamat! Buku '{$book['title']}' berhasil dipinjam. Batas waktu pengembalian adalah {$formattedDueDate}.";
            header('Location: index.php?url=my-loans');
            exit;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("CatalogController::borrow Exception: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Terjadi kesalahan sistem saat memproses peminjaman online.';
            header('Location: index.php?url=catalog');
            exit;
        }
    }
}
