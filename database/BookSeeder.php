<?php
/**
 * ========================================================
 * BookSpace - Database Seeder: Data Dummy Buku
 * ========================================================
 * Menyediakan 20 data buku nyata dan relevan yang terdistribusi
 * ke dalam 6 kategori yang telah ada di database:
 * - Novel
 * - Bisnis
 * - Pendidikan
 * - Sains
 * - Sejarah
 * - Teknologi
 *
 * Keamanan:
 * - Mendeteksi ID kategori langsung dari database (tanpa hardcode).
 * - Tidak membuat kategori baru dan tidak mengubah kategori yang ada.
 * - Memeriksa duplikasi berdasarkan ISBN dan Judul Buku (Idempotent).
 * - Tidak menghapus atau menimpa data buku yang sudah ada.
 * - Menggunakan Prepared Statement dan Database Transaction.
 * ========================================================
 */

require_once __DIR__ . '/../config/database.php';

class BookSeeder
{
    private PDO $db;

    public function __construct()
    {
        $conn = Database::getConnection();
        if (!$conn) {
            throw new RuntimeException("Gagal terhubung ke database MySQL.");
        }
        $this->db = $conn;
    }

    /**
     * Menjalankan proses seeding 20 buku dummy
     *
     * @return array Hasil eksekusi seeding
     */
    public function run(): array
    {
        echo "========================================================\n";
        echo "   BOOKSPACE - SEEDER DATA BUKU PERPUSTAKAAN            \n";
        echo "========================================================\n\n";

        // 1. Ambil data kategori yang tersedia langsung dari database
        $stmtCat = $this->db->query("SELECT id, category_name FROM categories ORDER BY id ASC");
        $categories = $stmtCat->fetchAll();

        if (empty($categories)) {
            throw new RuntimeException("Tabel categories kosong! Pastikan kategori telah dibuat terlebih dahulu.");
        }

        // Petakan nama kategori ke ID kategori (case-insensitive)
        $categoryMap = [];
        echo "Kategori terdeteksi di database:\n";
        foreach ($categories as $cat) {
            $normalizedName = strtolower(trim($cat['category_name']));
            $categoryMap[$normalizedName] = (int) $cat['id'];
            echo " - ID {$cat['id']}: {$cat['category_name']}\n";
        }
        echo "\n";

        // Daftar 6 kategori yang wajib ada
        $requiredCategories = ['novel', 'bisnis', 'pendidikan', 'sains', 'sejarah', 'teknologi'];
        foreach ($requiredCategories as $req) {
            if (!isset($categoryMap[$req])) {
                throw new RuntimeException("Kategori '{$req}' tidak ditemukan di database. Seeder dihentikan demi menjaga integritas data.");
            }
        }

        // 2. Definisi 20 data buku nyata dan relevan
        $booksData = [
            // --- KATEGORI NOVEL (3 buku) ---
            [
                'category'         => 'Novel',
                'title'            => 'Bumi Manusia',
                'author'           => 'Pramoedya Ananta Toer',
                'publisher'        => 'Lentera Dipantara',
                'publication_year' => 2005,
                'isbn'             => '9789799731234',
                'total_stock'      => 5,
                'available_stock'  => 5
            ],
            [
                'category'         => 'Novel',
                'title'            => 'Laskar Pelangi',
                'author'           => 'Andrea Hirata',
                'publisher'        => 'Bentang Pustaka',
                'publication_year' => 2005,
                'isbn'             => '9789793062792',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],
            [
                'category'         => 'Novel',
                'title'            => 'Cantik Itu Luka',
                'author'           => 'Eka Kurniawan',
                'publisher'        => 'Gramedia Pustaka Utama',
                'publication_year' => 2015,
                'isbn'             => '9786020312583',
                'total_stock'      => 3,
                'available_stock'  => 3
            ],

            // --- KATEGORI TEKNOLOGI (4 buku) ---
            [
                'category'         => 'Teknologi',
                'title'            => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author'           => 'Robert C. Martin',
                'publisher'        => 'Prentice Hall',
                'publication_year' => 2008,
                'isbn'             => '9780132350884',
                'total_stock'      => 6,
                'available_stock'  => 6
            ],
            [
                'category'         => 'Teknologi',
                'title'            => 'Designing Data-Intensive Applications',
                'author'           => 'Martin Kleppmann',
                'publisher'        => "O'Reilly Media",
                'publication_year' => 2017,
                'isbn'             => '9781449373320',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],
            [
                'category'         => 'Teknologi',
                'title'            => 'The Pragmatic Programmer: Your Journey to Mastery',
                'author'           => 'David Thomas, Andrew Hunt',
                'publisher'        => 'Addison-Wesley Professional',
                'publication_year' => 2019,
                'isbn'             => '9780135957059',
                'total_stock'      => 5,
                'available_stock'  => 5
            ],
            [
                'category'         => 'Teknologi',
                'title'            => 'Introduction to Algorithms',
                'author'           => 'Thomas H. Cormen, Charles E. Leiserson',
                'publisher'        => 'The MIT Press',
                'publication_year' => 2009,
                'isbn'             => '9780262033848',
                'total_stock'      => 3,
                'available_stock'  => 3
            ],

            // --- KATEGORI SAINS (3 buku) ---
            [
                'category'         => 'Sains',
                'title'            => 'Cosmos',
                'author'           => 'Carl Sagan',
                'publisher'        => 'Ballantine Books',
                'publication_year' => 2013,
                'isbn'             => '9780345539434',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],
            [
                'category'         => 'Sains',
                'title'            => 'A Brief History of Time',
                'author'           => 'Stephen Hawking',
                'publisher'        => 'Bantam Books',
                'publication_year' => 1998,
                'isbn'             => '9780553380163',
                'total_stock'      => 5,
                'available_stock'  => 5
            ],
            [
                'category'         => 'Sains',
                'title'            => 'The Selfish Gene',
                'author'           => 'Richard Dawkins',
                'publisher'        => 'Oxford University Press',
                'publication_year' => 2006,
                'isbn'             => '9780199291151',
                'total_stock'      => 3,
                'available_stock'  => 3
            ],

            // --- KATEGORI PENDIDIKAN (3 buku) ---
            [
                'category'         => 'Pendidikan',
                'title'            => 'Pendidikan Kaum Tertindas',
                'author'           => 'Paulo Freire',
                'publisher'        => 'LP3ES',
                'publication_year' => 2008,
                'isbn'             => '9789798391804',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],
            [
                'category'         => 'Pendidikan',
                'title'            => 'Sekolahnya Manusia: Melejitkan Potensi Semua Anak',
                'author'           => 'Munif Chatib',
                'publisher'        => 'Kaifa Publishing',
                'publication_year' => 2012,
                'isbn'             => '9786027870987',
                'total_stock'      => 5,
                'available_stock'  => 5
            ],
            [
                'category'         => 'Pendidikan',
                'title'            => 'Menjadi Guru Penggerak Praktik Baik',
                'author'           => 'Satria Dharma',
                'publisher'        => 'Erlangga',
                'publication_year' => 2019,
                'isbn'             => '9786022419822',
                'total_stock'      => 3,
                'available_stock'  => 3
            ],

            // --- KATEGORI SEJARAH (3 buku) ---
            [
                'category'         => 'Sejarah',
                'title'            => 'Sapiens: Riwayat Singkat Umat Manusia',
                'author'           => 'Yuval Noah Harari',
                'publisher'        => 'Kepustakaan Populer Gramedia',
                'publication_year' => 2017,
                'isbn'             => '9786024244163',
                'total_stock'      => 6,
                'available_stock'  => 6
            ],
            [
                'category'         => 'Sejarah',
                'title'            => 'Nusantara: Sejarah Indonesia',
                'author'           => 'Bernard H.M. Vlekke',
                'publisher'        => 'Kepustakaan Populer Gramedia',
                'publication_year' => 2008,
                'isbn'             => '9789799101235',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],
            [
                'category'         => 'Sejarah',
                'title'            => 'Guns, Germs, and Steel',
                'author'           => 'Jared Diamond',
                'publisher'        => 'W. W. Norton & Company',
                'publication_year' => 2005,
                'isbn'             => '9780393061314',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],

            // --- KATEGORI BISNIS (4 buku) ---
            [
                'category'         => 'Bisnis',
                'title'            => 'The Lean Startup',
                'author'           => 'Eric Ries',
                'publisher'        => 'Crown Business',
                'publication_year' => 2011,
                'isbn'             => '9780307887894',
                'total_stock'      => 5,
                'available_stock'  => 5
            ],
            [
                'category'         => 'Bisnis',
                'title'            => 'Zero to One: Notes on Startups, or How to Build the Future',
                'author'           => 'Peter Thiel, Blake Masters',
                'publisher'        => 'Crown Business',
                'publication_year' => 2014,
                'isbn'             => '9780804139298',
                'total_stock'      => 5,
                'available_stock'  => 5
            ],
            [
                'category'         => 'Bisnis',
                'title'            => "Good to Great: Why Some Companies Make the Leap... and Others Don't",
                'author'           => 'Jim Collins',
                'publisher'        => 'HarperBusiness',
                'publication_year' => 2001,
                'isbn'             => '9780066620992',
                'total_stock'      => 4,
                'available_stock'  => 4
            ],
            [
                'category'         => 'Bisnis',
                'title'            => 'Atomic Habits: Perubahan Kecil yang Memberikan Hasil Luar Biasa',
                'author'           => 'James Clear',
                'publisher'        => 'Gramedia Pustaka Utama',
                'publication_year' => 2019,
                'isbn'             => '9786020633176',
                'total_stock'      => 7,
                'available_stock'  => 7
            ],
        ];

        // 3. Eksekusi penyimpanan dengan perlindungan duplikasi & transaksi database
        $checkIsbnStmt = $this->db->prepare("SELECT id, title FROM books WHERE isbn = :isbn LIMIT 1");
        $checkTitleStmt = $this->db->prepare("SELECT id, isbn FROM books WHERE title = :title LIMIT 1");

        $insertStmt = $this->db->prepare("
            INSERT INTO books (
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
            )
        ");

        $this->db->beginTransaction();

        $insertedCount = 0;
        $skippedCount = 0;
        $categoryBreakdown = [
            'Novel'      => 0,
            'Bisnis'     => 0,
            'Pendidikan' => 0,
            'Sains'      => 0,
            'Sejarah'    => 0,
            'Teknologi'  => 0,
        ];

        try {
            foreach ($booksData as $b) {
                $catKey = strtolower(trim($b['category']));
                $categoryId = $categoryMap[$catKey];

                // Cek apakah ISBN sudah ada di database
                $checkIsbnStmt->execute([':isbn' => $b['isbn']]);
                $existingByIsbn = $checkIsbnStmt->fetch();

                // Cek apakah Judul sudah ada di database
                $checkTitleStmt->execute([':title' => $b['title']]);
                $existingByTitle = $checkTitleStmt->fetch();

                if ($existingByIsbn || $existingByTitle) {
                    $skippedCount++;
                    $reason = $existingByIsbn ? "ISBN '{$b['isbn']}' sudah ada" : "Judul '{$b['title']}' sudah ada";
                    echo " [DILEWATI] {$b['title']} ({$reason})\n";
                    continue;
                }

                // Simpan buku baru
                $insertStmt->execute([
                    ':category_id'      => $categoryId,
                    ':title'            => $b['title'],
                    ':author'           => $b['author'],
                    ':publisher'        => $b['publisher'],
                    ':publication_year' => $b['publication_year'],
                    ':isbn'             => $b['isbn'],
                    ':total_stock'      => $b['total_stock'],
                    ':available_stock'  => $b['available_stock'],
                ]);

                $insertedCount++;
                $categoryBreakdown[$b['category']]++;
                echo " [BERHASIL] {$b['title']} -> Kategori {$b['category']} (ID: {$categoryId}) | Stok: {$b['available_stock']}\n";
            }

            $this->db->commit();
            echo "\nTransaksi commit database berhasil!\n\n";
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new RuntimeException("Terjadi kesalahan transaksi seeder: " . $e->getMessage());
        }

        return [
            'total_target'       => count($booksData),
            'inserted'           => $insertedCount,
            'skipped'            => $skippedCount,
            'category_breakdown' => $categoryBreakdown,
        ];
    }
}

// Eksekusi seeder jika dipanggil via CLI
if (php_sapi_name() === 'cli' || !empty($_GET['run_seeder'])) {
    try {
        $seeder = new BookSeeder();
        $result = $seeder->run();

        echo "========================================================\n";
        echo "   LAPORAN HASIL PENAMBAHAN DATA BUKU DUMMY             \n";
        echo "========================================================\n";
        echo "Total target buku   : {$result['total_target']}\n";
        echo "Berhasil ditambahkan: {$result['inserted']}\n";
        echo "Dilewati (duplikasi): {$result['skipped']}\n\n";

        echo "Rincian Buku Berhasil Ditambahkan per Kategori:\n";
        foreach ($result['category_breakdown'] as $catName => $count) {
            echo " - {$catName}: {$count} buku\n";
        }
        echo "========================================================\n";
    } catch (Exception $e) {
        echo "ERROR SEEDER: " . $e->getMessage() . "\n";
        exit(1);
    }
}
