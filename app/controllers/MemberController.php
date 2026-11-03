<?php

require_once __DIR__ . '/../models/Member.php';

class MemberController
{
    private Member $memberModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

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
     * Menampilkan daftar seluruh anggota perpustakaan (Read / Index)
     * Mendukung pencarian kata kunci (nama, kode anggota, email, nomor telepon)
     */
    public function index(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $keyword = isset($_GET['q']) ? trim($_GET['q']) : null;
        $members = $this->memberModel->getAll($keyword);

        // Ambil notifikasi flash session
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        // Data admin untuk navigasi
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        require_once __DIR__ . '/../views/members/index.php';
    }

    /**
     * Menampilkan formulir pendaftaran anggota baru (Create Form)
     */
    public function create(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $suggestedCode = $this->memberModel->generateMemberCode();
        $errors        = $_SESSION['errors'] ?? [];
        $old           = $_SESSION['old_input'] ?? [];
        $csrfToken     = $_SESSION['csrf_token'];
        $adminName     = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';

        unset($_SESSION['errors'], $_SESSION['old_input']);

        require_once __DIR__ . '/../views/members/create.php';
    }

    /**
     * Menyimpan data anggota baru ke database (Store / POST)
     */
    public function store(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=members');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.';
            header('Location: index.php?url=members/create');
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $memberCode = trim($_POST['member_code'] ?? '');
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $address    = trim($_POST['address'] ?? '');

        // Jika kode anggota dikosongkan, gunakan kode otomatis
        if (empty($memberCode)) {
            $memberCode = $this->memberModel->generateMemberCode();
        } else {
            $memberCode = strtoupper($memberCode);
        }

        // 3. Validasi Data
        $errors = [];

        // Validasi Kode Anggota
        if (empty($memberCode)) {
            $errors['member_code'] = 'Kode anggota wajib diisi.';
        } elseif (mb_strlen($memberCode) > 20) {
            $errors['member_code'] = 'Kode anggota maksimal 20 karakter.';
        } elseif ($this->memberModel->codeExists($memberCode)) {
            $errors['member_code'] = 'Kode anggota "' . $memberCode . '" sudah digunakan. Silakan gunakan kode lain.';
        }

        // Validasi Nama
        if (empty($name)) {
            $errors['name'] = 'Nama lengkap anggota wajib diisi.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Nama lengkap maksimal 100 karakter.';
        }

        // Validasi Email (opsional, namun harus valid dan unik jika diisi)
        if (!empty($email)) {
            if (mb_strlen($email) > 100) {
                $errors['email'] = 'Email maksimal 100 karakter.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Format alamat email tidak valid.';
            } elseif ($this->memberModel->emailExists($email)) {
                $errors['email'] = 'Alamat email tersebut sudah digunakan oleh anggota lain.';
            }
        }

        // Validasi Nomor Telepon (opsional, maks 20 karakter)
        if (!empty($phone) && mb_strlen($phone) > 20) {
            $errors['phone'] = 'Nomor telepon maksimal 20 karakter.';
        }

        // Jika terdapat error validasi, redirect kembali ke form
        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=members/create');
            exit;
        }

        // 4. Eksekusi Simpan Data
        $data = [
            'member_code' => $memberCode,
            'name'        => $name,
            'email'       => !empty($email) ? $email : null,
            'phone'       => !empty($phone) ? $phone : null,
            'address'     => !empty($address) ? $address : null,
        ];

        if ($this->memberModel->create($data)) {
            $_SESSION['success'] = 'Anggota perpustakaan "' . $name . '" (' . $memberCode . ') berhasil didaftarkan.';
            header('Location: index.php?url=members');
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat menyimpan data anggota.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=members/create');
        }
        exit;
    }

    /**
     * Menampilkan formulir edit anggota (Edit Form)
     */
    public function edit(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $id = !empty($_GET['id']) ? (int) $_GET['id'] : 0;
        $member = $this->memberModel->getById($id);

        if (!$member) {
            $_SESSION['error'] = 'Data anggota tidak ditemukan atau telah dihapus.';
            header('Location: index.php?url=members');
            exit;
        }

        $errors    = $_SESSION['errors'] ?? [];
        $old       = $_SESSION['old_input'] ?? [];
        $csrfToken = $_SESSION['csrf_token'];
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';

        unset($_SESSION['errors'], $_SESSION['old_input']);

        require_once __DIR__ . '/../views/members/edit.php';
    }

    /**
     * Memperbarui data anggota di database (Update / POST)
     */
    public function update(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=members');
            exit;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $existingMember = $this->memberModel->getById($id);

        if (!$existingMember) {
            $_SESSION['error'] = 'Data anggota tidak ditemukan.';
            header('Location: index.php?url=members');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=members/edit&id=' . $id);
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $memberCode = strtoupper(trim($_POST['member_code'] ?? ''));
        $name       = trim($_POST['name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $address    = trim($_POST['address'] ?? '');

        // 3. Validasi Data
        $errors = [];

        if (empty($memberCode)) {
            $errors['member_code'] = 'Kode anggota wajib diisi.';
        } elseif (mb_strlen($memberCode) > 20) {
            $errors['member_code'] = 'Kode anggota maksimal 20 karakter.';
        } elseif ($this->memberModel->codeExists($memberCode, $id)) {
            $errors['member_code'] = 'Kode anggota "' . $memberCode . '" sudah digunakan oleh anggota lain.';
        }

        if (empty($name)) {
            $errors['name'] = 'Nama lengkap anggota wajib diisi.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Nama lengkap maksimal 100 karakter.';
        }

        if (!empty($email)) {
            if (mb_strlen($email) > 100) {
                $errors['email'] = 'Email maksimal 100 karakter.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Format alamat email tidak valid.';
            } elseif ($this->memberModel->emailExists($email, $id)) {
                $errors['email'] = 'Alamat email tersebut sudah digunakan oleh anggota lain.';
            }
        }

        if (!empty($phone) && mb_strlen($phone) > 20) {
            $errors['phone'] = 'Nomor telepon maksimal 20 karakter.';
        }

        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=members/edit&id=' . $id);
            exit;
        }

        // 4. Eksekusi Update Data
        $data = [
            'member_code' => $memberCode,
            'name'        => $name,
            'email'       => !empty($email) ? $email : null,
            'phone'       => !empty($phone) ? $phone : null,
            'address'     => !empty($address) ? $address : null,
        ];

        if ($this->memberModel->update($id, $data)) {
            $_SESSION['success'] = 'Data anggota "' . $name . '" berhasil diperbarui.';
            header('Location: index.php?url=members');
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat memperbarui data anggota.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=members/edit&id=' . $id);
        }
        exit;
    }

    /**
     * Menghapus data anggota dari database (Delete / POST)
     * Menjaga integritas foreign key dari tabel loans
     */
    public function delete(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=members');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=members');
            exit;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $member = $this->memberModel->getById($id);

        if (!$member) {
            $_SESSION['error'] = 'Data anggota tidak ditemukan atau telah dihapus sebelumnya.';
            header('Location: index.php?url=members');
            exit;
        }

        // 2. Validasi Foreign Key Relasi: Cek apakah memiliki transaksi peminjaman
        if ($this->memberModel->hasLoans($id)) {
            $_SESSION['error'] = 'Anggota "' . $member['name'] . '" (' . $member['member_code'] . ') tidak dapat dihapus karena masih memiliki riwayat transaksi peminjaman buku perpustakaan.';
            header('Location: index.php?url=members');
            exit;
        }

        // 3. Eksekusi Hapus Anggota
        if ($this->memberModel->delete($id)) {
            $_SESSION['success'] = 'Anggota "' . $member['name'] . '" berhasil dihapus dari sistem.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus anggota dari sistem.';
        }

        header('Location: index.php?url=members');
        exit;
    }
}

