<?php

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Category.php';

class BookController
{
    private Book $bookModel;
    private Category $categoryModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $this->bookModel = new Book();
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
     * Menampilkan daftar seluruh buku (Read / Index)
     * Mendukung pencarian kata kunci dan filter berdasarkan kategori
     */
    public function index(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        // Parameter pencarian & filter
        $keyword    = isset($_GET['q']) ? trim($_GET['q']) : null;
        $categoryId = !empty($_GET['category_id']) ? (int) $_GET['category_id'] : null;

        // Ambil data buku dan kategori
        $books      = $this->bookModel->getAll($keyword, $categoryId);
        $categories = $this->categoryModel->getAll();

        // Ambil notifikasi flash session
        $success = $_SESSION['success'] ?? null;
        $error   = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        // Data admin untuk navigasi
        $adminName = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';
        $csrfToken = $_SESSION['csrf_token'];

        require_once __DIR__ . '/../views/books/index.php';
    }

    /**
     * Menampilkan formulir tambah buku baru (Create Form)
     */
    public function create(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $categories = $this->categoryModel->getAll();
        $errors     = $_SESSION['errors'] ?? [];
        $old        = $_SESSION['old_input'] ?? [];
        $csrfToken  = $_SESSION['csrf_token'];
        $adminName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';

        unset($_SESSION['errors'], $_SESSION['old_input']);

        require_once __DIR__ . '/../views/books/create.php';
    }

    /**
     * Menyimpan data buku baru ke database (Store / POST)
     */
    public function store(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=books');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.';
            header('Location: index.php?url=books/create');
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $categoryId      = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
        $title           = trim($_POST['title'] ?? '');
        $author          = trim($_POST['author'] ?? '');
        $publisher       = trim($_POST['publisher'] ?? '');
        $publicationYear = !empty($_POST['publication_year']) ? (int) $_POST['publication_year'] : null;
        $isbn            = trim($_POST['isbn'] ?? '');
        $totalStock      = isset($_POST['total_stock']) ? (int) $_POST['total_stock'] : 0;
        $availableStock  = isset($_POST['available_stock']) && $_POST['available_stock'] !== '' 
            ? (int) $_POST['available_stock'] 
            : $totalStock;

        // 3. Validasi Data
        $errors = [];

        // Validasi Kategori
        if ($categoryId <= 0) {
            $errors['category_id'] = 'Kategori buku wajib dipilih.';
        } else {
            $cat = $this->categoryModel->getById($categoryId);
            if (!$cat) {
                $errors['category_id'] = 'Kategori yang dipilih tidak valid.';
            }
        }

        // Validasi Judul
        if (empty($title)) {
            $errors['title'] = 'Judul buku wajib diisi.';
        } elseif (mb_strlen($title) > 200) {
            $errors['title'] = 'Judul buku maksimal 200 karakter.';
        }

        // Validasi Pengarang
        if (empty($author)) {
            $errors['author'] = 'Nama pengarang/penulis wajib diisi.';
        } elseif (mb_strlen($author) > 100) {
            $errors['author'] = 'Nama pengarang maksimal 100 karakter.';
        }

        // Validasi Penerbit
        if (!empty($publisher) && mb_strlen($publisher) > 100) {
            $errors['publisher'] = 'Nama penerbit maksimal 100 karakter.';
        }

        // Validasi Tahun Terbit
        $currentYear = (int) date('Y');
        if ($publicationYear !== null) {
            if ($publicationYear < 1000 || $publicationYear > ($currentYear + 1)) {
                $errors['publication_year'] = "Tahun terbit tidak valid (antara 1000 dan " . ($currentYear + 1) . ").";
            }
        }

        // Validasi ISBN (Keunikan)
        if (!empty($isbn)) {
            if (mb_strlen($isbn) > 20) {
                $errors['isbn'] = 'Nomor ISBN maksimal 20 karakter.';
            } elseif ($this->bookModel->isbnExists($isbn)) {
                $errors['isbn'] = 'Nomor ISBN tersebut sudah terdaftar pada buku lain.';
            }
        }

        // Validasi Stok
        if ($totalStock < 0) {
            $errors['total_stock'] = 'Total stok tidak boleh bernilai negatif.';
        }

        if ($availableStock < 0) {
            $errors['available_stock'] = 'Stok tersedia tidak boleh bernilai negatif.';
        } elseif ($availableStock > $totalStock) {
            $errors['available_stock'] = 'Stok tersedia tidak boleh melebihi total stok buku.';
        }

        // Jika terdapat error validasi, redirect kembali ke form
        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=books/create');
            exit;
        }

        // 4. Eksekusi Simpan Data
        $data = [
            'category_id'      => $categoryId,
            'title'            => $title,
            'author'           => $author,
            'publisher'        => !empty($publisher) ? $publisher : null,
            'publication_year' => $publicationYear,
            'isbn'             => !empty($isbn) ? $isbn : null,
            'total_stock'      => $totalStock,
            'available_stock'  => $availableStock,
        ];

        if ($this->bookModel->create($data)) {
            $_SESSION['success'] = 'Buku baru "' . $title . '" berhasil ditambahkan ke katalog.';
            header('Location: index.php?url=books');
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat menyimpan data buku.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=books/create');
        }
        exit;
    }

    /**
     * Menampilkan formulir edit buku (Edit Form)
     */
    public function edit(): void
    {
        $this->checkAuth();
        $this->ensureCsrfToken();

        $id = !empty($_GET['id']) ? (int) $_GET['id'] : 0;
        $book = $this->bookModel->getById($id);

        if (!$book) {
            $_SESSION['error'] = 'Data buku tidak ditemukan atau telah dihapus.';
            header('Location: index.php?url=books');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $errors     = $_SESSION['errors'] ?? [];
        $old        = $_SESSION['old_input'] ?? [];
        $csrfToken  = $_SESSION['csrf_token'];
        $adminName  = $_SESSION['nama_lengkap'] ?? $_SESSION['name'] ?? 'Administrator';

        unset($_SESSION['errors'], $_SESSION['old_input']);

        require_once __DIR__ . '/../views/books/edit.php';
    }

    /**
     * Memperbarui data buku di database (Update / POST)
     */
    public function update(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=books');
            exit;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $existingBook = $this->bookModel->getById($id);

        if (!$existingBook) {
            $_SESSION['error'] = 'Data buku tidak ditemukan.';
            header('Location: index.php?url=books');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=books/edit&id=' . $id);
            exit;
        }

        // 2. Sanitasi & Pengambilan Data Input
        $categoryId      = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
        $title           = trim($_POST['title'] ?? '');
        $author          = trim($_POST['author'] ?? '');
        $publisher       = trim($_POST['publisher'] ?? '');
        $publicationYear = !empty($_POST['publication_year']) ? (int) $_POST['publication_year'] : null;
        $isbn            = trim($_POST['isbn'] ?? '');
        $totalStock      = isset($_POST['total_stock']) ? (int) $_POST['total_stock'] : 0;
        $availableStock  = isset($_POST['available_stock']) && $_POST['available_stock'] !== '' 
            ? (int) $_POST['available_stock'] 
            : $totalStock;

        // 3. Validasi Input
        $errors = [];

        if ($categoryId <= 0) {
            $errors['category_id'] = 'Kategori buku wajib dipilih.';
        } else {
            $cat = $this->categoryModel->getById($categoryId);
            if (!$cat) {
                $errors['category_id'] = 'Kategori yang dipilih tidak valid.';
            }
        }

        if (empty($title)) {
            $errors['title'] = 'Judul buku wajib diisi.';
        } elseif (mb_strlen($title) > 200) {
            $errors['title'] = 'Judul buku maksimal 200 karakter.';
        }

        if (empty($author)) {
            $errors['author'] = 'Nama pengarang/penulis wajib diisi.';
        } elseif (mb_strlen($author) > 100) {
            $errors['author'] = 'Nama pengarang maksimal 100 karakter.';
        }

        if (!empty($publisher) && mb_strlen($publisher) > 100) {
            $errors['publisher'] = 'Nama penerbit maksimal 100 karakter.';
        }

        $currentYear = (int) date('Y');
        if ($publicationYear !== null) {
            if ($publicationYear < 1000 || $publicationYear > ($currentYear + 1)) {
                $errors['publication_year'] = "Tahun terbit tidak valid (antara 1000 dan " . ($currentYear + 1) . ").";
            }
        }

        // Validasi ISBN Unik (kecuali untuk buku ini sendiri)
        if (!empty($isbn)) {
            if (mb_strlen($isbn) > 20) {
                $errors['isbn'] = 'Nomor ISBN maksimal 20 karakter.';
            } elseif ($this->bookModel->isbnExists($isbn, $id)) {
                $errors['isbn'] = 'Nomor ISBN tersebut sudah digunakan oleh buku lain.';
            }
        }

        if ($totalStock < 0) {
            $errors['total_stock'] = 'Total stok tidak boleh bernilai negatif.';
        }

        if ($availableStock < 0) {
            $errors['available_stock'] = 'Stok tersedia tidak boleh bernilai negatif.';
        } elseif ($availableStock > $totalStock) {
            $errors['available_stock'] = 'Stok tersedia tidak boleh melebihi total stok buku.';
        }

        if (!empty($errors)) {
            $_SESSION['errors']    = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=books/edit&id=' . $id);
            exit;
        }

        // 4. Eksekusi Update Data
        $data = [
            'category_id'      => $categoryId,
            'title'            => $title,
            'author'           => $author,
            'publisher'        => !empty($publisher) ? $publisher : null,
            'publication_year' => $publicationYear,
            'isbn'             => !empty($isbn) ? $isbn : null,
            'total_stock'      => $totalStock,
            'available_stock'  => $availableStock,
        ];

        if ($this->bookModel->update($id, $data)) {
            $_SESSION['success'] = 'Data buku "' . $title . '" berhasil diperbarui.';
            header('Location: index.php?url=books');
        } else {
            $_SESSION['error'] = 'Terjadi kesalahan sistem saat memperbarui data buku.';
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?url=books/edit&id=' . $id);
        }
        exit;
    }

    /**
     * Menghapus data buku dari database (Delete / POST)
     * Menjaga integritas foreign key dari tabel loans
     */
    public function delete(): void
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=books');
            exit;
        }

        // 1. Validasi CSRF Token
        if (!$this->validateCsrf($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Sesi keamanan tidak valid atau telah kedaluwarsa.';
            header('Location: index.php?url=books');
            exit;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $book = $this->bookModel->getById($id);

        if (!$book) {
            $_SESSION['error'] = 'Data buku tidak ditemukan atau telah dihapus sebelumnya.';
            header('Location: index.php?url=books');
            exit;
        }

        // 2. Validasi Foreign Key Relasi: Cek riwayat peminjaman
        if ($this->bookModel->hasLoans($id)) {
            $_SESSION['error'] = 'Buku "' . $book['title'] . '" tidak dapat dihapus karena tercatat dalam data peminjaman perpustakaan.';
            header('Location: index.php?url=books');
            exit;
        }

        // 3. Eksekusi Hapus Buku
        if ($this->bookModel->delete($id)) {
            $_SESSION['success'] = 'Buku "' . $book['title'] . '" berhasil dihapus dari sistem.';
        } else {
            $_SESSION['error'] = 'Gagal menghapus buku dari sistem.';
        }

        header('Location: index.php?url=books');
        exit;
    }
}

