<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
    }

    /**
     * Menampilkan halaman login
     */
    public function login(): void
    {
        // Jika sudah login, redirect langsung ke dashboard
        if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        // Siapkan CSRF token
        $this->generateCsrfToken();

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Memproses data login (Metode POST)
     */
    public function authenticate(): void
    {
        // 1. Validasi metode request harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=login');
            exit;
        }

        // 2. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $error = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan muat ulang halaman.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // 3. Validasi & sanitasi input
        $email = trim($_POST['email'] ?? $_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Email dan password wajib diisi.';
            $old_email = $email;
            $old_username = $email;
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // 4. Cari pengguna melalui User Model berdasarkan email
        $user = $this->userModel->findByEmail($email);

        // 5. Verifikasi password dengan password_verify()
        if (!$user || !password_verify($password, $user['password'])) {
            // Pesan kesalahan jika akun tidak ditemukan atau password tidak cocok
            $error = 'Email atau password yang Anda masukkan salah.';
            $old_email = $email;
            $old_username = $email;
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // 6. Regenerasi session ID untuk mencegah serangan Session Fixation
        session_regenerate_id(true);

        // 7. Simpan informasi pengguna ke dalam session
        $_SESSION['user_id']      = $user['id'];
        $_SESSION['email']        = $user['email'];
        $_SESSION['nama_lengkap'] = $user['name'] ?? $user['nama_lengkap'] ?? 'Admin';
        $_SESSION['name']         = $user['name'] ?? 'Admin';
        $_SESSION['role']         = $user['role'] ?? 'admin';
        $_SESSION['logged_in']    = true;

        // Perbarui CSRF token setelah login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // 8. Redirect ke halaman dashboard
        header('Location: index.php?url=dashboard');
        exit;
    }

    /**
     * Memproses logout pengguna (Metode POST)
     */
    public function logout(): void
    {
        // 1. Validasi metode request harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=dashboard');
            exit;
        }

        // 2. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        // 3. Bersihkan seluruh variabel session
        $_SESSION = [];

        // 4. Hapus cookie session dari browser jika digunakan
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 5. Hancurkan session di server
        session_destroy();

        // 6. Redirect ke halaman login
        header('Location: index.php?url=login');
        exit;
    }

    /**
     * Membuat CSRF token baru jika belum ada
     *
     * @return string
     */
    public function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Memvalidasi CSRF token menggunakan perbandingan aman waktu konstan
     *
     * @param string|null $token
     * @return bool
     */
    private function validateCsrfToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
