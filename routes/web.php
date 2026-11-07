<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/BookController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/MemberController.php';
require_once __DIR__ . '/../app/controllers/LoanController.php';
require_once __DIR__ . '/../app/controllers/ReturnController.php';
require_once __DIR__ . '/../app/controllers/UserDashboardController.php';
require_once __DIR__ . '/../app/controllers/CatalogController.php';
require_once __DIR__ . '/../app/controllers/UserLoanController.php';

/**
 * Mengurai dan mendapatkan URI path yang diminta
 * Kompatibel dengan lingkungan XAMPP (Query String, PATH_INFO, & Clean URL)
 *
 * @return string
 */
if (!function_exists('requireLogin')) {
    function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin(): void
    {
        requireLogin();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman atau fitur administrator.';
            header('Location: index.php?url=user/dashboard');
            exit;
        }
    }
}

if (!function_exists('resolveRequestUri')) {
    function resolveRequestUri(): string
    {
        // 1. Dukungan query string ?url=... (Standar XAMPP bawaan tanpa mod_rewrite)
        if (isset($_GET['url'])) {
            $path = '/' . trim($_GET['url'], '/');
            return $path === '/' ? '/' : rtrim($path, '/');
        }

        // 2. Dukungan PATH_INFO (misal: index.php/login)
        if (!empty($_SERVER['PATH_INFO'])) {
            $path = '/' . trim($_SERVER['PATH_INFO'], '/');
            return $path === '/' ? '/' : rtrim($path, '/');
        }

        // 3. Dukungan REQUEST_URI (Clean URL / mod_rewrite aktif)
        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $scriptDir  = dirname($_SERVER['SCRIPT_NAME'] ?? '');

            if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($requestUri, $scriptDir) === 0) {
                $requestUri = substr($requestUri, strlen($scriptDir));
            }

            $requestUri = preg_replace('#^/index\.php#i', '', $requestUri);
            $path = '/' . trim($requestUri, '/');
            return $path === '/' ? '/' : rtrim($path, '/');
        }

        return '/';
    }
}

$requestUri = resolveRequestUri();
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Tabel Rute Aplikasi BookSpace
switch ($requestUri) {
        // Route: GET /
    case '/':
        if ($requestMethod === 'GET') {
            if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
                if (($_SESSION['role'] ?? '') === 'admin') {
                    header('Location: index.php?url=dashboard');
                } else {
                    header('Location: index.php?url=user/dashboard');
                }
                exit;
            }
            (new AuthController())->login();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /register & POST /register (Sign Up Pengguna Baru)
    case '/register':
        if ($requestMethod === 'GET') {
            (new AuthController())->register();
        } elseif ($requestMethod === 'POST') {
            (new AuthController())->processRegister();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /user/dashboard & /user-dashboard (Dashboard Pengguna Biasa)
    case '/user/dashboard':
    case '/user-dashboard':
        if ($requestMethod === 'GET') {
            requireLogin();
            (new UserDashboardController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /catalog & /katalog (Katalog Buku Pengguna)
    case '/catalog':
    case '/katalog':
        if ($requestMethod === 'GET') {
            requireLogin();
            (new CatalogController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /catalog/detail & /book/detail (Detail Buku Pengguna)
    case '/catalog/detail':
    case '/book/detail':
        if ($requestMethod === 'GET') {
            requireLogin();
            (new CatalogController())->detail();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /catalog/borrow & /loans/borrow & /borrow (Peminjaman Online Mandiri)
    case '/catalog/borrow':
    case '/loans/borrow':
    case '/borrow':
        if ($requestMethod === 'POST') {
            requireLogin();
            (new CatalogController())->borrow();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /my-loans & /peminjaman-saya (Buku Sedang Dipinjam)
    case '/my-loans':
    case '/peminjaman-saya':
        if ($requestMethod === 'GET') {
            requireLogin();
            (new UserLoanController())->myLoans();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /my-loans/history & /riwayat-peminjaman (Riwayat Peminjaman Lengkap)
    case '/my-loans/history':
    case '/riwayat-peminjaman':
        if ($requestMethod === 'GET') {
            requireLogin();
            (new UserLoanController())->history();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /login & POST /login
    case '/login':
        if ($requestMethod === 'GET') {
            (new AuthController())->login();
        } elseif ($requestMethod === 'POST') {
            (new AuthController())->authenticate();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

        // Route: GET /dashboard (Khusus Admin Perpustakaan)
    case '/dashboard':
        if ($requestMethod === 'GET') {
            requireAdmin();
            (new DashboardController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /logout
    case '/logout':
        if ($requestMethod === 'POST') {
            (new AuthController())->logout();
        } else {
            // Validasi metode HTTP: logout hanya diizinkan via POST
            http_response_code(405);
            header('Location: index.php?url=dashboard');
            exit;
        }
        break;

    // ==========================================
    // Rute CRUD Buku (Tanggung Jawab Anggota 2)
    // ==========================================
    // Route: GET /books (Dukungan Admin CRUD & User Katalog)
    case '/books':
        if ($requestMethod === 'GET') {
            requireLogin();
            if (($_SESSION['role'] ?? '') === 'admin') {
                (new BookController())->index();
            } else {
                (new CatalogController())->index();
            }
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /books/create (Form) & POST /books/create (Simpan Buku)
    case '/books/create':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new BookController())->create();
        } elseif ($requestMethod === 'POST') {
            (new BookController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /books/edit (Form) & POST /books/edit (Update Buku)
    case '/books/edit':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new BookController())->edit();
        } elseif ($requestMethod === 'POST') {
            (new BookController())->update();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /books/delete (Hapus Buku)
    case '/books/delete':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new BookController())->delete();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Dukungan alias: POST /books/store & POST /books/update
    case '/books/store':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new BookController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    case '/books/update':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new BookController())->update();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // ==========================================
    // Rute CRUD Kategori (Tanggung Jawab Anggota 2)
    // ==========================================
    // Route: GET /categories (Daftar Kategori)
    case '/categories':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new CategoryController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /categories/create (Form) & POST /categories/create (Simpan Kategori)
    case '/categories/create':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new CategoryController())->create();
        } elseif ($requestMethod === 'POST') {
            (new CategoryController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /categories/edit (Form) & POST /categories/edit (Update Kategori)
    case '/categories/edit':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new CategoryController())->edit();
        } elseif ($requestMethod === 'POST') {
            (new CategoryController())->update();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /categories/delete (Hapus Kategori)
    case '/categories/delete':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new CategoryController())->delete();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Dukungan alias: POST /categories/store & POST /categories/update
    case '/categories/store':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new CategoryController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    case '/categories/update':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new CategoryController())->update();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // ==========================================
    // Rute CRUD Anggota (Tanggung Jawab Anggota 2)
    // ==========================================
    // Route: GET /members (Daftar Anggota)
    case '/members':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new MemberController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /members/create (Form) & POST /members/create (Simpan Anggota)
    case '/members/create':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new MemberController())->create();
        } elseif ($requestMethod === 'POST') {
            (new MemberController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /members/edit (Form) & POST /members/edit (Update Anggota)
    case '/members/edit':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new MemberController())->edit();
        } elseif ($requestMethod === 'POST') {
            (new MemberController())->update();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /members/delete (Hapus Anggota)
    case '/members/delete':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new MemberController())->delete();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Dukungan alias: POST /members/store & POST /members/update
    case '/members/store':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new MemberController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    case '/members/update':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new MemberController())->update();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // ===============================================
    // Rute Sirkulasi Peminjaman (Tanggung Jawab Anggota 3)
    // ===============================================
    // Route: GET /loans (Daftar Transaksi Peminjaman)
    case '/loans':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new LoanController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /loans/create & POST /loans/create (Form & Simpan Peminjaman)
    case '/loans/create':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new LoanController())->create();
        } elseif ($requestMethod === 'POST') {
            (new LoanController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /loans/store (Simpan Peminjaman)
    case '/loans/store':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new LoanController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /loans/detail (Detail Transaksi)
    case '/loans/detail':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new LoanController())->detail();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /loans/return (Proses Pengembalian Buku)
    case '/loans/return':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new LoanController())->returnBook();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // ===============================================
    // Rute Layanan Pengembalian Buku
    // ===============================================
    // Route: GET /returns & POST /returns
    case '/returns':
        requireAdmin();
        if ($requestMethod === 'GET') {
            (new ReturnController())->index();
        } elseif ($requestMethod === 'POST') {
            (new ReturnController())->process();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /returns/process
    case '/returns/process':
        requireAdmin();
        if ($requestMethod === 'POST') {
            (new ReturnController())->process();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Rute Tidak Ditemukan (404)
    default:
        http_response_code(404);
        require_once __DIR__ . '/../app/views/errors/404.php';
        break;
}
