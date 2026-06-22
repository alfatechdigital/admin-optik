<div class="wizard-panel" id="panel-step-3">
    <div class="step-error-msg" id="err-step-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span id="err-step-3-msg">Tambahkan minimal 1 produk ke keranjang sebelum melanjutkan.</span>
    </div>

    <div class="row g-3">
        <!-- KIRI: Pemilihan Produk -->
        <div class="col-lg-7">
            {{-- CONTAINER UMUM --}}
            <div id="umum-product-container">
                <div class="glass-card h-100">
                    <div class="card-body p-4">
                        <h6 class="card-header-section">
                            <i class="bi bi-box-seam"></i> Tambah Produk (UMUM)
                        </h6>

                        <div id="product-entry">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">Cari Produk <span
                                            class="badge-opt ms-1">opsional</span></label>
                                    <div class="input-group input-group-sm position-relative">
                                        <input type="text" id="ac_produk_new" class="form-control" autocomplete="off"
                                            placeholder="Ketik kode / nama produk...">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="clearProductSearch()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        <div id="dd_produk_new" class="ac-dropdown d-none"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" id="new_item_name" class="form-control form-control-sm"
                                        placeholder="Nama frame / lensa / item...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tipe Produk <span class="text-danger">*</span></label>
                                    <select id="new_item_type" class="form-select form-select-sm">
                                        <option value="Frame">Frame</option>
                                        <option value="Lensa">Lensa</option>
                                        <option value="Lainnya" selected>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" id="new_item_seri" class="form-control form-control-sm"
                                        placeholder="Merek / seri..." Hidden>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" id="new_item_warna" class="form-control form-control-sm"
                                        placeholder="Warna..." hidden>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Harga Satuan</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" id="new_item_harga" class="form-control text-end"
                                            placeholder="0" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Qty</label>
                                    <input type="number" id="new_item_qty"
                                        class="form-control form-control-sm text-center" value="1" min="1">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-success btn-action w-100" id="btn-add-item"
                                        onclick="addItemToCart()">
                                        <i class="bi bi-cart-plus"></i> Tambah
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" id="new_item_keterangan" class="form-control form-control-sm"
                                        placeholder="Keterangan tambahan...">
                                </div>
                            </div>
                        </div>

                        {{-- Hidden fields lama --}}
                        <div id="product-items" style="display:none">
                            <div class="product-item">
                                <input type="hidden" name="product_id[]">
                                <input type="text" name="kode_frame[]" value="">
                                <input type="text" name="kode_produk_display[]" value="">
                                <input type="text" name="nama_produk[]" value="">
                                <input type="text" name="seri[]" value="">
                                <input type="text" name="warna[]" value="">
                                <input type="text" name="keterangan[]" value="">
                                <input type="text" name="harga_satuan[]" value="0" class="harga-satuan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CONTAINER BPJS --}}
            <div id="bpjs-product-container" class="d-none">
                <div class="glass-card h-100" style="border-left: 4px solid #7c3aed;">
                    <div class="card-body p-4 border-bottom border-light">
                        <h6 class="card-header-section" style="color: #7c3aed;">
                            <i class="bi bi-eyeglasses"></i> Pilih Frame
                        </h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="input-group input-group-sm position-relative">
                                    <input type="text" id="bpjs_frame_ac" class="form-control" autocomplete="off"
                                        placeholder="Cari Frame...">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="clearBpjs('Frame')"><i class="bi bi-x"></i></button>
                                    <div id="bpjs_frame_dd" class="ac-dropdown d-none"></div>
                                </div>
                            </div>
                            <input type="hidden" id="bpjs_frame_id">
                            <div class="col-12">
                                <label class="form-label">Nama Frame <span class="text-danger">*</span></label>
                                <input type="text" name="bpjs_frame_nama" id="bpjs_frame_nama" class="form-control"
                                    oninput="document.getElementById('bpjs_frame_tipe').value = 'frame';">
                                <input type="hidden" id="bpjs_frame_tipe" name="bpjs_frame_tipe" value="">
                                <select name="select_frame" id="select_frame"
                                    onchange="document.getElementById('bpjs_frame_tipe').value = 'frame';">
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Harga Beli</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="text" id="bpjs_frame_harga" class="form-control text-end" value="0">
                                </div>
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <button type="button" class="btn btn-primary btn-sm w-100"
                                    onclick="setBpjsItem('Frame')">Set Frame</button>
                            </div>
                        </div>
                        {{-- Status Indicator --}}
                        <div id="bpjs_frame_status"
                            class="mt-3 p-2 rounded bg-light border d-none small text-center text-success fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Frame terpilih
                        </div>
                    </div>
                    <div class="card-body p-4" style="border-left: 4px solid #059669; margin-left: -4px;">
                        <h6 class="card-header-section" style="color: #059669;">
                            <i class="bi bi-circle"></i> Pilih Lensa
                        </h6>
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="input-group input-group-sm position-relative">
                                    <input type="text" id="bpjs_lensa_ac" class="form-control" autocomplete="off"
                                        placeholder="Cari Lensa...">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="clearBpjs('Lensa')"><i class="bi bi-x"></i></button>
                                    <div id="bpjs_lensa_dd" class="ac-dropdown d-none"></div>
                                </div>
                            </div>
                            <input type="hidden" id="bpjs_lensa_id">
                            <div class="col-12">
                                <label class="form-label">Nama Lensa <span class="text-danger">*</span></label>
                                <input type="text" name="bpjs_lensa_nama" id="bpjs_lensa_nama" class="form-control"
                                    oninput="document.getElementById('bpjs_lensa_tipe').value = 'lensa';">
                                <input type="hidden" id="bpjs_lensa_tipe" name="bpjs_lensa_tipe" value=""><select
                                    name="select_lensa" id="select_lensa"
                                    onchange="document.getElementById('bpjs_lensa_tipe').value = 'lensa';">
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Harga Beli</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="text" id="bpjs_lensa_harga" class="form-control text-end" value="0">
                                </div>
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <button type="button" class="btn btn-success btn-sm w-100"
                                    onclick="setBpjsItem('Lensa')">Set Lensa</button>
                            </div>
                        </div>
                        {{-- Status Indicator --}}
                        <div id="bpjs_lensa_status"
                            class="mt-3 p-2 rounded bg-light border d-none small text-center text-success fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Lensa terpilih
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- KANAN: Keranjang & Rincian Pembayaran -->
        <div class="col-lg-5">
            <div class="finance-card p-4">
                <h6 class="card-header-section text-white mb-3">
                    <i class="bi bi-cash-stack"></i> Rincian Pembayaran
                </h6>

                <!-- Keranjang Terintegrasi dalam Finance Card -->
                <div class="accordion mb-4" id="accordionCart">
                    <div class="accordion-item bg-transparent border-0">
                        <h2 class="accordion-header" id="headingCart">
                            <!-- Ditambahkan class 'collapsed' agar default tertutup -->
                            <button
                                class="accordion-button collapsed bg-white bg-opacity-10 text-white border-0 shadow-none px-3 rounded"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseCart"
                                aria-expanded="false" aria-controls="collapseCart"
                                style="border-radius: 10px !important;">
                                <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                    <div>
                                        <i class="bi bi-cart3 me-2"></i>
                                        <span class="fw-bold">Ringkasan Pesanan</span>
                                        <span class="badge bg-primary ms-1 rounded-pill" id="cart-count-badge">
                                            {{ isset($transaction) ? count($transaction->items) : 0 }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-white-50 d-block"
                                            style="font-size: 0.65rem;">SUBTOTAL</small>
                                        <strong class="text-white" id="cart-inline-total">
                                            Rp
                                            {{ isset($transaction) ? number_format($transaction->total_harga, 0, ',', '.') : '0' }}
                                        </strong>
                                    </div>
                                </div>
                            </button>
                        </h2>

                        <!-- Class 'show' dihapus agar default tertutup -->
                        <div id="collapseCart" class="accordion-collapse collapse" aria-labelledby="headingCart"
                            data-bs-parent="#accordionCart">
                            <div class="accordion-body px-1 py-3 text-white">
                                <div id="cart-inline-body">
                                    @if(isset($transaction) && count($transaction->items) > 0)
                                        @foreach($transaction->items as $item)
                                            <div
                                                class="cart-item-row d-flex align-items-center mb-2 pb-2 border-bottom border-white border-opacity-10">
                                                <span
                                                    class="cart-type-badge small px-2 py-1 rounded me-2 {{ $item->type === 'Frame' ? 'ct-frame' : ($item->type === 'Lensa' ? 'ct-lensa' : 'ct-other') }}"
                                                    style="font-size: 0.7rem;">
                                                    {{ $item->type ?? 'Product' }}
                                                </span>
                                                <div class="cart-item-name-group flex-grow-1" style="min-width: 0;">
                                                    <div class=" cart-item-name fw-semibold small" style="color: #ffffff"
                                                        title="{{ $item->nama_produk }}">
                                                        {{ $item->nama_produk }}
                                                    </div>
                                                    <div class="text-white-50" style="font-size: 0.75rem;">
                                                        {{ $item->seri ?? '' }}
                                                        {{ $item->warna ? ($item->seri ? '| ' : '') . $item->warna : '' }}
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-2 small">x{{ $item->qty }}</span>
                                                    <span class="fw-bold small">
                                                        Rp{{ number_format($item->harga_satuan * $item->qty, 0, ',', '.') }}
                                                    </span>
                                                    <button class="btn btn-link btn-sm text-danger cart-item-del p-0 ms-2"
                                                        style="text-decoration:none">✕</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3 text-white-50" id="cart-empty-msg">
                                            <small>Keranjang kosong</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Finance Section -->
                <div class="summary-box mb-3" id="finance-cart-summary">
                    {{-- diisi JS --}}
                </div>

                <div class="mb-3">
                    <span class="finance-label">Total Harga <small style="font-size:0.65rem;opacity:0.7">(otomatis dari
                            keranjang)</small></span>
                    <div class="input-group input-group-sm bg-white bg-opacity-10 rounded" style="opacity:0.85">
                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                        <input type="text" name="harga_jual" id="input_harga_jual"
                            class="form-control bg-transparent border-0 text-white text-end fs-6" value="0" readonly
                            style="cursor:default">
                    </div>
                </div>

                <div class="mb-3" id="container_potongan">
                    <span class="finance-label">Potongan Biasa / Diskon (−)</span>
                    <div class="input-group input-group-sm bg-white bg-opacity-10 rounded">
                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                        <input type="text" name="potongan" id="input_potongan"
                            class="form-control bg-transparent border-0 text-white text-end" value="0">
                    </div>
                </div>

                <div class="mb-3" id="container_potongan_bpjs">
                    <span class="finance-label">Potongan BPJS Kesehatan (−)</span>
                    <div
                        class="input-group input-group-sm bg-white bg-opacity-10 rounded border border-info border-opacity-50">
                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                        <input type="text" name="potongan_bpjs" id="input_potongan_bpjs"
                            class="form-control bg-transparent border-0 text-white text-end" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="finance-label mb-0">DP / Bayar Pertama <small id="lbl_selisih_bpjs"
                                class="d-none text-info">(Selisih Yang Dibayar)</small></span>

                    </div>
                    <div class="input-group input-group-sm bg-white bg-opacity-10 rounded">
                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                        <input type="text" name="dp" id="input_dp"
                            class="form-control bg-transparent border-0 text-white text-end" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <span class="finance-label mb-0">Pelunasan</span>
                    </div>
                    <div class="input-group input-group-sm bg-white bg-opacity-10 rounded">
                        <span class="input-group-text bg-transparent border-0 text-white fw-bold">Rp</span>
                        <input type="text" name="pelunasan" id="input_pelunasan"
                            class="form-control bg-transparent border-0 text-white text-end" value="0">
                        <button type="button" class="btn btn-info btn-sm text-white px-3 fw-bold"
                            onclick="setLunas()">Set Lunas</button>
                    </div>
                </div>

                <!-- Sisa Tagihan Section -->
                <div class="mt-4 pt-3 border-top border-white border-opacity-25">
                    <span class="finance-label opacity-75 d-block mb-1">Sisa Tagihan</span>
                    <div class="input-group input-group-lg bg-danger bg-opacity-25 rounded overflow-hidden">
                        <span class="input-group-text fw-bold border-0 bg-transparent text-white">Rp</span>
                        <input type="text" name="sisa" id="input_sisa"
                            class="form-control bg-transparent border-0 text-white text-end fw-bold sisa-field" readonly
                            value="0">
                    </div>
                </div>

                <!-- Warning Alerts -->
                <div id="dp-warning"
                    class="mt-2 d-none alert alert-danger bg-danger bg-opacity-25 border-danger border-opacity-25 text-white py-2 px-3 small">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>DP tidak boleh melebihi harga jual!
                </div>

            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(2)">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
        <div class="nav-right">
            <span class="step-hint">Minimal 1 item di keranjang sebelum checkout</span>
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(3)">
                Lanjut ke Data Tambahan <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>