<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Member.php';

class UserLoanController
{
    private Loan $loanModel;
    private Member $memberModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $this->loanModel   = new Loan();
        $this->memberModel = new Member();
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
     * Menampilkan halaman Peminjaman Saya (Buku yang sedang aktif dipinjam)
     */
    public function myLoans(): void
    {
        $this->checkAuth();

        $userId    = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? '';
        $userName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';

        $member   = $this->memberModel->getOrCreateForUser($userId, $userName, $userEmail);
        $memberId = (int) ($member['id'] ?? 0);

        // Ambil hanya peminjaman yang berstatus 'borrowed'
        $activeLoans      = $memberId > 0 ? $this->loanModel->getActiveLoansByMember($memberId) : [];
        $activeLoansCount = count($activeLoans);

        $pageTitle = 'Peminjaman Saya - BookSpace';
        $activeNav = 'my_loans';

        require_once __DIR__ . '/../views/user/my_loans.php';
    }

    /**
     * Menampilkan halaman Riwayat Peminjaman (Seluruh transaksi yang pernah dilakukan)
     */
    public function history(): void
    {
        $this->checkAuth();

        $userId    = (int) $_SESSION['user_id'];
        $userEmail = $_SESSION['email'] ?? '';
        $userName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Anggota';

        $member   = $this->memberModel->getOrCreateForUser($userId, $userName, $userEmail);
        $memberId = (int) ($member['id'] ?? 0);

        // Ambil seluruh data riwayat peminjaman (borrowed maupun returned)
        $allLoans      = $memberId > 0 ? $this->loanModel->getAllLoansByMember($memberId) : [];
        $activeCount   = 0;
        $returnedCount = 0;

        foreach ($allLoans as $l) {
            if ($l['status'] === 'borrowed') {
                $activeCount++;
            } elseif ($l['status'] === 'returned') {
                $returnedCount++;
            }
        }

        $pageTitle = 'Riwayat Peminjaman - BookSpace';
        $activeNav = 'history';

        require_once __DIR__ . '/../views/user/loan_history.php';
    }
}
