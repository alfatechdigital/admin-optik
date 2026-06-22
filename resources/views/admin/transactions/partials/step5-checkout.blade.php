<div class="wizard-panel" id="panel-step-5">


    <div class="glass-card mb-0 h-100">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="card-header-section mb-0">
                    <i class="bi bi-card-checklist"></i> Preview Lengkap Transaksi
                </h6>

                <div id="btn-print-bpjs-container" class="d-none">
                    <button type="button" class="btn btn-sm btn-outline-info shadow-sm btn-action"
                        onclick="printBpjsForm()">
                        <i class="bi bi-printer"></i> Cetak Form BPJS
                    </button>
                </div>
            </div>

            {{-- BADGES --}}
            <div class="row g-2 mb-3" id="checkout-summary-badges"></div>

            <div class="row g-3">

                {{-- INVOICE --}}
                <div class="col-md-12">
                    <div class="glass-card">
                        <div class="card-body p-3">
                            <h6 class="card-header-section">
                                <i class="bi bi-receipt"></i> Invoice
                            </h6>
                            <div id="checkout-preview-invoice"></div>
                        </div>
                    </div>
                </div>

                {{-- PATIENT --}}
                <div class="col-md-12">
                    <div class="glass-card">
                        <div class="card-body p-3">
                            <h6 class="card-header-section">
                                <i class="bi bi-person"></i> Data Pasien
                            </h6>
                            <div id="checkout-preview-patient"></div>
                        </div>
                    </div>
                </div>

                {{-- RESEP --}}
                <div class="col-md-12">
                    <div class="glass-card">
                        <div class="card-body p-3">
                            <h6 class="card-header-section">
                                <i class="bi bi-capsule"></i> Resep
                            </h6>
                            <div id="checkout-preview-resep"></div>
                        </div>
                    </div>
                </div>
                {{-- ITEM LIST --}}
                <div class="col-md-12">
                    <div class="glass-card h-100">
                        <div class="card-body p-3">
                            <h6 class="card-header-section">
                                <i class="bi bi-list-ul"></i> Daftar Item
                            </h6>
                            <div id="checkout-item-list" class="border rounded-3 p-3 bg-light mb-3"
                                style="max-height:260px; overflow-y:auto;">
                            </div>
                            <div id="checkout-preview-payments"></div>
                        </div>
                    </div>
                </div>



                {{-- TAMBAHAN --}}
                <div class="col-12">
                    <div class="glass-card">
                        <div class="card-body p-3">
                            <h6 class="card-header-section">
                                <i class="bi bi-plus-circle"></i> Informasi Tambahan
                            </h6>
                            <div id="checkout-preview-additional"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(4)">
            <i class="bi bi-arrow-left"></i> Kembali ke Data Tambahan
        </button>
        <div class="nav-right">
            <span class="step-hint">Review semua data sebelum simpan</span>
            <button type="submit" class="btn btn-action btn-success shadow-sm px-4" id="btn-simpan">
                <i class="bi bi-save"></i> Simpan Transaksi
            </button>
        </div>
    </div>
</div>