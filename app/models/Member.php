<?php

require_once __DIR__ . '/../../config/database.php';

class Member
{
    private ?PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Mengambil seluruh data anggota perpustakaan
     * Dilengkapi jumlah peminjaman aktif yang sedang berjalan
     * Mendukung pencarian kata kunci (nama, kode anggota, email, nomor telepon)
     *
     * @param string|null $keyword Kata kunci pencarian opsional
     * @return array Daftar seluruh anggota
     */
    public function getAll(?string $keyword = null): array
    {
        try {
            $sql = "SELECT m.*, 
                           (SELECT COUNT(*) 
                            FROM loans l 
                            WHERE l.member_id = m.id AND l.status = 'borrowed') AS active_loans_count
                    FROM members m";
            $params = [];

            if (!empty($keyword)) {
                $sql .= " WHERE (m.name LIKE :kw_name 
                             OR m.member_code LIKE :kw_code 
                             OR m.email LIKE :kw_email 
                             OR m.phone LIKE :kw_phone)";
                $wildcard = '%' . trim($keyword) . '%';
                $params[':kw_name']  = $wildcard;
                $params[':kw_code']  = $wildcard;
                $params[':kw_email'] = $wildcard;
                $params[':kw_phone'] = $wildcard;
            }

            $sql .= " ORDER BY m.id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Member::getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil satu data anggota berdasarkan ID
     *
     * @param int $id
     * @return array|false Data anggota jika ditemukan, false jika tidak atau terjadi error
     */
    public function getById(int $id)
    {
        try {
            $sql = "SELECT m.*, 
                           (SELECT COUNT(*) 
                            FROM loans l 
                            WHERE l.member_id = m.id AND l.status = 'borrowed') AS active_loans_count
                    FROM members m 
                    WHERE m.id = :id 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Member::getById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menambahkan data anggota baru ke database
     *
     * @param array $data Data anggota:
     *                    - member_code (string)
     *                    - name (string)
     *                    - email (?string)
     *                    - phone (?string)
     *                    - address (?string)
     * @return bool True jika berhasil, false jika gagal
     */
    public function create(array $data): bool
    {
        try {
            // Jika kode anggota belum disediakan, buat secara otomatis
            $memberCode = !empty($data['member_code']) 
                ? strtoupper(trim($data['member_code'])) 
                : $this->generateMemberCode();

            $sql = "INSERT INTO members (
                        member_code, 
                        name, 
                        email, 
                        phone, 
                        address
                    ) VALUES (
                        :member_code, 
                        :name, 
                        :email, 
                        :phone, 
                        :address
                    )";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':member_code' => $memberCode,
                ':name'        => trim($data['name'] ?? ''),
                ':email'       => !empty($data['email']) ? trim($data['email']) : null,
                ':phone'       => !empty($data['phone']) ? trim($data['phone']) : null,
                ':address'     => !empty($data['address']) ? trim($data['address']) : null,
            ]);
        } catch (PDOException $e) {
            error_log("Member::create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui data anggota berdasarkan ID
     *
     * @param int $id
     * @param array $data Data anggota yang diperbarui
     * @return bool True jika berhasil, false jika gagal
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = "UPDATE members SET 
                        member_code = :member_code, 
                        name = :name, 
                        email = :email, 
                        phone = :phone, 
                        address = :address 
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'          => $id,
                ':member_code' => strtoupper(trim($data['member_code'] ?? '')),
                ':name'        => trim($data['name'] ?? ''),
                ':email'       => !empty($data['email']) ? trim($data['email']) : null,
                ':phone'       => !empty($data['phone']) ? trim($data['phone']) : null,
                ':address'     => !empty($data['address']) ? trim($data['address']) : null,
            ]);
        } catch (PDOException $e) {
            error_log("Member::update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus data anggota berdasarkan ID
     * Memastikan data tidak dihapus jika masih digunakan dalam riwayat peminjaman (fk_loans_member)
     *
     * @param int $id
     * @return bool True jika berhasil dihapus, false jika gagal atau masih memiliki riwayat transaksi
     */
    public function delete(int $id): bool
    {
        try {
            // Validasi integritas foreign key: Cegah penghapusan jika anggota memiliki catatan di tabel loans
            if ($this->hasLoans($id)) {
                error_log("Member::delete Warning: Anggota ID {$id} tidak dapat dihapus karena memiliki riwayat peminjaman.");
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM members WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Member::delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memeriksa apakah anggota memiliki riwayat transaksi di tabel loans
     *
     * @param int $id
     * @return bool True jika terdapat catatan transaksi di loans, false jika tidak
     */
    public function hasLoans(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM loans WHERE member_id = :id");
            $stmt->execute([':id' => $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Member::hasLoans Error: " . $e->getMessage());
            // Asumsikan true untuk keamanan integritas data
            return true;
        }
    }

    /**
     * Memeriksa apakah anggota sedang memiliki pinjaman aktif yang belum dikembalikan
     *
     * @param int $id
     * @return bool True jika memiliki pinjaman berstatus 'borrowed', false jika tidak
     */
    public function hasActiveLoans(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM loans WHERE member_id = :id AND status = 'borrowed'");
            $stmt->execute([':id' => $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Member::hasActiveLoans Error: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Memeriksa apakah kode anggota sudah terdaftar (validasi kolom UNIQUE)
     *
     * @param string $code
     * @param int|null $excludeId ID anggota yang diabaikan (digunakan saat edit)
     * @return bool True jika kode sudah ada, false jika belum
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM members WHERE member_code = :member_code";
            $params = [':member_code' => strtoupper(trim($code))];

            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Member::codeExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memeriksa apakah alamat email sudah digunakan oleh anggota lain (validasi kolom UNIQUE)
     *
     * @param string $email
     * @param int|null $excludeId ID anggota yang diabaikan (digunakan saat edit)
     * @return bool True jika email sudah ada, false jika belum
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM members WHERE email = :email";
            $params = [':email' => trim($email)];

            if ($excludeId !== null) {
                $sql .= " AND id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Member::emailExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghasilkan kode anggota unik otomatis secara berurutan (misal: MBR-0001)
     *
     * @return string Kode anggota baru
     */
    public function generateMemberCode(): string
    {
        try {
            $stmt = $this->db->query("SELECT MAX(id) FROM members");
            $maxId = (int) ($stmt->fetchColumn() ?: 0);
            $nextId = $maxId + 1;
            $candidate = sprintf("MBR-%04d", $nextId);

            // Verifikasi bahwa kode yang dihasilkan belum pernah digunakan
            while ($this->codeExists($candidate)) {
                $nextId++;
                $candidate = sprintf("MBR-%04d", $nextId);
            }

            return $candidate;
        } catch (PDOException $e) {
            error_log("Member::generateMemberCode Error: " . $e->getMessage());
            return 'MBR-' . strtoupper(substr(uniqid(), -6));
        }
    }
}

