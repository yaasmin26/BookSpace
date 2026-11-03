<?php

require_once __DIR__ . '/../models/Category.php';

class CategoryController
{
    private Category $categoryModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $this->categoryModel = new Category();
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
     * Menampilkan daftar seluruh kategori (Read / Index)
     * Mendukung pencarian kata kunci nama kategori
     */
    public function index(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $keyword = isset($_GET['q']) ? trim($_GET['q']) : null;
        $allCategories = $this->categoryModel->getAll();

        // Filter kategori berdasarkan kata kunci pencarian jika diberikan
        if (!empty($keyword)) {
            $categories = array_filter($allCategories, function ($cat) use ($keyword) {
                return (stripos($cat['category_name'], $keyword) !== false) 
                    || (!empty($cat['description']) && stripos($cat['description'], $keyword) !== false);
            });
        } else {
            $categories = $allCategories;
        }

        // Ambil pesan flash session
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        // Data admin untuk navigasi
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        require_once __DIR__ . '/../views/categories/index.php';
    }

    /**
     * Menampilkan formulir tambah kategori baru (Create Form)
     */
    public function create(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $errors    = $_SESSION['errors'] ?? [];
        $old       = $_SESSION['old_input'] ?? [];
        $csrfToken = $_SESSION['csrf_token'];
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';

        unset($_SESSION['errors'], $_SESSION['old_input']);

        require_once __DIR__ . '/../views/categories/create.php';
    }

    /**
     * Menyimpan data kategori baru ke database (Store / POST)
     */
    public function store(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=categories');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.';
            header('Location: index.php?url=categories/create');
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $categoryName = trim($_POST['category_name'] ?? '');
        $description  = trim($_POST['description'] ?? '');

        // 3. Validasi Input
        $errors = [];

        if (empty($categoryName)) {
            $errors['category_name'] = 'Nama kategori wajib diisi.';
        } elseif (mb_strlen($categoryName) > 100) {
            $errors['category_name'] = 'Nama kategori maksimal 100 karakter.';
        } elseif ($this->categoryModel->nameExists($categoryName)) {
            $errors['category_name'] = 'Nama kategori sudah terdaftar. Silakan gunakan nama lain.';
        }

        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=categories/create');
            exit;
        }

        // 4. Eksekusi Simpan Data
        $data = [
            'category_name' => $categoryName,
            'description'   => !empty($description) ? $description : null,
        ];

        if ($this->categoryModel->create($data)) {
            $_SESSION['success'] = 'Kategori "' . $categoryName . '" berhasil ditambahkan.';
            header('Location: index.php?url=categories');
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat menyimpan data kategori.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=categories/create');
        }
        exit;
    }

    /**
     * Menampilkan formulir edit kategori (Edit Form)
     */
    public function edit(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $id = !empty($_GET['id']) ? (int) $_GET['id'] : 0;
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $_SESSION['error'] = 'Data kategori tidak ditemukan atau telah dihapus.';
            header('Location: index.php?url=categories');
            exit;
        }

        $errors    = $_SESSION['errors'] ?? [];
        $old       = $_SESSION['old_input'] ?? [];
        $csrfToken = $_SESSION['csrf_token'];
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';

        unset($_SESSION['errors'], $_SESSION['old_input']);

        require_once __DIR__ . '/../views/categories/edit.php';
    }

    /**
     * Memperbarui data kategori di database (Update / POST)
     */
    public function update(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=categories');
            exit;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $existingCategory = $this->categoryModel->getById($id);

        if (!$existingCategory) {
            $_SESSION['error'] = 'Data kategori tidak ditemukan.';
            header('Location: index.php?url=categories');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=categories/edit&id=' . $id);
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $categoryName = trim($_POST['category_name'] ?? '');
        $description  = trim($_POST['description'] ?? '');

        // 3. Validasi Input
        $errors = [];

        if (empty($categoryName)) {
            $errors['category_name'] = 'Nama kategori wajib diisi.';
        } elseif (mb_strlen($categoryName) > 100) {
            $errors['category_name'] = 'Nama kategori maksimal 100 karakter.';
        } elseif ($this->categoryModel->nameExists($categoryName, $id)) {
            $errors['category_name'] = 'Nama kategori sudah digunakan oleh kategori lain.';
        }

        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=categories/edit&id=' . $id);
            exit;
        }

        // 4. Eksekusi Update Data
        $data = [
            'category_name' => $categoryName,
            'description'   => !empty($description) ? $description : null,
        ];

        if ($this->categoryModel->update($id, $data)) {
            $_SESSION['success'] = 'Data kategori "' . $categoryName . '" berhasil diperbarui.';
            header('Location: index.php?url=categories');
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat memperbarui data kategori.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=categories/edit&id=' . $id);
        }
        exit;
    }

    /**
     * Menghapus data kategori dari database (Delete / POST)
     * Menjaga integritas foreign key dari tabel books
     */
    public function delete(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=categories');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=categories');
            exit;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            $_SESSION['error'] = 'Data kategori tidak ditemukan atau telah dihapus sebelumnya.';
            header('Location: index.php?url=categories');
            exit;
        }

        // 2. Validasi Foreign Key Relasi: Cek apakah masih digunakan oleh buku
        if ($this->categoryModel->hasBooks($id)) {
            $_SESSION['error'] = 'Kategori "' . $category['category_name'] . '" tidak dapat dihapus karena masih digunakan oleh koleksi buku.';
            header('Location: index.php?url=categories');
            exit;
        }

        // 3. Eksekusi Hapus Kategori
        if ($this->categoryModel->delete($id)) {
            $_SESSION['success'] = 'Kategori "' . $category['category_name'] . '" berhasil dihapus dari sistem.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kategori dari sistem.';
        }

        header('Location: index.php?url=categories');
        exit;
    }
}

