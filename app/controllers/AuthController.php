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
        // Jika sudah login, arahkan langsung ke dashboard sesuai role
        if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
            if (($_SESSION['role'] ?? 'user') === 'admin') {
                header('Location: index.php?url=dashboard');
            } else {
                header('Location: index.php?url=user/dashboard');
            }
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
        $identifier = trim($_POST['email'] ?? $_POST['username'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            $error = 'Email atau username dan password wajib diisi.';
            $old_email = $identifier;
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // 4. Cari pengguna melalui User Model (bisa berupa email atau username)
        $user = $this->userModel->findByIdentifier($identifier);

        // 5. Verifikasi password dengan password_verify()
        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Email/username atau password yang Anda masukkan salah.';
            $old_email = $identifier;
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // 6. Regenerasi session ID untuk mencegah serangan Session Fixation
        session_regenerate_id(true);

        // 7. Simpan informasi pengguna ke dalam session
        $_SESSION['user_id']      = (int) $user['id'];
        $_SESSION['email']        = $user['email'];
        $_SESSION['name']         = $user['name'];
        $_SESSION['nama_lengkap'] = $user['name'];
        $_SESSION['username']     = $user['username'] ?? $user['name'];
        $_SESSION['role']         = $user['role'] ?? 'user';
        $_SESSION['logged_in']    = true;

        // Perbarui CSRF token setelah login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // 8. Redirect berdasarkan role pengguna
        if ($_SESSION['role'] === 'admin') {
            header('Location: index.php?url=dashboard');
        } else {
            header('Location: index.php?url=user/dashboard');
        }
        exit;
    }

    /**
     * Menampilkan halaman pendaftaran (Sign Up / Register)
     */
    public function register(): void
    {
        // Jika sudah login, redirect langsung ke dashboard masing-masing
        if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
            if (($_SESSION['role'] ?? 'user') === 'admin') {
                header('Location: index.php?url=dashboard');
            } else {
                header('Location: index.php?url=user/dashboard');
            }
            exit;
        }

        // Siapkan CSRF token
        $this->generateCsrfToken();

        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Memproses data pendaftaran pengguna baru (Metode POST)
     */
    public function processRegister(): void
    {
        // 1. Validasi metode request harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=register');
            exit;
        }

        // 2. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $error = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 3. Ambil dan bersihkan input
        $name            = trim($_POST['name'] ?? '');
        $username        = trim($_POST['username'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $old_input = [
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
        ];

        // 4. Validasi kelengkapan semua field
        if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $error = 'Semua bidang isian (nama, username, email, password, dan konfirmasi) wajib diisi.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 5. Validasi format email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format alamat email tidak valid.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 6. Validasi panjang karakter username
        if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_.-]+$/', $username)) {
            $error = 'Username minimal 3 karakter dan hanya boleh berisi huruf, angka, titik, atau garis bawah.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 7. Validasi panjang password minimal 6 karakter
        if (strlen($password) < 6) {
            $error = 'Password harus memiliki panjang minimal 6 karakter.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 8. Validasi kesesuaian password dan konfirmasi password
        if ($password !== $confirmPassword) {
            $error = 'Konfirmasi password tidak cocok dengan password yang dimasukkan.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 9. Validasi keunikan email
        if ($this->userModel->emailExists($email)) {
            $error = 'Alamat email sudah terdaftar. Silakan gunakan email lain atau login.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 10. Validasi keunikan username
        if ($this->userModel->usernameExists($username)) {
            $error = 'Username sudah digunakan oleh akun lain. Silakan pilih username lain.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // 11. Simpan user baru dengan role 'user'
        $newUserId = $this->userModel->create([
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'password' => $password,
            'role'     => 'user',
        ]);

        if (!$newUserId) {
            $error = 'Terjadi kesalahan sistem saat mendaftarkan akun. Silakan coba lagi.';
            $this->generateCsrfToken();
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        // Opsional: Buat catatan anggota di tabel members agar terintegrasi dengan peminjaman
        try {
            $db = Database::getConnection();
            if ($db) {
                $checkMember = $db->prepare("SELECT id FROM members WHERE email = :email LIMIT 1");
                $checkMember->execute([':email' => $email]);
                if (!$checkMember->fetch()) {
                    $memberCode = 'MBR-' . date('y') . str_pad((string)$newUserId, 4, '0', STR_PAD_LEFT);
                    $insMember = $db->prepare("INSERT INTO members (member_code, name, email) VALUES (:code, :name, :email)");
                    $insMember->execute([
                        ':code'  => $memberCode,
                        ':name'  => $name,
                        ':email' => $email,
                    ]);
                }
            }
        } catch (Throwable $e) {
            error_log("Member sync notice: " . $e->getMessage());
        }

        // 12. Set notifikasi sukses dan arahkan ke halaman login
        $_SESSION['flash_success'] = 'Registrasi berhasil, silakan login.';
        header('Location: index.php?url=login');
        exit;
    }

    /**
     * Memproses logout pengguna (Metode POST)
     */
    public function logout(): void
    {
        // 1. Validasi metode request harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=login');
            exit;
        }

        // 2. Validasi CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            header('Location: index.php?url=login');
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
