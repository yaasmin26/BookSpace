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
     * Mencari pengguna berdasarkan username
     *
     * @param string $username
     * @return array|false Data user jika ditemukan, false jika tidak atau terjadi error
     */
    public function findByUsername(string $username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::findByUsername Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mencari pengguna berdasarkan identifier (bisa username atau email)
     *
     * @param string $identifier
     * @return array|false Data user jika ditemukan, false jika tidak
     */
    public function findByIdentifier(string $identifier)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM users 
                WHERE email = :email OR username = :username 
                LIMIT 1
            ");
            $stmt->execute([
                ':email'    => $identifier,
                ':username' => $identifier,
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::findByIdentifier Error: " . $e->getMessage());
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
     * Memeriksa apakah username sudah terdaftar di database
     *
     * @param string $username
     * @return bool True jika username sudah ada, false jika belum ada atau terjadi error
     */
    public function usernameExists(string $username): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("User::usernameExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendaftarkan pengguna baru (Sign Up / Register)
     * Password selalu di-hash dengan algoritma BCRYPT, tidak pernah plaintext
     *
     * @param array $data ['name', 'username', 'email', 'password', 'role']
     * @return int|false ID user yang baru dibuat atau false jika gagal
     */
    public function create(array $data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (name, username, email, password, role)
                VALUES (:name, :username, :email, :password, :role)
            ");

            $role = $data['role'] ?? 'user';
            $hashedPassword = $this->hashPassword($data['password']);

            $success = $stmt->execute([
                ':name'     => trim($data['name']),
                ':username' => trim($data['username']),
                ':email'    => trim(strtolower($data['email'])),
                ':password' => $hashedPassword,
                ':role'     => $role,
            ]);

            if ($success) {
                return (int) $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("User::create Error: " . $e->getMessage());
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
