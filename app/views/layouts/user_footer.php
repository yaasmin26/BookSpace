<?php
/**
 * ========================================================
 * BookSpace - Modular User Footer & Borrow Modal Component
 * ========================================================
 */
?>
    <!-- Modal Konfirmasi Peminjaman Online -->
    <div id="borrowModal" class="modal-backdrop" style="display: none;">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-icon">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-title">Konfirmasi Peminjaman Buku</h3>
                    <p class="modal-subtitle">Pastikan Anda membaca ketentuan sirkulasi perpustakaan</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeBorrowModal()">&times;</button>
            </div>

            <form action="index.php?url=catalog/borrow" method="POST" id="borrowForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="book_id" id="modalBookId" value="">

                <div class="modal-body">
                    <p class="modal-prompt">Apakah Anda yakin ingin meminjam buku berikut?</p>
                    
                    <div class="modal-book-preview">
                        <div class="preview-title" id="modalBookTitle">-</div>
                        <div class="preview-meta">Penulis: <span id="modalBookAuthor">-</span></div>
                    </div>

                    <div class="modal-terms-box">
                        <div class="term-row">
                            <span class="term-label">Durasi Pinjam:</span>
                            <span class="term-val"><strong>14 Hari Kerja</strong></span>
                        </div>
                        <div class="term-row">
                            <span class="term-label">Batas Pengembalian:</span>
                            <span class="term-val" style="color: var(--navy); font-weight: 700;">
                                <?= date('d M Y', strtotime('+14 days')) ?>
                            </span>
                        </div>
                        <div class="term-row">
                            <span class="term-label">Batas Kuota:</span>
                            <span class="term-val">Maksimal 3 buku aktif per anggota</span>
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-modal-cancel" onclick="closeBorrowModal()">Batal</button>
                    <button type="submit" class="btn-modal-confirm">Ya, Pinjam Buku Ini</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Perpustakaan -->
    <footer class="app-footer" style="padding: 1.5rem 2.25rem; border-top: 1px solid var(--border); background: var(--white); text-align: center; font-size: 0.8125rem; color: var(--text-secondary); margin-top: auto;">
        <p>&copy; <?= date('Y') ?> <strong>BookSpace Library Management System</strong>. Seluruh Hak Cipta Dilindungi.</p>
    </footer>

    <!-- Script Pengendali Modal -->
    <script>
        function openBorrowModal(bookId, bookTitle, bookAuthor) {
            document.getElementById('modalBookId').value = bookId;
            document.getElementById('modalBookTitle').textContent = bookTitle || 'Buku Perpustakaan';
            document.getElementById('modalBookAuthor').textContent = bookAuthor || '-';
            
            const modal = document.getElementById('borrowModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeBorrowModal() {
            const modal = document.getElementById('borrowModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('click', function(event) {
            const modal = document.getElementById('borrowModal');
            if (event.target === modal) {
                closeBorrowModal();
            }
        });

        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBorrowModal();
            }
        });
    </script>
