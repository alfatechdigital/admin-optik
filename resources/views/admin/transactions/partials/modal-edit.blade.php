<!-- resources/views/admin/transactions/partials/modal-edit.blade.php -->
<div class="modal fade" id="editPrintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Edit Format Cetak
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pb-4 px-4">
                <p class="text-muted mb-4 small">Pilih format cetak yang ingin Anda sunting sebelum dicetak atau
                    diunduh.</p>

                <div class="d-grid gap-2 col-10 mx-auto">
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('bon_3_rangkap')">
                        <i class="bi bi-file-earmark-ruled me-2"></i> Edit Bon (3 Rangkap)
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('bon_1_rangkap')">
                        <i class="bi bi-receipt me-2"></i> Edit Bon Standar
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('fasetan')">
                        <i class="bi bi-file-earmark-medical me-2"></i> Edit Bon Fasetan
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('pelunasan')">
                        <i class="bi bi-file-earmark-medical me-2"></i> Edit Bon Pelunasan
                    </button>
                    <button id="btn-edit-print-bpjs"
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('formulir_bpjs')">
                        <i class="bi bi-file-earmark-person me-2"></i> Edit Form BPJS
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('label')">
                        <i class="bi bi-patch-check me-2"></i> Edit Label
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('garansi')">
                        <i class="bi bi-patch-check me-2"></i> Edit Kartu Garansi
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('pesanan_besar', 0)">
                        <i class="bi bi-file-earmark-text me-2"></i> Edit Kwitansi Tanpa Perincian
                    </button>
                    <button
                        class="btn btn-outline-warning rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doEditPrint('pesanan_besar', 1)">
                        <i class="bi bi-list-check me-2"></i> Edit Kwitansi Dengan Perincian
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const editPrintModalElement = document.getElementById('editPrintModal');
        let editPrintModal;
        if (editPrintModalElement) {
            editPrintModal = new bootstrap.Modal(editPrintModalElement);
        }

        function openEditPrintModal(id = null, isBpjs = false) {
            if (id) {
                if (typeof globalTrxIdInput !== 'undefined' && globalTrxIdInput) {
                    globalTrxIdInput.value = id;
                } else {
                    window._currentPrintId = id;
                }
            }

            const currentId = id || (typeof globalTrxIdInput !== 'undefined' && globalTrxIdInput ? globalTrxIdInput.value : window._currentPrintId);
            if (!currentId) {
                if (typeof snackbar === 'function') snackbar('ID Transaksi tidak ditemukan', 'warning');
                else alert('ID Transaksi tidak ditemukan');
                return;
            }

            // Show/Hide BPJS button
            const bpjsBtn = document.getElementById('btn-edit-print-bpjs');
            if (bpjsBtn) {
                if (isBpjs) bpjsBtn.classList.remove('d-none');
                else bpjsBtn.classList.add('d-none');
            }

            if (editPrintModal) {
                editPrintModal.show();
            }
        }

        function doEditPrint(type, showDetail = 1) {
            const id = typeof globalTrxIdInput !== 'undefined' && globalTrxIdInput ? globalTrxIdInput.value : window._currentPrintId;
            if (!id) return;

            let url = `{{ url('transactions') }}/${id}/edit-print?type=${type}`;
            if (type === 'pesanan_besar') {
                url += `&show_detail=${showDetail}`;
            }

            // Redirect to the CKEditor page
            window.location.href = url;
        }
    </script>
@endpush