<div class="wizard-panel" id="panel-step-4">
    <div class="step-error-msg" id="err-step-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span id="err-step-4-msg">Mohon lengkapi data tambahan yang diperlukan.</span>
    </div>

    <div class="glass-card">
        <div class="card-body p-4">
            <h6 class="card-header-section">
                <i class="bi bi-gear"></i> Data Tambahan
                <span class="badge-opt ms-1">opsional</span>
            </h6>

            <div class="row g-3">

                <!-- INFORMASI LAB -->
                <div class="col-12 mb-3">
                    <h6 class="text-muted small fw-bold">Informasi Lab</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Lab</label>
                            <input type="text" name="lab" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tgl Order</label>
                            <input type="text" name="tgl_order" id="tgl_order" class="form-control form-control-sm datepicker" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tgl Lensa Datang</label>
                            <input type="text" name="tgl_datang_faset" id="tgl_lensa_datang"
                                class="form-control form-control-sm datepicker" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                    </div>
                </div>

                <!-- PROSES FASET -->
                <div class="col-12 mb-3">
                    <h6 class="text-muted small fw-bold">Proses Faset</h6>
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Tgl Faset</label>
                            <input type="text" name="tgl_faset" id="tgl_faset" class="form-control form-control-sm datepicker" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tempat Faset</label>
                            <input type="text" name="tempat_faset" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tgl Selesai Faset</label>
                            <input type="text" name="tgl_selesai_faset" id="tgl_selesai_faset"
                                class="form-control form-control-sm datepicker" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>

                    </div>
                </div>

                <!-- PENYELESAIAN & PENGAMBILAN -->
                <div class="col-12 mb-3">
                    <h6 class="text-muted small fw-bold">Penyelesaian & Pengambilan</h6>
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Tgl Diambil</label>
                            <input type="text" name="tgl_diambil" id="tgl_diambil" class="form-control form-control-sm datepicker" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status Ambil</label>
                            <div class="d-flex gap-3 align-items-center mt-1">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="diambil" id="belum" value="2"
                                        checked>
                                    <label class="form-check-label small">Belum</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="diambil" id="sudah" value="1">
                                    <label class="form-check-label small">Sudah</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tgl Selesai (Janji Customer)</label>
                            <input type="text" name="tgl_selesai_janji" id="tgl_selesai_janji"
                                class="form-control form-control-sm datepicker" placeholder="dd/mm/yyyy" autocomplete="off" required>
                        </div>

                    </div>
                </div>

                <!-- CATATAN -->
                <div class="col-12 mt-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="2"></textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(3)">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
        <div class="nav-right">
            <span class="step-hint"></span>
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(4)">
                Lanjut ke Checkout <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>