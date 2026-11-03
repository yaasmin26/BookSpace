<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | BookSpace</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
        }
        .error-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 3rem 2rem;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
        }
        .error-code {
            font-size: 4.5rem;
            font-weight: 900;
            color: #2563eb;
            line-height: 1;
            margin-bottom: 0.75rem;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        .error-desc {
            font-size: 0.925rem;
            color: #64748b;
            margin-bottom: 1.75rem;
            line-height: 1.5;
        }
        .btn-home {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 0.75rem 1.4rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.925rem;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .btn-home:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">Maaf, tautan atau halaman yang Anda akses tidak ditemukan pada sistem BookSpace.</p>
        <a href="index.php?url=login" class="btn-home">Kembali ke Beranda</a>
    </div>
</body>
</html>

