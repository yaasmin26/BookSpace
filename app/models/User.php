<?php

require_once __DIR__ . '/../../config/database.php';

class User
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Mencari pengguna berdasarkan email
     *
     * @param string $email
     * @return array|false Data user jika ditemukan, false jika tidak atau terjadi error
     */
    public function findByEmail(string $email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::findByEmail Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari pengguna berdasarkan ID
     *
     * @param int $id
     * @return array|false Data user jika ditemukan, false jika tidak atau terjadi error
     */
    public function findById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::findById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memeriksa apakah email sudah terdaftar di database
     *
     * @param string $email
     * @return bool True jika email sudah ada, false jika belum ada atau terjadi error
     */
    public function emailExists(string $email): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("User::emailExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari pengguna berdasarkan nama/username (opsional sebagai fallback)
     *
     * @param string $username
     * @return array|false Data user jika ditemukan, false jika tidak atau terjadi error
     */
    public function findByUsername(string $username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE name = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::findByUsername Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifikasi kecocokan password plaintext terhadap hash BCRYPT
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Menghasilkan hash password yang aman menggunakan BCRYPT
     * Menjamin password tidak pernah disimpan atau diproses dalam bentuk plaintext
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
