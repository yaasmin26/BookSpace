<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

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

    // Rute Tidak Ditemukan (404)
    default:
        http_response_code(404);
        require_once __DIR__ . '/../app/views/errors/404.php';
        break;
}
