<!-- resources/views/admin/transactions/partials/modal-print.blade.php -->
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-printer me-2"></i>Cetak Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pb-4 px-4">
                <p class="text-muted mb-4 small">Pilih format cetak untuk transaksi ini.</p>

                <div class="d-grid gap-2 col-10 mx-auto">
                    
                    <button
                        class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('bon_3_rangkap')">
                        <i class="bi bi-file-earmark-ruled me-2"></i> Cetak Bon (3 Rangkap)
                    </button>
                    <button
                         class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('bon_1_rangkap')">
                        <i class="bi bi-receipt me-2"></i> Cetak Bon Standar
                    </button>
                    <button
                         class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('fasetan')">
                        <i class="bi bi-file-earmark-medical me-2"></i> Cetak Bon Fasetan
                    </button>
                    <button id="btn-print-bpjs"
                         class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('formulir_bpjs')">
                        <i class="bi bi-file-earmark-person me-2"></i> Cetak Form BPJS
                    </button>
                    <button
                         class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('garansi')">
                        <i class="bi bi-patch-check me-2"></i> Cetak Kartu Garansi
                    </button>
                    <button
                         class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('pesanan_besar', 0)">
                        <i class="bi bi-file-earmark-text me-2"></i> Cetak Kwitansi Tanpa Perincian
                    </button>
                    <button
                         class="btn btn-outline-primary rounded-pill py-2 shadow-sm mb-1 d-flex align-items-center justify-content-center"
                        onclick="doPrint('pesanan_besar', 1)">
                        <i class="bi bi-list-check me-2"></i> Cetak Kwitansi Dengan Perincian
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                @if(Route::currentRouteName() === 'transactions.create')
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Selesai & Ke Riwayat
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none"></iframe>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const printModal = new bootstrap.Modal(document.getElementById('printModal'));
        const printFrame = document.getElementById('printFrame');
        const globalTrxIdInput = document.getElementById('trx_id');

        // Basic Helpers for Print Modal
        function formatRibuan(val) {
            return new Intl.NumberFormat('id-ID').format(val);
        }

        function snackbar(msg, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type, title: msg, toast: true,
                    position: 'bottom-end', showConfirmButton: false, timer: 2200,
                    timerProgressBar: true
                });
            } else {
                console.log(msg);
            }
        }

        function openPrintModal(id = null, isBpjs = false) {
            if (id) {
                if (globalTrxIdInput) globalTrxIdInput.value = id;
                else window._currentPrintId = id;
            }

            const currentId = id || (globalTrxIdInput ? globalTrxIdInput.value : window._currentPrintId);
            if (!currentId) {
                if (typeof snackbar === 'function') snackbar('ID Transaksi tidak ditemukan', 'warning');
                else alert('ID Transaksi tidak ditemukan');
                return;
            }

            // Show/Hide BPJS button
            const bpjsBtn = document.getElementById('btn-print-bpjs');
            if (bpjsBtn) {
                if (isBpjs) bpjsBtn.classList.remove('d-none');
                else bpjsBtn.classList.add('d-none');
            }

            printModal.show();
        }

        function doPrint(type, showDetail = 1) {
            const id = globalTrxIdInput ? globalTrxIdInput.value : window._currentPrintId;
            if (!id) return;

            let url = `{{ url('transactions') }}/${id}/print?type=${type}`;
            if (type === 'pesanan_besar') {
                url += `&show_detail=${showDetail}`;
            }
            printFrame.src = url;

            // Auto-print functionality
            printFrame.onload = function () {
                setTimeout(() => {
                    if (printFrame.contentWindow) {
                        printFrame.contentWindow.print();
                    }
                }, 500);
            };
        }
    </script>
@endpush