<div class="modal fade" id="markupModal" tabindex="-1" aria-labelledby="markupModalLabel" aria-true="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary bg-gradient text-white border-bottom-0 py-3">
                <h5 class="modal-title fw-bold" id="markupModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Penyesuaian Data Transaksi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <form id="markupForm">
                    @csrf
                    <input type="hidden" id="markup_trx_id" name="trx_id">

                    <div class="card border shadow-sm mb-4" style="border-radius: 10px;">
                        <div class="card-header bg-light fw-bold text-dark border-bottom py-2">
                            <i class="bi bi-cart3 me-2 text-primary"></i>Item Transaksi
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="markupItemsTable">
                                    <thead class="table-light text-muted small uppercase">
                                        <tr>
                                            <th class="ps-3">Nama Produk</th>
                                            <th class="text-center" width="100">Qty</th>
                                            <th class="text-end" width="200">Harga Satuan (Rp)</th>
                                            <th class="text-end pe-3" width="200">Subtotal (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border shadow-sm h-100" style="border-radius: 10px;">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">
                                        <i class="bi bi-tags me-1 text-primary"></i> Rincian Harga
                                    </h6>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-muted mb-1">Total Harga
                                            (Kotor)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                                            <input type="text"
                                                class="form-control bg-white border-start-0 text-end fw-semibold money-input"
                                                id="markup_harga_jual" name="harga_jual"
                                                oninput="calculateMarkup(true)">
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-muted mb-1">Potongan / Diskon
                                            Tambahan</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white text-danger border-end-0">- Rp</span>
                                            <input type="text"
                                                class="form-control border-start-0 ps-0 text-end money-input"
                                                id="markup_potongan" name="potongan" oninput="calculateMarkup()">
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-muted mb-1">Potongan
                                            BPJS</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white text-success border-end-0">-
                                                Rp</span>
                                            <input type="text"
                                                class="form-control border-start-0 ps-0 text-end money-input"
                                                id="markup_potongan_bpjs" name="potongan_bpjs"
                                                oninput="calculateMarkup()">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-primary border-opacity-25 shadow-sm h-100 bg-light bg-opacity-50"
                                style="border-radius: 10px;">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">
                                        <i class="bi bi-credit-card me-1"></i> Pembayaran
                                    </h6>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-primary mb-1">Total Bayar
                                            Akhir</label>
                                        <div class="input-group input-group-sm">
                                            <span
                                                class="input-group-text bg-white text-primary border-end-0 fw-bold">Rp</span>
                                            <input type="text"
                                                class="form-control border-start-0 ps-0 bg-white fw-bold text-dark text-end money-input"
                                                id="markup_total_bayar" name="total_bayar"
                                                oninput="calculateMarkup(false, true)">
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-muted mb-1">Total
                                            Dibayar</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                                            <input type="text"
                                                class="form-control border-start-0 ps-0 text-end money-input"
                                                id="markup_dp" name="dp" oninput="calculateMarkup()">
                                            <button type="button" class="btn btn-primary fw-semibold px-3"
                                                id="btn-lunasi" onclick="actionLunasi()">Lunasi</button>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold text-muted mb-1">Sisa Tagihan</label>
                                        <div class="input-group input-group-sm">
                                            <span
                                                class="input-group-text bg-white border-end-0 text-danger fw-semibold">Rp</span>
                                            <input type="text"
                                                class="form-control bg-white text-danger fw-bold text-end money-input"
                                                id="markup_sisa" name="sisa"
                                                oninput="calculateMarkup(false, false, true)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
                <div style="max-width: 320px; width: 100%;">
                    <select class="form-select form-select-sm border-primary border-opacity-50 fw-semibold text-dark"
                        id="markup_save_mode" onchange="updateSaveModeUI()" style="border-radius: 6px;">
                        <option value="temporary" selected>🔹 Simpan Sementara (Hanya Ubah Struk)</option>
                        <option value="permanent">🔺 Simpan Permanen (Ubah Database Utama)</option>
                    </select>
                </div>

                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 fw-semibold me-2"
                        data-bs-dismiss="modal" style="border-radius: 6px;">Batal</button>
                    <button type="button" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm" id="btn-save-markup"
                        onclick="saveMarkup()" style="border-radius: 6px;">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Penyesuaian
                    </button>
                </div>
            </div>

            <div class="bg-white border-top px-4 py-2 small text-muted" id="markup-explanation-container"
                style="background-color: #fafafa !important;">
                <div id="explanation-temporary" class="d-flex align-items-start">
                    <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
                    <span><strong>Mode Sementara aktif:</strong> Perubahan hanya akan diubah tanpa disimpan ke
                        Database.</span>
                </div>
                <div id="explanation-permanent" class="d-flex align-items-start d-none">
                    <i class="bi bi-exclamation-triangle text-danger me-2 mt-1"></i>
                    <span><strong class="text-danger">Mode Permanen aktif!</strong> Sistem akan langsung melakukan
                        perubahan data di laporan penjualan. <strong>Pastikan data valid!</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let markupItems = [];
    // State flag untuk mendeteksi jika user melakukan pengetikan manual bypass pada harga kotor
    let isHargaJualManuallyEdited = false;
    let isTotalBayarManuallyEdited = false;
    let isSisaManuallyEdited = false;

    function formatRupiah(angka, prefix) {
        if (angka === undefined || angka === null) return '';
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    function parseRupiah(rupiahStr) {
        if (!rupiahStr) return 0;
        return parseInt(rupiahStr.toString().replace(/\./g, '')) || 0;
    }

    // Money input formatter runtime element hooks
    document.addEventListener('input', function (e) {
        if (e.target && e.target.classList.contains('money-input')) {
            let val = e.target.value.replace(/\./g, '');
            if (!isNaN(val) && val !== '') {
                e.target.value = formatRupiah(val);
            }
        }
    });

    function updateSaveModeUI() {
        const mode = document.getElementById('markup_save_mode').value;
        const btnSave = document.getElementById('btn-save-markup');
        const expTemp = document.getElementById('explanation-temporary');
        const expPerm = document.getElementById('explanation-permanent');

        if (mode === 'permanent') {
            btnSave.className = "btn btn-sm btn-danger px-4 fw-semibold shadow-sm";
            btnSave.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Simpan Permanen';
            expTemp.classList.add('d-none');
            expPerm.classList.remove('d-none');
        } else {
            btnSave.className = "btn btn-sm btn-primary px-4 fw-semibold shadow-sm";
            btnSave.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan Penyesuaian';
            expPerm.classList.add('d-none');
            expTemp.classList.remove('d-none');
        }
    }

    function openMarkupModal(trxId, markupData, originalItems, originalHargaJual, originalDp, originalSisa, originalPotongan, originalPotonganBpjs, originalTotalBayar) {
        document.getElementById('markup_trx_id').value = trxId;

        document.getElementById('markup_save_mode').value = 'temporary';
        updateSaveModeUI();

        // Reset state pelacak manual edit saat modal dibuka kembali
        isHargaJualManuallyEdited = false;
        isTotalBayarManuallyEdited = false;
        isSisaManuallyEdited = false;

        let useMarkup = markupData && markupData.items && markupData.items.length > 0;
        markupItems = useMarkup ? markupData.items : originalItems;

        if (!useMarkup) {
            markupItems = markupItems.map(item => ({
                product_id: item.product_id,
                nama_produk: item.nama_produk,
                qty: parseInt(item.qty) || 0,
                harga_satuan: parseInt(item.harga_satuan) || 0,
                subtotal: parseInt(item.subtotal) || 0
            }));
        }

        document.getElementById('markup_harga_jual').value = formatRupiah(useMarkup ? markupData.harga_jual : originalHargaJual);
        document.getElementById('markup_potongan').value = formatRupiah(useMarkup ? markupData.potongan : originalPotongan);
        document.getElementById('markup_potongan_bpjs').value = formatRupiah(useMarkup ? markupData.potongan_bpjs : originalPotonganBpjs);
        document.getElementById('markup_dp').value = formatRupiah(useMarkup ? markupData.dp : originalDp);
        document.getElementById('markup_total_bayar').value = formatRupiah(useMarkup ? markupData.total_bayar : originalTotalBayar);
        document.getElementById('markup_sisa').value = formatRupiah(useMarkup ? markupData.sisa : originalSisa);

        renderMarkupItems();

        // Jika data pemulihan memiliki total kotor berbeda dari kalkulasi item, tandai sebagai manual edited langsung
        let totalItemCalc = markupItems.reduce((sum, item) => sum + (parseInt(item.subtotal) || 0), 0);
        if (parseRupiah(document.getElementById('markup_harga_jual').value) !== totalItemCalc) {
            isHargaJualManuallyEdited = true;
        }

        calculateMarkup();

        new bootstrap.Modal(document.getElementById('markupModal')).show();
    }

    function renderMarkupItems() {
        const tbody = document.querySelector('#markupItemsTable tbody');
        tbody.innerHTML = '';

        markupItems.forEach((item, index) => {
            const tr = document.createElement('tr');
            const currentQty = parseInt(item.qty) || 0;
            const currentHarga = parseInt(item.harga_satuan) || 0;
            const currentSubtotal = currentQty * currentHarga;

            tr.innerHTML = `
                <td class="align-middle fw-semibold ps-3 text-dark">${item.nama_produk}</td>
                <td class="align-middle text-center text-secondary">${currentQty}</td>
                <td>
                    <div class="input-group input-group-sm ms-auto" style="max-width: 160px;">
                        <span class="input-group-text bg-white border-end-0 text-muted small">Rp</span>
                        <input type="text" class="form-control border-start-0 money-input text-end fw-semibold text-dark" value="${formatRupiah(currentHarga)}" oninput="updateItemPrice(${index}, this.value)">
                    </div>
                </td>
                <td class="align-middle text-end fw-bold pe-3 text-dark">Rp <span id="item_subtotal_${index}">${formatRupiah(currentSubtotal)}</span></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updateItemPrice(index, priceStr) {
        const price = parseRupiah(priceStr);
        const qty = parseInt(markupItems[index].qty) || 0;

        markupItems[index].harga_satuan = price;
        markupItems[index].subtotal = qty * price;

        document.getElementById(`item_subtotal_${index}`).innerText = formatRupiah(markupItems[index].subtotal);

        // Hanya update kolom total harga kotor dari item jikalau user BELUM PERNAH mengetik nominal kustom mandiri
        if (!isHargaJualManuallyEdited) {
            let totalHarga = markupItems.reduce((sum, item) => sum + (parseInt(item.subtotal) || 0), 0);
            document.getElementById('markup_harga_jual').value = formatRupiah(totalHarga);
        }

        calculateMarkup();
    }

    // Parameter flag dipasang untuk membedakan asal perubahan target element input
    function calculateMarkup(fromHargaJual = false, fromTotalBayar = false, fromSisa = false) {
        if (fromHargaJual) isHargaJualManuallyEdited = true;
        if (fromTotalBayar) isTotalBayarManuallyEdited = true;
        if (fromSisa) isSisaManuallyEdited = true;

        const totalHarga = parseRupiah(document.getElementById('markup_harga_jual').value);
        const potongan = parseRupiah(document.getElementById('markup_potongan').value);
        const potonganBpjs = parseRupiah(document.getElementById('markup_potongan_bpjs').value);
        const dp = parseRupiah(document.getElementById('markup_dp').value);

        let totalBayar = parseRupiah(document.getElementById('markup_total_bayar').value);

        // Hitung total bayar otomatis jika kolom Total Bayar Akhir tidak sedang di-override manual oleh user
        if (!isTotalBayarManuallyEdited) {
            totalBayar = Math.max(0, totalHarga - potongan - potonganBpjs);
            document.getElementById('markup_total_bayar').value = formatRupiah(totalBayar);
        }

        let sisa = parseRupiah(document.getElementById('markup_sisa').value);

        // Hitung sisa otomatis jika kolom Sisa tidak sedang di-override manual oleh user
        if (!isSisaManuallyEdited) {
            sisa = Math.max(0, totalBayar - dp);
            document.getElementById('markup_sisa').value = formatRupiah(sisa);
        }
    }

    function actionLunasi() {
        // Otomatis tandai sisa tidak diubah manual lagi agar mengikuti kalkulasi pelunasan baru
        isSisaManuallyEdited = false;
        const totalBayarAkhirStr = document.getElementById('markup_total_bayar').value;
        document.getElementById('markup_dp').value = totalBayarAkhirStr;
        calculateMarkup();
    }

    function saveMarkup() {
        const trxId = document.getElementById('markup_trx_id').value;
        const btn = document.getElementById('btn-save-markup');
        const saveMode = document.getElementById('markup_save_mode').value;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Proses...';

        // Bersihkan string berformat pemisah titik ke nominal integer murni sebelum dikirim ke backend
        const data = {
            _token: document.querySelector('meta[name="csrf-token"]').content,
            save_mode: saveMode,
            items: markupItems,
            harga_jual: parseRupiah(document.getElementById('markup_harga_jual').value),
            potongan: parseRupiah(document.getElementById('markup_potongan').value),
            potongan_bpjs: parseRupiah(document.getElementById('markup_potongan_bpjs').value),
            dp: parseRupiah(document.getElementById('markup_dp').value),
            sisa: parseRupiah(document.getElementById('markup_sisa').value),
            total_bayar: parseRupiah(document.getElementById('markup_total_bayar').value),
        };

        fetch(`/transactions/${trxId}/markup`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data penyesuaian berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(res.message);
                }
            })
            .catch(err => {
                Swal.fire('Error', err.message || 'Terjadi kesalahan sistem', 'error');
                btn.disabled = false;
                updateSaveModeUI();
            });
    }
</script>