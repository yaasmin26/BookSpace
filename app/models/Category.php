<?php

require_once __DIR__ . '/../../config/database.php';

class Category
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Mengambil seluruh data kategori dari database
     * Dilengkapi jumlah buku yang terhubung pada setiap kategori
     *
     * @return array Daftar seluruh kategori
     */
    public function getAll(): array
    {
        try {
            $sql = "SELECT c.*, 
                           (SELECT COUNT(*) FROM books b WHERE b.category_id = c.id) AS total_books
                    FROM categories c 
                    ORDER BY c.category_name ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Category::getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil satu data kategori berdasarkan ID
     *
     * @param int $id
     * @return array|false Data kategori jika ditemukan, false jika tidak atau terjadi error
     */
    public function getById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Category::getById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menambahkan data kategori baru ke database
     *
     * @param array $data Data kategori ['category_name' => string, 'description' => ?string]
     * @return bool True jika berhasil, false jika gagal
     */
    public function create(array $data): bool
    {
        try {
            $sql = "INSERT INTO categories (category_name, description) 
                    VALUES (:category_name, :description)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':category_name' => trim($data['category_name'] ?? ''),
                ':description'   => !empty($data['description']) ? trim($data['description']) : null,
            ]);
        } catch (PDOException $e) {
            error_log("Category::create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui data kategori berdasarkan ID
     *
     * @param int $id
     * @param array $data Data kategori ['category_name' => string, 'description' => ?string]
     * @return bool True jika berhasil, false jika gagal
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE categories 
                    SET category_name = :category_name, 
                        description = :description 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'            => $id,
                ':category_name' => trim($data['category_name'] ?? ''),
                ':description'   => !empty($data['description']) ? trim($data['description']) : null,
            ]);
        } catch (PDOException $e) {
            error_log("Category::update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus data kategori berdasarkan ID
     * Memastikan data tidak dihapus jika masih digunakan oleh relasi buku (fk_books_category)
     *
     * @param int $id
     * @return bool True jika berhasil dihapus, false jika gagal atau masih berelasi
     */
    public function delete(int $id): bool
    {
        try {
            // Validasi integritas foreign key: Cegah penghapusan jika kategori masih memiliki buku
            if ($this->hasBooks($id)) {
                error_log("Category::delete Warning: Kategori ID {$id} tidak dapat dihapus karena masih digunakan oleh buku.");
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Category::delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memeriksa apakah kategori masih digunakan oleh tabel books
     *
     * @param int $id
     * @return bool True jika ada buku yang terikat, false jika kosong
     */
    public function hasBooks(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM books WHERE category_id = :id");
            $stmt->execute([':id' => $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Category::hasBooks Error: " . $e->getMessage());
            // Asumsikan true untuk melindungi integritas data jika query pengecekan gagal
            return true;
        }
    }

    /**
     * Memeriksa apakah nama kategori sudah digunakan (untuk validasi kolom UNIQUE)
     *
     * @param string $categoryName
     * @param int|null $excludeId ID kategori yang diabaikan (digunakan saat edit)
     * @return bool True jika nama sudah ada, false jika belum ada
     */
    public function nameExists(string $categoryName, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM categories WHERE category_name = :category_name";
            $params = [':category_name' => trim($categoryName)];

            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Category::nameExists Error: " . $e->getMessage());
            return false;
        }
    }
}

