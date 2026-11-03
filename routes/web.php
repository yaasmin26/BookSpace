<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/BookController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/MemberController.php';
require_once __DIR__ . '/../app/controllers/LoanController.php';

/**
 * Mengurai dan mendapatkan URI path yang diminta
 * Kompatibel dengan lingkungan XAMPP (Query String, PATH_INFO, & Clean URL)
 *
 * @return string
 */
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

$requestUri = resolveRequestUri();
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Tabel Rute Aplikasi BookSpace
switch ($requestUri) {
    // Route: GET /
    case '/':
        if ($requestMethod === 'GET') {
            if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
                header('Location: index.php?url=dashboard');
                exit;
            }
            (new AuthController())->login();
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

    // Route: GET /dashboard (Dilindungi autentikasi session)
    case '/dashboard':
        if ($requestMethod === 'GET') {
            // Validasi Autentikasi: Cegah akses tanpa login
            if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
                header('Location: index.php?url=login');
                exit;
            }
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
    // Route: GET /books (Katalog Buku)
    case '/books':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'GET') {
            (new BookController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /books/create (Form) & POST /books/create (Simpan Buku)
    case '/books/create':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new BookController())->delete();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Dukungan alias: POST /books/store & POST /books/update
    case '/books/store':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new BookController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    case '/books/update':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'GET') {
            (new CategoryController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /categories/create (Form) & POST /categories/create (Simpan Kategori)
    case '/categories/create':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new CategoryController())->delete();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Dukungan alias: POST /categories/store & POST /categories/update
    case '/categories/store':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new CategoryController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    case '/categories/update':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'GET') {
            (new MemberController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /members/create (Form) & POST /members/create (Simpan Anggota)
    case '/members/create':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new MemberController())->delete();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Dukungan alias: POST /members/store & POST /members/update
    case '/members/store':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new MemberController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    case '/members/update':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'GET') {
            (new LoanController())->index();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /loans/create & POST /loans/create (Form & Simpan Peminjaman)
    case '/loans/create':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
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
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new LoanController())->store();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: GET /loans/detail (Detail Transaksi)
    case '/loans/detail':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'GET') {
            (new LoanController())->detail();
        } else {
            http_response_code(405);
            echo "405 Method Not Allowed";
        }
        break;

    // Route: POST /loans/return (Proses Pengembalian Buku)
    case '/loans/return':
        if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if ($requestMethod === 'POST') {
            (new LoanController())->returnBook();
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
