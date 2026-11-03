<?php

require_once __DIR__ . '/../../config/database.php';

class Book
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Mengambil seluruh data buku beserta informasi nama kategorinya
     * Mendukung pencarian kata kunci dan filter berdasarkan kategori
     *
     * @param string|null $keyword Kata kunci pencarian (judul, penulis, penerbit, isbn)
     * @param int|null $categoryId Filter ID kategori
     * @return array Daftar buku
     */
    public function getAll(?string $keyword = null, ?int $categoryId = null): array
    {
        try {
            $sql = "SELECT b.*, c.category_name 
                    FROM books b 
                    JOIN categories c ON b.category_id = c.id";
            $conditions = [];
            $params = [];

            if (!empty($keyword)) {
                $conditions[] = "(b.title LIKE :kw_title 
                              OR b.author LIKE :kw_author 
                              OR b.publisher LIKE :kw_publisher 
                              OR b.isbn LIKE :kw_isbn)";
                $wildcard = '%' . trim($keyword) . '%';
                $params[':kw_title']     = $wildcard;
                $params[':kw_author']    = $wildcard;
                $params[':kw_publisher'] = $wildcard;
                $params[':kw_isbn']      = $wildcard;
            }

            if (!empty($categoryId)) {
                $conditions[] = "b.category_id = :category_id";
                $params[':category_id'] = $categoryId;
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " ORDER BY b.id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Book::getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil satu data buku berdasarkan ID beserta nama kategori
     *
     * @param int $id
     * @return array|false Data buku jika ditemukan, false jika tidak atau terjadi error
     */
    public function getById(int $id)
    {
        try {
            $sql = "SELECT b.*, c.category_name 
                    FROM books b 
                    JOIN categories c ON b.category_id = c.id 
                    WHERE b.id = :id 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Book::getById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menambahkan data buku baru ke database
     *
     * @param array $data Data buku:
     *                    - category_id (int)
     *                    - title (string)
     *                    - author (string)
     *                    - publisher (?string)
     *                    - publication_year (?int)
     *                    - isbn (?string)
     *                    - total_stock (int)
     *                    - available_stock (?int)
     * @return bool True jika berhasil, false jika gagal
     */
    public function create(array $data): bool
    {
        try {
            $totalStock = max(0, (int) ($data['total_stock'] ?? 0));
            // Jika available_stock tidak diisi, otomatis samakan dengan total_stock
            $availableStock = isset($data['available_stock']) 
                ? max(0, min($totalStock, (int) $data['available_stock']))
                : $totalStock;

            $sql = "INSERT INTO books (
                        category_id, 
                        title, 
                        author, 
                        publisher, 
                        publication_year, 
                        isbn, 
                        total_stock, 
                        available_stock
                    ) VALUES (
                        :category_id, 
                        :title, 
                        :author, 
                        :publisher, 
                        :publication_year, 
                        :isbn, 
                        :total_stock, 
                        :available_stock
                    )";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':category_id'      => (int) $data['category_id'],
                ':title'            => trim($data['title'] ?? ''),
                ':author'           => trim($data['author'] ?? ''),
                ':publisher'        => !empty($data['publisher']) ? trim($data['publisher']) : null,
                ':publication_year' => !empty($data['publication_year']) ? (int) $data['publication_year'] : null,
                ':isbn'             => !empty($data['isbn']) ? trim($data['isbn']) : null,
                ':total_stock'      => $totalStock,
                ':available_stock'  => $availableStock,
            ]);
        } catch (PDOException $e) {
            error_log("Book::create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui data buku berdasarkan ID
     *
     * @param int $id
     * @param array $data Data buku yang diperbarui
     * @return bool True jika berhasil, false jika gagal
     */
    public function update(int $id, array $data): bool
    {
        try {
            $totalStock = max(0, (int) ($data['total_stock'] ?? 0));
            $availableStock = isset($data['available_stock']) 
                ? max(0, (int) $data['available_stock']) 
                : $totalStock;

            // Pastikan available_stock tidak melebihi total_stock
            if ($availableStock > $totalStock) {
                $availableStock = $totalStock;
            }

            $sql = "UPDATE books SET 
                        category_id = :category_id, 
                        title = :title, 
                        author = :author, 
                        publisher = :publisher, 
                        publication_year = :publication_year, 
                        isbn = :isbn, 
                        total_stock = :total_stock, 
                        available_stock = :available_stock 
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'               => $id,
                ':category_id'      => (int) $data['category_id'],
                ':title'            => trim($data['title'] ?? ''),
                ':author'           => trim($data['author'] ?? ''),
                ':publisher'        => !empty($data['publisher']) ? trim($data['publisher']) : null,
                ':publication_year' => !empty($data['publication_year']) ? (int) $data['publication_year'] : null,
                ':isbn'             => !empty($data['isbn']) ? trim($data['isbn']) : null,
                ':total_stock'      => $totalStock,
                ':available_stock'  => $availableStock,
            ]);
        } catch (PDOException $e) {
            error_log("Book::update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus data buku berdasarkan ID
     * Memastikan data tidak dihapus jika buku masih digunakan dalam riwayat peminjaman (fk_loans_book)
     *
     * @param int $id
     * @return bool True jika berhasil dihapus, false jika gagal atau masih memiliki riwayat peminjaman
     */
    public function delete(int $id): bool
    {
        try {
            // Validasi integritas foreign key: Cegah penghapusan jika buku memiliki transaksi di tabel loans
            if ($this->hasLoans($id)) {
                error_log("Book::delete Warning: Buku ID {$id} tidak dapat dihapus karena tercatat dalam riwayat peminjaman.");
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM books WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Book::delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memeriksa apakah buku tercatat dalam riwayat peminjaman di tabel loans
     *
     * @param int $id
     * @return bool True jika buku ada di tabel loans, false jika tidak
     */
    public function hasLoans(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM loans WHERE book_id = :id");
            $stmt->execute([':id' => $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Book::hasLoans Error: " . $e->getMessage());
            // Asumsikan true untuk melindungi integritas data relasi
            return true;
        }
    }

    /**
     * Memeriksa apakah buku sedang dipinjam (status = 'borrowed')
     *
     * @param int $id
     * @return bool True jika sedang dipinjam aktif, false jika tidak
     */
    public function hasActiveLoans(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM loans WHERE book_id = :id AND status = 'borrowed'");
            $stmt->execute([':id' => $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Book::hasActiveLoans Error: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Memeriksa apakah nomor ISBN sudah digunakan oleh buku lain (untuk validasi UNIQUE)
     *
     * @param string $isbn
     * @param int|null $excludeId ID buku yang diabaikan (digunakan saat edit)
     * @return bool True jika ISBN sudah digunakan, false jika belum
     */
    public function isbnExists(string $isbn, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM books WHERE isbn = :isbn";
            $params = [':isbn' => trim($isbn)];

            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Book::isbnExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui stok buku yang tersedia (available_stock)
     * Berguna untuk integrasi modul peminjaman dan pengembalian
     *
     * @param int $id
     * @param int $delta Perubahan stok (+1 untuk pengembalian, -1 untuk peminjaman)
     * @return bool
     */
    public function adjustAvailableStock(int $id, int $delta): bool
    {
        try {
            $sql = "UPDATE books 
                    SET available_stock = available_stock + :delta 
                    WHERE id = :id 
                      AND (available_stock + :delta) >= 0 
                      AND (available_stock + :delta) <= total_stock";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'    => $id,
                ':delta' => $delta
            ]);
        } catch (PDOException $e) {
            error_log("Book::adjustAvailableStock Error: " . $e->getMessage());
            return false;
        }
    }
}

