<?php

require_once __DIR__ . '/../../config/database.php';

class Loan
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Mengambil seluruh daftar transaksi peminjaman buku
     * Dilengkapi informasi relasi dari tabel members, books, dan users (petugas yang mencatat)
     * Mendukung filter status transaksi, pencarian nama anggota, judul buku, dan rentang tanggal
     *
     * @param string|null $status Filter status ('borrowed', 'returned', atau null untuk semua)
     * @param string|null $keyword Kata kunci pencarian umum (nama anggota, kode anggota, judul buku, ISBN)
     * @param string|null $startDate Filter batas awal tanggal peminjaman (YYYY-MM-DD)
     * @param string|null $endDate Filter batas akhir tanggal peminjaman (YYYY-MM-DD)
     * @param string|null $memberQuery Kata kunci pencarian spesifik nama atau kode anggota
     * @param string|null $bookQuery Kata kunci pencarian spesifik judul buku atau ISBN
     * @return array Daftar riwayat transaksi peminjaman
     */
    public function getAll(
        ?string $status = null, 
        ?string $keyword = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?string $memberQuery = null,
        ?string $bookQuery = null
    ): array {
        try {
            $sql = "SELECT l.*, 
                           m.name AS member_name, 
                           m.member_code, 
                           m.phone AS member_phone,
                           m.email AS member_email,
                           b.title AS book_title, 
                           b.isbn AS book_isbn,
                           b.author AS book_author,
                           u.name AS admin_name
                    FROM loans l
                    JOIN members m ON l.member_id = m.id
                    JOIN books b ON l.book_id = b.id
                    JOIN users u ON l.user_id = u.id";

            $conditions = [];
            $params = [];

            // 1. Filter status peminjaman (borrowed / returned)
            if (!empty($status) && in_array($status, ['borrowed', 'returned'], true)) {
                $conditions[] = "l.status = :status";
                $params[':status'] = $status;
            }

            // 2. Pencarian umum (keyword)
            if (!empty($keyword)) {
                $conditions[] = "(m.name LIKE :kw_name 
                              OR m.member_code LIKE :kw_code 
                              OR b.title LIKE :kw_title 
                              OR b.isbn LIKE :kw_isbn)";
                $wildcard = '%' . trim($keyword) . '%';
                $params[':kw_name']  = $wildcard;
                $params[':kw_code']  = $wildcard;
                $params[':kw_title'] = $wildcard;
                $params[':kw_isbn']  = $wildcard;
            }

            // 3. Pencarian spesifik nama atau kode anggota
            if (!empty($memberQuery)) {
                $conditions[] = "(m.name LIKE :m_name OR m.member_code LIKE :m_code)";
                $memberWildcard = '%' . trim($memberQuery) . '%';
                $params[':m_name'] = $memberWildcard;
                $params[':m_code'] = $memberWildcard;
            }

            // 4. Pencarian spesifik judul buku atau ISBN
            if (!empty($bookQuery)) {
                $conditions[] = "(b.title LIKE :b_title OR b.isbn LIKE :b_isbn)";
                $bookWildcard = '%' . trim($bookQuery) . '%';
                $params[':b_title'] = $bookWildcard;
                $params[':b_isbn']  = $bookWildcard;
            }

            // 5. Filter tanggal peminjaman (start_date & end_date)
            if (!empty($startDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                $conditions[] = "l.loan_date >= :start_date";
                $params[':start_date'] = $startDate;
            }

            if (!empty($endDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $conditions[] = "l.loan_date <= :end_date";
                $params[':end_date'] = $endDate;
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " ORDER BY l.id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Loan::getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil satu data transaksi peminjaman berdasarkan ID
     * Dilengkapi informasi lengkap buku, anggota, dan petugas yang memproses
     *
     * @param int $id ID transaksi peminjaman
     * @return array|false Data transaksi jika ditemukan, false jika tidak atau terjadi error
     */
    public function getById(int $id)
    {
        try {
            $sql = "SELECT l.*, 
                           m.name AS member_name, 
                           m.member_code, 
                           m.phone AS member_phone,
                           m.email AS member_email,
                           b.title AS book_title, 
                           b.isbn AS book_isbn,
                           b.author AS book_author,
                           b.available_stock,
                           b.total_stock,
                           u.name AS admin_name
                    FROM loans l
                    JOIN members m ON l.member_id = m.id
                    JOIN books b ON l.book_id = b.id
                    JOIN users u ON l.user_id = u.id
                    WHERE l.id = :id
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Loan::getById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menyimpan transaksi peminjaman buku baru
     * Menggunakan Database Transaction dan Row Locking (FOR UPDATE) untuk menjamin:
     * 1. Validasi ketersediaan stok buku (available_stock > 0).
     * 2. Insert data transaksi ke tabel loans.
     * 3. Pengurangan available_stock buku sebanyak 1 secara atomik.
     *
     * @param array $data Data transaksi:
     *                    - member_id (int)
     *                    - book_id (int)
     *                    - user_id (int)
     *                    - loan_date (string Y-m-d)
     *                    - due_date (string Y-m-d)
     * @return bool True jika transaksi berhasil disimpan dan stok berkurang, false jika gagal
     */
    public function create(array $data): bool
    {
        if (empty($data['member_id']) || empty($data['book_id']) || empty($data['user_id'])) {
            error_log("Loan::create Error: Parameter member_id, book_id, atau user_id tidak boleh kosong.");
            return false;
        }

        $memberId = (int) $data['member_id'];
        $bookId   = (int) $data['book_id'];
        $userId   = (int) $data['user_id'];
        $loanDate = !empty($data['loan_date']) ? trim($data['loan_date']) : date('Y-m-d');
        $dueDate  = !empty($data['due_date']) ? trim($data['due_date']) : date('Y-m-d', strtotime('+7 days'));

        try {
            // Memulai transaksi database
            $this->db->beginTransaction();

            // 1. Kunci baris buku dan validasi ketersediaan stok buku
            $stmtBook = $this->db->prepare("SELECT id, available_stock, total_stock FROM books WHERE id = :id FOR UPDATE");
            $stmtBook->execute([':id' => $bookId]);
            $book = $stmtBook->fetch();

            if (!$book) {
                error_log("Loan::create Error: Buku dengan ID {$bookId} tidak ditemukan.");
                $this->db->rollBack();
                return false;
            }

            if ((int) $book['available_stock'] <= 0) {
                error_log("Loan::create Error: Stok buku ID {$bookId} habis (available_stock <= 0). Peminjaman dibatalkan.");
                $this->db->rollBack();
                return false;
            }

            // 2. Simpan record transaksi ke tabel loans
            $sqlLoan = "INSERT INTO loans (
                            member_id, 
                            book_id, 
                            user_id, 
                            loan_date, 
                            due_date, 
                            status
                        ) VALUES (
                            :member_id, 
                            :book_id, 
                            :user_id, 
                            :loan_date, 
                            :due_date, 
                            'borrowed'
                        )";
            $stmtLoan = $this->db->prepare($sqlLoan);
            $loanInserted = $stmtLoan->execute([
                ':member_id' => $memberId,
                ':book_id'   => $bookId,
                ':user_id'   => $userId,
                ':loan_date' => $loanDate,
                ':due_date'  => $dueDate,
            ]);

            if (!$loanInserted) {
                $this->db->rollBack();
                return false;
            }

            // 3. Kurangi stok buku yang tersedia (available_stock) sebanyak 1 secara atomik
            $stmtUpdateStock = $this->db->prepare("UPDATE books 
                                                    SET available_stock = available_stock - 1 
                                                    WHERE id = :id AND available_stock > 0");
            $stmtUpdateStock->execute([':id' => $bookId]);

            if ($stmtUpdateStock->rowCount() === 0) {
                // Batalkan jika pengurangan stok gagal
                $this->db->rollBack();
                return false;
            }

            // Komit transaksi database jika seluruh langkah berhasil
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Loan::create Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memproses pengembalian buku
     * Menggunakan Database Transaction dan Row Locking (FOR UPDATE) untuk menjamin:
     * 1. Transaksi valid dan berstatus 'borrowed' (buku benar-benar sedang dipinjam).
     * 2. Status diubah menjadi 'returned' dan tanggal return_date dicatat hari ini.
     * 3. Stok available_stock buku bertambah 1 HANYA SATU KALI (idempotent).
     *
     * @param int $id ID transaksi peminjaman
     * @return bool True jika pengembalian berhasil diproses, false jika gagal atau sudah pernah dikembalikan
     */
    public function returnBook(int $id): bool
    {
        try {
            // Memulai transaksi database
            $this->db->beginTransaction();

            // 1. Kunci baris transaksi peminjaman dan periksa status saat ini
            $stmtLoan = $this->db->prepare("SELECT id, book_id, status FROM loans WHERE id = :id FOR UPDATE");
            $stmtLoan->execute([':id' => $id]);
            $loan = $stmtLoan->fetch();

            if (!$loan) {
                error_log("Loan::returnBook Error: Transaksi pinjaman ID {$id} tidak ditemukan.");
                $this->db->rollBack();
                return false;
            }

            // Validasi: Cegah penambahan stok ganda jika transaksi sudah berstatus 'returned'
            if ($loan['status'] !== 'borrowed') {
                error_log("Loan::returnBook Warning: Transaksi pinjaman ID {$id} sudah berstatus '{$loan['status']}'. Stok tidak ditambah ulang.");
                $this->db->rollBack();
                return false;
            }

            $bookId = (int) $loan['book_id'];
            $returnDate = date('Y-m-d');

            // 2. Perbarui status peminjaman menjadi 'returned' dan isi return_date
            $stmtUpdateLoan = $this->db->prepare("UPDATE loans 
                                                  SET status = 'returned', 
                                                      return_date = :return_date 
                                                  WHERE id = :id AND status = 'borrowed'");
            $stmtUpdateLoan->execute([
                ':id'          => $id,
                ':return_date' => $returnDate
            ]);

            if ($stmtUpdateLoan->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            // 3. Tambahkan available_stock buku sebanyak 1 (tidak boleh melebihi total_stock)
            $stmtUpdateStock = $this->db->prepare("UPDATE books 
                                                    SET available_stock = available_stock + 1 
                                                    WHERE id = :id AND available_stock < total_stock");
            $stmtUpdateStock->execute([':id' => $bookId]);

            if ($stmtUpdateStock->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            // Komit transaksi jika seluruh operasi berhasil
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Loan::returnBook Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui status transaksi peminjaman
     * Jika status diubah menjadi 'returned', alur otomatis didelegasikan ke returnBook()
     * agar pencatatan return_date dan penyesuaian stok buku berjalan secara konsisten dan atomik.
     *
     * @param int $id ID transaksi peminjaman
     * @param string $status Status baru ('borrowed' atau 'returned')
     * @return bool True jika berhasil diperbarui, false jika gagal
     */
    public function updateStatus(int $id, string $status): bool
    {
        $status = strtolower(trim($status));
        if (!in_array($status, ['borrowed', 'returned'], true)) {
            error_log("Loan::updateStatus Error: Status '{$status}' tidak valid.");
            return false;
        }

        // Jika status yang diinginkan adalah 'returned', alihkan ke returnBook
        // untuk menjamin stok bertambah satu kali dan tanggal pengembalian tercatat
        if ($status === 'returned') {
            return $this->returnBook($id);
        }

        try {
            $stmt = $this->db->prepare("UPDATE loans SET status = :status WHERE id = :id");
            return $stmt->execute([
                ':id'     => $id,
                ':status' => $status
            ]);
        } catch (PDOException $e) {
            error_log("Loan::updateStatus Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghitung total transaksi peminjaman aktif yang belum dikembalikan
     *
     * @return int Jumlah pinjaman berstatus 'borrowed'
     */
    public function getActiveLoansCount(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM loans WHERE status = 'borrowed'");
            return (int) ($stmt->fetchColumn() ?: 0);
        } catch (PDOException $e) {
            error_log("Loan::getActiveLoansCount Error: " . $e->getMessage());
            return 0;
        }
    }
}

