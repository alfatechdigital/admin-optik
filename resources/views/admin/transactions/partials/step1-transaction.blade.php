<div class="wizard-panel is-active" id="panel-step-1">
    
            <div class="glass-card">
                <div class="card-body p-4">
                    <h6 class="card-header-section">
                        <i class="bi bi-receipt"></i> Informasi Faktur
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">No Faktur <span class="badge-auto ms-1">auto</span></label>
                            <input type="text" name="no_transaksi" id="no_transaksi"
                                class="form-control form-control-sm bg-light fw-semibold text-primary"
                                value="{{ \App\Models\Transaction::generateNomor() }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Faktur</label>
                            <input type="text" name="tgl_faktur" id="tgl_faktur"
                                class="form-control form-control-sm datepicker"
                                value="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Transaksi</label>
                            <div class="d-flex gap-3 align-items-center mt-1">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="typefaktur" id="tunai" value="1" checked onchange="checkForChanges()">
                                    <label class="form-check-label small" for="tunai">Umum</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="typefaktur" id="bpjs" value="2" onchange="checkForChanges()">
                                    <label class="form-check-label small" for="bpjs">BPJS</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        
    </div>

    <div class="wizard-nav-bar">
        <span class="step-hint"><i class="bi bi-info-circle me-1"></i>No faktur &amp; tanggal wajib diisi</span>
        <div class="nav-right">
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(1)">
                Lanjut ke Pasien &amp; Resep <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
