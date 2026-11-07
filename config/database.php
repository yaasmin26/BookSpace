<?php

class Database
{
    private static string $host = 'localhost';
    private static string $db_name = 'bookspace';
    private static string $username = 'root';
    private static string $password = '';
    private static string $charset = 'utf8mb4';
    
    private static ?PDO $conn = null;

    /**
     * Mendapatkan koneksi database PDO tunggal (singleton)
     * Dapat digunakan oleh seluruh Model: Database::getConnection()
     *
     * @return PDO|null
     */
    public static function getConnection(): ?PDO
    {
        if (self::$conn === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$conn = new PDO($dsn, self::$username, self::$password, $options);
            } catch (PDOException $e) {
                // Catat detail error ke log server tanpa mengekspos informasi sensitif ke pengguna
                error_log("Database Error: " . $e->getMessage());
                die("Terjadi kesalahan pada koneksi sistem. Silakan hubungi administrator.");
            }
        }

        return self::$conn;
    }
}
