<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    /* ==========================================================
       HELPERS
    ========================================================== */
    function parseAngka(val) {
        if (!val) return 0;
        return parseInt(String(val).replace(/\./g, '').replace(',', '')) || 0;
    }
    function formatRibuan(val) {
        return new Intl.NumberFormat('id-ID').format(val);
    }
    function formatDateInput(value) {
        if (!value) return '—';
        const [year, month, day] = value.split('-');
        return day && month && year ? `${day}/${month}/${year}` : value;
    }
    /**
     * Initialize flatpickr on all .datepicker inputs
     * Uses dd/mm/yyyy display format, submits Y-m-d to server
     */
    function initAllDatepickers() {
        document.querySelectorAll('.datepicker').forEach(function (el) {
            if (el._flatpickr) return; // already initialized
            flatpickr(el, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                allowInput: true
            });
        });
    }
    // Init after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllDatepickers);
    } else {
        setTimeout(initAllDatepickers, 50);
    }

    /**
     * Set a datepicker value programmatically (for edit/fill mode)
     * This correctly updates both the hidden input and the visible alt input
     */
    function setDatepickerValue(el, dateStr) {
        if (!el) return;
        if (el._flatpickr) {
            if (dateStr) {
                el._flatpickr.setDate(dateStr, true);
            } else {
                el._flatpickr.clear();
            }
        } else {
            el.value = dateStr || '';
        }
    }

    function formatFieldText(value, fallback = '—') {
        const text = String(value || '').trim();
        return text === '' ? fallback : text;
    }
    function toast(icon, title, text = '') {
        Swal.fire({ icon, title, text, timer: 1400, showConfirmButton: false, toast: false });
    }
    function snackbar(msg, type = 'success') {
        Swal.fire({
            icon: type, title: msg, toast: true,
            position: 'bottom-end', showConfirmButton: false, timer: 2200,
            timerProgressBar: true
        });
    }

    /* ==========================================================
       WIZARD STATE
    ========================================================== */
    let currentStep = 1;
    const TOTAL_STEPS = 5;

    const STEP_LABELS = {
        1: 'Step 1: Transaksi',
        2: 'Step 2: Pasien & Resep',
        3: 'Step 3: Produk',
        4: 'Step 4: Data',
        5: 'Step 5: Checkout',
    };

    function updateStepUI(step) {
        for (let i = 1; i <= TOTAL_STEPS; i++) {
            document.getElementById('panel-step-' + i).classList.remove('is-active');
        }
        document.getElementById('panel-step-' + step).classList.add('is-active');

        for (let i = 1; i <= TOTAL_STEPS; i++) {
            const tab = document.getElementById('step-tab-' + i);
            tab.classList.remove('is-active', 'is-done');
            if (i === step) tab.classList.add('is-active');
            else if (i < step) tab.classList.add('is-done');
        }

        for (let i = 1; i < TOTAL_STEPS; i++) {
            const conn = document.getElementById('conn-' + i);
            if (conn) conn.classList.toggle('is-done', i < step);
        }

        document.getElementById('display-step-label').textContent = STEP_LABELS[step];
        document.getElementById('display-no-trx').textContent =
            document.querySelector('input[name="no_transaksi"]').value || '—';

        const focuses = {
            1: '#no_transaksi',
            2: '#ac_pasien',
            3: '#new_item_name',
            5: '#input_harga_jual',
        };
        const focusEl = document.querySelector(focuses[step]);
        if (focusEl) setTimeout(() => focusEl.focus(), 180);

        currentStep = step;

        if (step === 5) buildCheckoutSummary();
    }

    function goStep(n) {
        updateStepUI(n);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function tryGoStep(n) {
        if (n < currentStep) { goStep(n); return; }
        if (n === currentStep) return;
        let ok = true;
        for (let i = currentStep; i < n; i++) {
            if (!validateStep(i)) { ok = false; break; }
        }
        if (ok) goStep(n);
    }

    function goNextStep(fromStep) {
        if (!validateStep(fromStep)) return;

        // Auto add BPJS items if moving from Step 3 to 5 and cart is empty
        if (fromStep === 3) {
            if (document.getElementById('bpjs').checked && cart.length === 0) {
                // Cek apakah ada frame/lensa terpilih di ui bpjs
                const frameId = document.getElementById('bpjs_frame_id').value;
                const lensaId = document.getElementById('bpjs_lensa_id').value;
                if (frameId || lensaId) {
                    // Not strictly added to cart array, but let's push them to cart so summary works
                    if (frameId) {
                        cart.push({
                            type: 'Frame',
                            nama: document.getElementById('bpjs_frame_nama').value,
                            harga: parseAngka(document.getElementById('bpjs_frame_harga').value),
                            qty: 1,
                            product_id: frameId
                        });
                    }
                    if (lensaId) {
                        cart.push({
                            type: 'Lensa',
                            nama: document.getElementById('bpjs_lensa_nama').value,
                            harga: parseAngka(document.getElementById('bpjs_lensa_harga').value),
                            qty: 1,
                            product_id: lensaId
                        });
                    }
                    processCart();
                }
            }
        }

        goStep(fromStep + 1);
    }

    /* ==========================================================
       VALIDATION
       ----------------------------------------------------------
       PANDUAN TAMBAH FIELD WAJIB (MANUAL):
       1. Pastikan input di file .blade.php punya id (misal id="my_field")
       2. Tambahkan pengecekan di dalam function validateStep(step)
       3. Gunakan pattern: 
          const el = document.getElementById('my_field');
          if(!el.value) { 
             showStepError(step, 'Pesan error'); 
             el.classList.add('is-invalid'); 
             return false; 
          }
          el.classList.remove('is-invalid');
    ========================================================== */
    function validateStep(step) {

        if (step === 4) {
            hideStepError(4);
            const fields = [
                { id: 'tgl_selesai_janji', label: 'Tanggal Selesai Janji' }
            ];

            for (const f of fields) {
                const el = document.getElementById(f.id);
                if (!el.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: `${f.label} wajib diisi.`,
                        confirmButtonText: 'Baik, saya perbaiki'
                    });
                    el.focus();
                    el.classList.add('is-invalid');
                    return false;
                }
                el.classList.remove('is-invalid');
            }
            return true;
        }

        if (step === 1) {
            const noTrxEl = document.querySelector('input[name="no_transaksi"]');
            const noTrx = noTrxEl.value.trim();
            const tglEl = document.getElementById('tgl_faktur');
            const tgl = tglEl.value;

            if (!noTrx) {
                Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'No Faktur tidak boleh kosong.', confirmButtonText: 'Baik, saya perbaiki' });
                noTrxEl.focus();
                noTrxEl.classList.add('is-invalid');
                return false;
            }
            noTrxEl.classList.remove('is-invalid');

            if (!tgl) {
                Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Tanggal faktur tidak boleh kosong.', confirmButtonText: 'Baik, saya perbaiki' });
                tglEl.focus();
                tglEl.classList.add('is-invalid');
                return false;
            }
            tglEl.classList.remove('is-invalid');
            return true;
        }

        if (step === 2) {
            const dokterEl = document.getElementById('nama_dokter');
            const dokter = dokterEl.value.trim();
            if (!dokter) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Nama dokter / klinik penulis resep wajib diisi.',
                    confirmButtonText: 'Baik, saya perbaiki'
                });
                dokterEl.focus();
                dokterEl.classList.add('is-invalid');
                return false;
            }
            dokterEl.classList.remove('is-invalid');

            const nikEl = document.getElementById('nik');
            const nik = nikEl ? nikEl.value.trim() : '';
            if (nik && !/^\d{16}$/.test(nik)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'NIK Tidak Valid',
                    text: 'Nomor Induk Kependudukan (NIK) harus terdiri dari 16 digit angka.',
                    confirmButtonText: 'Baik, saya perbaiki'
                });
                nikEl.classList.add('is-invalid');
                nikEl.focus();
                return false;
            }
            if (nikEl) nikEl.classList.remove('is-invalid');

            const isBpjs = document.getElementById('bpjs') ? document.getElementById('bpjs').checked : false;
            const noBpjsEl = document.getElementById('no_bpjs');
            const no_bpjs = noBpjsEl ? noBpjsEl.value.trim() : '';
            if (isBpjs && no_bpjs && !/^\d{13}$/.test(no_bpjs)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No BPJS Tidak Valid',
                    text: 'Nomor BPJS harus terdiri dari 13 digit angka.',
                    confirmButtonText: 'Baik, saya perbaiki'
                });
                noBpjsEl.classList.add('is-invalid');
                noBpjsEl.focus();
                return false;
            }
            if (noBpjsEl) noBpjsEl.classList.remove('is-invalid');

            const telpEl = document.querySelector('input[name="telp"]');
            const telp = telpEl ? telpEl.value.trim() : '';
            if (telp && !/^\d{9,15}$/.test(telp)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Telepon Tidak Valid',
                    text: 'Nomor telepon harus berupa angka antara 9 hingga 15 digit.',
                    confirmButtonText: 'Baik, saya perbaiki'
                });
                telpEl.classList.add('is-invalid');
                telpEl.focus();
                return false;
            }
            if (telpEl) telpEl.classList.remove('is-invalid');

            return true;
        }

        if (step === 3) {
            const isBpjs = document.getElementById('bpjs').checked;
            if (isBpjs) {
                const hasFrame = document.getElementById('bpjs_frame_id').value || document.getElementById('bpjs_frame_nama').value;
                const hasLensa = document.getElementById('bpjs_lensa_id').value || document.getElementById('bpjs_lensa_nama').value;
                if (!hasFrame && !hasLensa && cart.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Produk Kosong',
                        text: 'Pilih minimal 1 Frame atau Lensa untuk pasien BPJS.',
                        confirmButtonText: 'Baik, saya pilih'
                    });
                    return false;
                }
            } else {
                if (cart.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keranjang Kosong',
                        text: 'Silakan tambahkan minimal 1 produk ke keranjang sebelum melanjutkan.',
                        confirmButtonText: 'Baik, saya tambahkan'
                    });
                    return false;
                }
            }
            return true;
        }

        if (step === 5) {
            const dp = parseAngka(document.getElementById('input_dp').value);
            const harga = parseAngka(document.getElementById('input_harga_jual').value);
            if (dp > harga && harga > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Nominal Tidak Sesuai',
                    text: 'Jumlah DP tidak boleh melebihi harga jual/total tagihan.',
                    confirmButtonText: 'Baik, saya perbaiki'
                });
                document.getElementById('input_dp').classList.add('is-invalid');
                document.getElementById('input_dp').focus();
                return false;
            }
            document.getElementById('input_dp').classList.remove('is-invalid');
            return true;
        }

        return true;
    }

    function showStepError(step, msg) {
        const el = document.getElementById('err-step-' + step);
        if (!el) return;
        document.getElementById('err-step-' + step + '-msg').textContent = msg;
        el.classList.add('show');
    }
    function hideStepError(step) {
        const el = document.getElementById('err-step-' + step);
        if (el) el.classList.remove('show');
    }

    /* ==========================================================
       TANGGAL AMBIL & TIPE FAKTUR LOGIC
    ========================================================== */
    function bindOptionEvents() {
        const rBelum = document.getElementById('belum');
        const rSudah = document.getElementById('sudah');
        const tglJanji = document.getElementById('tgl_selesai_janji');
        const tglOrder = document.getElementById('tgl_order');
        const tglDiambil = document.getElementById('tgl_diambil');
        const tglSelesaiFaset = document.getElementById('tgl_selesai_faset');
        const tglLensaDatang = document.getElementById('tgl_lensa_datang');
        const tglFaset = document.getElementById('tgl_faset');

        // Group A: Janji = Diambil, Selesai Faset = Janji - 1
        // const syncGroupA = (val) => {
        //     if (!val) return;
        //     const d = new Date(val);
        //     tglJanji.value = val;
        //     tglDiambil.value = val;
        //     d.setDate(d.getDate() - 1);
        //     tglSelesaiFaset.value = d.toISOString().split('T')[0];
        // };

        // Group B: Order = Lensa Datang = Faset
        // const syncGroupB = (val) => {
        //     if (!val) return;
        //     tglOrder.value = val;
        //     tglLensaDatang.value = val;
        //     tglFaset.value = val;
        // };

        const toggleAmbil = () => {
            if (rSudah.checked) {
                // Jika status "Sudah Diambil", isi tanggal hari ini jika kosong
                if (!tglDiambil.value) {
                    const today = new Date();
                    const y = today.getFullYear();
                    const m = String(today.getMonth() + 1).padStart(2, '0');
                    const d = String(today.getDate()).padStart(2, '0');
                    setDatepickerValue(tglDiambil, `${y}-${m}-${d}`);
                }
            } else {
                setDatepickerValue(tglDiambil, '');
            }
        };

        rBelum.addEventListener('change', toggleAmbil);
        rSudah.addEventListener('change', toggleAmbil);

        // tglJanji.addEventListener('change', (e) => syncGroupA(e.target.value));
        // tglDiambil.addEventListener('change', (e) => syncGroupA(e.target.value));
        // tglSelesaiFaset.addEventListener('change', (e) => {
        //     if (!e.target.value) return;
        //     const d = new Date(e.target.value);
        //     d.setDate(d.getDate() + 1);
        //     const nextDay = d.toISOString().split('T')[0];
        //     tglJanji.value = nextDay;
        //     tglDiambil.value = nextDay;
        // });

        // tglOrder.addEventListener('change', (e) => {
        //     syncGroupB(e.target.value);
        //     if (rSudah.checked) syncGroupA(e.target.value);
        // });
        // tglLensaDatang.addEventListener('change', (e) => syncGroupB(e.target.value));
        // tglFaset.addEventListener('change', (e) => syncGroupB(e.target.value));

        const rUmum = document.getElementById('tunai');
        const rBpjs = document.getElementById('bpjs');

        const uiBpjs = document.getElementById('bpjs-product-container');
        const uiUmum = document.getElementById('umum-product-container');
        const contNoBpjs = document.getElementById('no_bpjs_container');
        const printBpjs = document.getElementById('btn-print-bpjs-container');

        // Potongan labels
        const cPotongan = document.getElementById('container_potongan');
        const cPotonganBpjs = document.getElementById('container_potongan_bpjs');
        const lblSelisih = document.getElementById('lbl_selisih_bpjs');

        const contTipeBpjs = document.getElementById('tipe_bpjs_container');

        const toggleFakturType = () => {
            if (rBpjs.checked) {
                uiBpjs.classList.remove('d-none');
                uiUmum.classList.add('d-none');
                contNoBpjs.style.display = 'block';
                contTipeBpjs.style.display = 'block';
                printBpjs.classList.remove('d-none');

                cPotongan.classList.add('d-none');
                cPotonganBpjs.classList.remove('d-none');
                lblSelisih.classList.remove('d-none');

                // Auto-fill potongan BPJS dari tipe_bpjs yang sudah dipilih
                applyPotonganBpjs();
            } else {
                uiBpjs.classList.add('d-none');
                uiUmum.classList.remove('d-none');
                contNoBpjs.style.display = 'none';
                contTipeBpjs.style.display = 'none';
                printBpjs.classList.add('d-none');

                cPotongan.classList.remove('d-none');
                cPotonganBpjs.classList.add('d-none');
                lblSelisih.classList.add('d-none');

                // Reset potongan BPJS saat mode Umum
                applyPotonganBpjs();
            }
        };

        let lastFakturType = rBpjs.checked ? '2' : '1';

        const handleFakturChange = (e) => {
            const newVal = e.target.value;
            if (cart.length > 0 && newVal !== lastFakturType) {
                Swal.fire({
                    title: 'Ubah Jenis Transaksi?',
                    text: 'Pesanan pada produk akan dikosongkan karena formatnya berbeda.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Ubah & Kosongkan',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) {
                        cart = [];
                        clearBpjs('Frame');
                        clearBpjs('Lensa');
                        processCart();
                        lastFakturType = newVal;
                        toggleFakturType();
                    } else {
                        // Revert radio
                        if (lastFakturType === '1') { rUmum.checked = true; }
                        else { rBpjs.checked = true; }
                    }
                });
            } else {
                lastFakturType = newVal;
                toggleFakturType();
            }
        };

        rUmum.addEventListener('change', handleFakturChange);
        rBpjs.addEventListener('change', handleFakturChange);

        toggleAmbil();
        toggleFakturType();

        // Listener: saat tipe_bpjs berubah, otomatis isi potongan BPJS
        document.getElementById('tipe_bpjs').addEventListener('change', function () {
            applyPotonganBpjs();
        });
    }

    /* ==========================================================
       AUTO-FILL POTONGAN BPJS BERDASARKAN TIPE/KELAS
       Kelas 1 = 330.000, Kelas 2 = 220.000, Kelas 3 = 165.000
       Hanya aktif jika tipe faktur = BPJS
    ========================================================== */
    function applyPotonganBpjs() {
        const isBpjs = document.getElementById('bpjs').checked;
        const tipeBpjs = document.getElementById('tipe_bpjs').value;
        const inputPotonganBpjs = document.getElementById('input_potongan_bpjs');

        const mapping = {
            '1': 330000,
            '2': 220000,
            '3': 165000,
        };

        // Hanya isi potongan jika mode BPJS aktif, selain itu 0
        const potongan = isBpjs ? (mapping[tipeBpjs] || 0) : 0;
        inputPotonganBpjs.value = formatRibuan(potongan);
        calculateSisa();
    }

    /* ==========================================================
       AUTOCOMPLETE ENGINE
    ========================================================== */
    function setupAC(inp, dd, url, mapFn, onSelect) {
        let timer = null;
        inp.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 1) {
                dd.classList.add('d-none');
                // Reset product_id if the input is cleared
                if (inp.id === 'ac_produk_new') {
                    const pid = document.querySelector('input[name="product_id[]"]');
                    if (pid) pid.value = '';
                }
                return;
            }
            timer = setTimeout(() => {
                const separator = url.includes('?') ? '&' : '?';
                fetch(`${url}${separator}q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) {
                            dd.innerHTML = '<div class="ac-item text-muted">Tidak ditemukan</div>';
                        } else {
                            dd.innerHTML = data.slice(0, 8).map(item =>
                                `<div class="ac-item" data-item='${JSON.stringify(item).replace(/'/g, "&#39;")}'>${mapFn(item)}</div>`
                            ).join('');
                            dd.querySelectorAll('.ac-item[data-item]').forEach(el => {
                                el.addEventListener('click', function () {
                                    onSelect(JSON.parse(this.dataset.item));
                                    dd.classList.add('d-none');
                                });
                            });
                        }
                        dd.classList.remove('d-none');
                    })
                    .catch(() => dd.classList.add('d-none'));
            }, 280);
        });
        document.addEventListener('click', e => {
            if (!inp.contains(e.target) && !dd.contains(e.target)) dd.classList.add('d-none');
        });
        inp.addEventListener('keydown', e => {
            if (e.key === 'Escape') dd.classList.add('d-none');
        });
    }

    // Pasien Autocomplete
    setupAC(
        document.getElementById('ac_pasien'),
        document.getElementById('dd_pasien'),
        '{{ route('patients.autocomplete') }}',
        p => `<b>${p.nama}</b> <span class="text-muted small">${p.no_bpjs || ''} ${p.no_hp || ''}</span>`,
        p => {
            document.getElementById('patient_id').value = p.id;
            document.getElementById('nik').value = p.nik || '';
            document.getElementById('no_bpjs').value = p.no_bpjs || '';
            document.getElementById('tipe_bpjs').value = p.tipe_bpjs || '';
            document.getElementById('nama_pasien').value = p.nama || '';
            document.querySelector('input[name="telp"]').value = p.no_hp || '';
            document.querySelector('textarea[name="alamat"]').value = p.alamat || '';
            document.getElementById('ac_pasien').value = p.nama || '';
            document.getElementById('patient-selected-name').textContent = p.nama;
            document.getElementById('patient-selected-badge').classList.remove('d-none');
            document.getElementById('btn-load-history').style.display = '';
            // Hanya tampilkan field BPJS di step2 jika tipe faktur = BPJS
            const isBpjsMode = document.getElementById('bpjs').checked;
            document.getElementById('no_bpjs_container').style.display = (isBpjsMode && p.no_bpjs) ? '' : 'none';
            document.getElementById('tipe_bpjs_container').style.display = (isBpjsMode && p.tipe_bpjs) ? '' : 'none';

            // Auto-fill potongan BPJS jika tipe_bpjs tersedia
            applyPotonganBpjs();
            loadPatientHistory(p.id);
        }
    );

    function clearPatient() {
        document.getElementById('patient_id').value = '';
        document.getElementById('ac_pasien').value = '';
        document.getElementById('nik').value = '';
        document.getElementById('no_bpjs').value = '';
        document.getElementById('tipe_bpjs').value = '';
        document.getElementById('nama_pasien').value = '';
        document.querySelector('input[name="telp"]').value = '';
        document.querySelector('textarea[name="alamat"]').value = '';
        document.getElementById('patient-selected-badge').classList.add('d-none');
        document.getElementById('btn-load-history').style.display = 'none';
        document.getElementById('no_bpjs_container').style.display = 'none';
        document.getElementById('tipe_bpjs_container').style.display = 'none';
        document.getElementById('history-tag-container').classList.add('d-none');

        // Reset potongan BPJS
        applyPotonganBpjs();
    }

    // Produk UMUM
    setupAC(
        document.getElementById('ac_produk_new'),
        document.getElementById('dd_produk_new'),
        '{{ route('transactions.product.search') }}',
        f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''} <span class="text-muted small">${f.merek || ''} (Stok: ${f.stok})</span>`,
        f => {
            document.getElementById('new_item_name').value = f.nama || '';
            document.getElementById('new_item_seri').value = f.merek || '';
            // document.getElementById('new_item_warna').value = f.warna || ''; // warna tidak diambil di query
            document.getElementById('new_item_keterangan').value = f.keterangan || '';
            document.getElementById('new_item_harga').value = formatRibuan(parseInt(f.harga_jual) || 0);
            document.querySelector('input[name="product_id[]"]').value = f.id || '';
            document.getElementById('ac_produk_new').value = f.kode_produk || f.nama || '';
            document.getElementById('ac_produk_new').dataset.kode = f.kode_produk || '';
            document.getElementById('new_item_name').focus();

            // Auto set product type if category name is available
            if (f.category && f.category.nama) {
                let catName = f.category.nama.toLowerCase();
                if (catName.includes('frame')) setProductType('Frame');
                else if (catName.includes('lensa') && !catName.includes('kontak')) setProductType('Lensa');
                else if (catName.includes('aksesori') || catName.includes('kontak')) setProductType('Aksesoris');
                else setProductType('Lainnya');
            }
        }
    );

    function clearProductSearch() {
        document.getElementById('ac_produk_new').value = '';
        document.getElementById('new_item_name').value = '';
        document.getElementById('new_item_seri').value = '';
        document.getElementById('new_item_warna').value = '';
        document.getElementById('new_item_harga').value = '0';
        document.getElementById('new_item_keterangan').value = '';
        document.getElementById('ac_produk_new').dataset.kode = '';
        document.querySelector('input[name="product_id[]"]').value = '';
    }

    // BPJS FRAME Autocomplete
    setupAC(
        document.getElementById('bpjs_frame_ac'),
        document.getElementById('bpjs_frame_dd'),
        '{{ route('products.frame.autocomplete') }}?type=Frame',
        f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''}`,
        f => {
            document.getElementById('bpjs_frame_id').value = f.id || '';
            document.getElementById('bpjs_frame_nama').value = f.nama || '';
            document.getElementById('bpjs_frame_harga').value = formatRibuan(parseInt(f.harga_jual) || 0);
            document.getElementById('bpjs_frame_ac').value = f.kode_produk || f.nama;
            document.getElementById('bpjs_frame_ac').dataset.kode = f.kode_produk || '';
        }
    );

    // BPJS LENSA Autocomplete
    setupAC(
        document.getElementById('bpjs_lensa_ac'),
        document.getElementById('bpjs_lensa_dd'),
        '{{ route('products.frame.autocomplete') }}?type=Lensa',
        f => `<b>${f.kode_produk || ''}</b> | ${f.nama || ''}`,
        f => {
            document.getElementById('bpjs_lensa_id').value = f.id || '';
            document.getElementById('bpjs_lensa_nama').value = f.nama || '';
            document.getElementById('bpjs_lensa_harga').value = formatRibuan(parseInt(f.harga_jual) || 0);
            document.getElementById('bpjs_lensa_ac').value = f.kode_produk || f.nama;
            document.getElementById('bpjs_lensa_ac').dataset.kode = f.kode_produk || '';
            // Juga isikan ke master keterangan lensa jika mau disinkronkan
            const lensaKet = document.getElementById('lensa_ket');
            if (lensaKet) {
                lensaKet.value = f.nama;
            }
        }
    );

    function clearBpjs(type) {
        const t = type.toLowerCase();
        document.getElementById(`bpjs_${t}_ac`).value = '';
        document.getElementById(`bpjs_${t}_id`).value = '';
        document.getElementById(`bpjs_${t}_nama`).value = '';
        document.getElementById(`bpjs_${t}_harga`).value = '0';
        document.getElementById(`bpjs_${t}_ac`).dataset.kode = '';
        document.getElementById(`bpjs_${t}_status`).classList.add('d-none');
    }

    function setBpjsItem(type) {
        const t = type.toLowerCase();
        const nama = document.getElementById(`bpjs_${t}_nama`).value;
        const harga = parseAngka(document.getElementById(`bpjs_${t}_harga`).value);
        const id = document.getElementById(`bpjs_${t}_id`).value;
        const kode = document.getElementById(`bpjs_${t}_ac`).dataset.kode || '';

        if (!nama) {
            snackbar(`Nama ${type} tidak boleh kosong`, 'warning');
            return;
        }

        document.getElementById(`bpjs_${t}_status`).classList.remove('d-none');

        const existIdx = cart.findIndex(i => i.type === type && i.product_id === id && i.harga === harga && i.nama === nama);
        if (existIdx >= 0) {
            cart[existIdx].qty += 1;
        } else {
            cart.push({
                type: type,
                nama: nama,
                harga: harga,
                qty: 1,
                product_id: id,
                kode_produk: kode
            });
        }

        processCart();
        snackbar(`${type} BPJS berhasil ditambahkan!`, 'success');
    }

    /* ==========================================================
       PRODUCT TYPE SELECTOR
    ========================================================== */
    function setProductType(type) {
        document.getElementById('new_item_type').value = type;
        document.querySelectorAll('.ptype-btn').forEach(btn => {
            btn.classList.remove('active-frame', 'active-lensa', 'active-other');
            if (btn.dataset.type === type) {
                if (type === 'Frame') btn.classList.add('active-frame');
                else if (type === 'Lensa') btn.classList.add('active-lensa');
                else btn.classList.add('active-other');
            }
        });
        document.getElementById('new_item_name').focus();
    }

    /* ==========================================================
       CART
    ========================================================== */
    let cart = [];

    function getCartTypeBadge(type) {
        if (type === 'Frame') return 'ct-frame';
        if (type === 'Lensa') return 'ct-lensa';
        return 'ct-other';
    }

    function renderCartInline() {
        const body = document.getElementById('cart-inline-body');
        const badge = document.getElementById('cart-count-badge');

        const totalQty = cart.reduce((s, i) => s + i.qty, 0);
        if (badge) badge.textContent = totalQty;

        const totalEl = document.getElementById('cart-inline-total');

        if (cart.length === 0) {
            body.innerHTML = '<div class="text-center py-3 text-white-50" id="cart-empty-msg"><small>Keranjang kosong</small></div>';
            if (totalEl) totalEl.textContent = 'Rp 0';
            return;
        }

        body.innerHTML = '';
        cart.forEach((item, idx) => {
            const row = document.createElement('div');
            row.className = 'cart-item-row d-flex align-items-center mb-2 pb-2 border-bottom border-white border-opacity-10';
            row.innerHTML = `
                <span class="cart-type-badge small px-2 py-1 rounded me-2 ${getCartTypeBadge(item.type)}" style="font-size: 0.7rem;">
                    ${item.type || 'Product'}
                </span>
                <div class="cart-item-name-group flex-grow-1" style="min-width: 0;">
                    <div class="cart-item-name fw-semibold small text-truncate" title="${item.nama}">${item.nama || '—'}</div>
                    <div class="text-white-50" style="font-size: 0.75rem;">${item.seri || ''} ${item.warna ? (item.seri ? '| ' : '') + item.warna : ''}</div>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-2 small">x${item.qty}</span>
                    <span class="fw-bold small">Rp${formatRibuan(item.qty * item.harga)}</span>
                    <button class="btn btn-link btn-sm text-danger cart-item-del p-0 ms-2" style="text-decoration:none" onclick="removeCartItem(${idx})">✕</button>
                </div>
            `;
            body.appendChild(row);
        });

        const total = cart.reduce((s, i) => s + i.qty * i.harga, 0);
        if (totalEl) totalEl.textContent = 'Rp ' + formatRibuan(total);
    }

    function processCart() {
        const total = cart.reduce((s, i) => s + i.qty * i.harga, 0);
        document.getElementById('cart_data').value = JSON.stringify(cart);

        // Harga Jual selalu = total keranjang (readonly)
        inputHargaJual.value = formatRibuan(total);

        calculateSisa();
        renderCartInline();
    }

    let lastAutoHarga = 0;

    function addItemToCart() {
        const type = document.getElementById('new_item_type').value;
        const nama = document.getElementById('new_item_name').value.trim();
        const seri = document.getElementById('new_item_seri').value.trim();
        const warna = document.getElementById('new_item_warna').value.trim();
        const ket = document.getElementById('new_item_keterangan').value.trim();
        const harga = parseAngka(document.getElementById('new_item_harga').value);
        const qty = Math.max(1, parseInt(document.getElementById('new_item_qty').value) || 1);
        const prodId = document.querySelector('input[name="product_id[]"]').value;
        const kodeProduk = document.getElementById('ac_produk_new').dataset.kode || '';

        if (!nama) {
            document.getElementById('new_item_name').classList.add('is-invalid');
            document.getElementById('new_item_name').focus();
            snackbar('Nama produk wajib diisi!', 'warning');
            return;
        }
        document.getElementById('new_item_name').classList.remove('is-invalid');
        const existIdx = cart.findIndex(i =>
            i.nama === nama &&
            i.type === type &&
            i.seri === seri &&
            i.warna === warna &&
            i.harga === harga
        );
        if (existIdx >= 0) {
            cart[existIdx].qty += qty;
            snackbar(`Qty diperbarui.`, 'info');
        } else {
            cart.push({ type, nama, seri, warna, keterangan: ket, harga, qty, product_id: prodId, kode_produk: kodeProduk });
            snackbar(`Ditambahkan ke keranjang.`, 'success');
        }
        console.log("Data yang akan dimasukkan ke cart:", {
            type: type, // Ini adalah variabel type yang kita tentukan tadi
            nama: nama
        });

        // Lihat isi keranjang saat ini
        console.log("Isi cart saat ini:", cart);
        processCart();
        clearProductSearch();
        document.getElementById('new_item_qty').value = '1';

        if (type === 'Frame' && !cart.some(i => i.type === 'Lensa')) {
            setProductType('Lensa');
        } else {
            document.getElementById('new_item_name').focus();
        }
    }

    function removeCartItem(idx) {
        cart.splice(idx, 1);
        processCart();
    }

    /* ==========================================================
       FINANCE CALCULATIONS
    ========================================================== */
    const inputHargaJual = document.getElementById('input_harga_jual');
    const inputPotongan = document.getElementById('input_potongan');
    const inputPotonganBpjs = document.getElementById('input_potongan_bpjs');
    const inputDp = document.getElementById('input_dp');
    const inputPelunasan = document.getElementById('input_pelunasan');
    const inputSisa = document.getElementById('input_sisa');

    function calculateSisa() {
        const h = parseAngka(inputHargaJual.value);
        const p = document.getElementById('bpjs').checked ? parseAngka(inputPotonganBpjs.value) : parseAngka(inputPotongan.value);
        const d = parseAngka(inputDp.value);
        const pelunasan = parseAngka(inputPelunasan.value);
        const sisa = Math.max(0, h - p - d - pelunasan);
        inputSisa.value = formatRibuan(sisa);

        const dpWarn = document.getElementById('dp-warning');
        if (d + pelunasan > h && h > 0) dpWarn.classList.remove('d-none');
        else dpWarn.classList.add('d-none');

        // Proteksi Status Ambil: Tidak bisa "Sudah Diambil" jika Sisa > 0
        const rSudah = document.getElementById('sudah');
        const rBelum = document.getElementById('belum');
        if (rSudah && rBelum) {
            if (sisa > 0) {
                rSudah.disabled = true;
                if (rSudah.checked) {
                    rBelum.checked = true;
                    // Trigger change manually to reset date fields
                    rBelum.dispatchEvent(new Event('change'));
                }
            } else {
                rSudah.disabled = false;
            }
        }
    }

    function setLunas() {
        const h = parseAngka(inputHargaJual.value);
        const p = document.getElementById('bpjs').checked ? parseAngka(inputPotonganBpjs.value) : parseAngka(inputPotongan.value);
        const d = parseAngka(inputDp.value);
        const sisaLunas = Math.max(0, h - p - d);
        inputPelunasan.value = formatRibuan(sisaLunas);
        calculateSisa();
    }

    [inputPotongan, inputPotonganBpjs, inputDp, inputPelunasan].forEach(el => {
        if (!el) return;
        el.addEventListener('input', function () {
            this.value = formatRibuan(parseAngka(this.value));
            calculateSisa();
            if (currentStep === 5) buildCheckoutSummary();
        });
    });

    // Formatting Ribuan untuk Input Harga Satuan / Harga Beli (Step 3)
    ['new_item_harga', 'bpjs_frame_harga', 'bpjs_lensa_harga'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function () {
                this.value = formatRibuan(parseAngka(this.value));
            });
        }
    });

    function buildCheckoutSummary() {
        const noTrx = formatFieldText(document.querySelector('input[name="no_transaksi"]')?.value, '—');
        const tglFaktur = formatDateInput(document.getElementById('tgl_faktur')?.value);
        const typeFaktur = document.getElementById('bpjs')?.checked ? 'BPJS' : 'Umum';
        const namaPas = formatFieldText(document.getElementById('nama_pasien')?.value, 'UMUM');
        const telp = formatFieldText(document.querySelector('input[name="telp"]')?.value, '—');
        const nikEl = document.getElementById('nik');
        const nik = nikEl ? formatFieldText(nikEl.value, '—') : '—';
        const noBpjsEl = document.getElementById('no_bpjs');
        const tipeBpjsEl = document.getElementById('tipe_bpjs');
        const noBpjs = noBpjsEl ? formatFieldText(noBpjsEl.value, '—') : '—';
        const tipeBpjs = tipeBpjsEl ? formatFieldText(tipeBpjsEl.value, '—') : '—';
        const alamat = formatFieldText(document.querySelector('textarea[name="alamat"]')?.value, '—');
        const namaDokter = formatFieldText(document.getElementById('nama_dokter')?.value, '—');
        const tglResep = formatDateInput(document.querySelector('input[name="tgl_resep"]')?.value);
        const tglRefraksi = formatDateInput(document.querySelector('input[name="tgl_refraksi"]')?.value);
        const catatanResep = formatFieldText(document.querySelector('textarea[name="catatan_resep"]')?.value, '—');
        const lensaKet = formatFieldText(document.getElementById('lensa_ket')?.value, '—');
        const ketFrame = formatFieldText(document.querySelector('input[name="keterangan_frame"]')?.value, '—');

        const odSc = formatFieldText(document.getElementById('og_od_sc')?.value, '—');
        const odSph = formatFieldText(document.getElementById('og_od_sph')?.value, '—');
        const odCyl = formatFieldText(document.getElementById('og_od_cyl')?.value, '—');
        const odAxis = formatFieldText(document.getElementById('og_od_axis')?.value, '—');
        const odAdd = formatFieldText(document.getElementById('og_od_add')?.value, '—');
        const odMpd = formatFieldText(document.getElementById('og_od_mpd')?.value, '—');
        const odPrism = formatFieldText(document.getElementById('og_od_prism')?.value, '—');
        const odCc = formatFieldText(document.getElementById('og_od_cc')?.value, '—');

        const osSc = formatFieldText(document.getElementById('og_os_sc')?.value, '—');
        const osSph = formatFieldText(document.getElementById('og_os_sph')?.value, '—');
        const osCyl = formatFieldText(document.getElementById('og_os_cyl')?.value, '—');
        const osAxis = formatFieldText(document.getElementById('og_os_axis')?.value, '—');
        const osAdd = formatFieldText(document.getElementById('og_os_add')?.value, '—');
        const osMpd = formatFieldText(document.getElementById('og_os_mpd')?.value, '—');
        const osPrism = formatFieldText(document.getElementById('og_os_prism')?.value, '—');
        const osCc = formatFieldText(document.getElementById('og_os_cc')?.value, '—');

        const lab = formatFieldText(document.querySelector('input[name="lab"]')?.value, '—');
        const tglOrder = formatDateInput(document.querySelector('input[name="tgl_order"]')?.value);
        const tglLensaDatang = formatDateInput(document.querySelector('input[name="tgl_lensa_datang"]')?.value);
        const tglFaset = formatDateInput(document.querySelector('input[name="tgl_faset"]')?.value);
        const tempatFaset = formatFieldText(document.querySelector('input[name="tempat_faset"]')?.value, '—');
        const tglSelesaiFaset = formatDateInput(document.querySelector('input[name="tgl_selesai_faset"]')?.value);
        const tglJanji = formatDateInput(document.querySelector('input[name="tgl_selesai_janji"]')?.value);
        const diambil = document.querySelector('input[name="diambil"]:checked')?.value === '1' ? 'Sudah' : 'Belum';
        const tglDiambil = formatDateInput(document.querySelector('input[name="tgl_diambil"]')?.value);
        const catatan = formatFieldText(document.querySelector('textarea[name="catatan"]')?.value, '—');

        document.getElementById('checkout-summary-badges').innerHTML = `
        <div class="col-auto"><span class="badge bg-primary-subtle text-primary py-1">${noTrx}</span></div>
        <div class="col-auto"><span class="badge bg-success-subtle text-success py-1"><i class="bi bi-person me-1"></i>${namaPas}</span></div>
        <div class="col-auto"><span class="badge bg-secondary-subtle text-secondary py-1"><i class="bi bi-calendar-event me-1"></i>${typeFaktur}</span></div>
    `;

        document.getElementById('checkout-preview-invoice').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="text-muted small">No Faktur</div>
                    <div class="fw-semibold">${noTrx}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tanggal Faktur</div>
                    <div>${tglFaktur}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Tipe Transaksi</div>
                    <div>${typeFaktur}</div>
                </div>
                
            </div>
        </div>
    `;

        document.getElementById('checkout-preview-patient').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Data Pasien & Resep</div>
            <div class="row g-2">
                <div class="col-sm-3"><strong>Nama Pasien</strong><div>${namaPas}</div></div>
                <div class="col-sm-3"><strong>No. Telp</strong><div>${telp}</div></div>
                <div class="col-sm-3"><strong>No. NIK</strong><div>${nik}</div></div>
                ${typeFaktur.toLowerCase() === 'bpjs' ? `<div class="col-sm-3"><strong>No. BPJS</strong><div>${noBpjs}</div></div>` : ''}
                <div class="col-sm-3"><strong>Alamat</strong><div>${alamat}</div></div>
                <div class="col-sm-3"><strong>Dokter / Klinik</strong><div>${namaDokter}</div></div>
                <div class="col-sm-3"><strong>Tanggal Resep</strong><div>${tglResep}</div></div>
                <div class="col-sm-3"><strong>Tgl Refraksi</strong><div>${tglRefraksi}</div></div>
                <div class="col-sm-3"><strong>Catatan Resep</strong><div>${catatanResep}</div></div>
            </div>
        </div>
    `;

        document.getElementById('checkout-preview-resep').innerHTML = `
        <div class="border rounded-3 p-3 bg-white shadow-sm">
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-eye me-2 text-primary"></i>
        <div class="fw-bold text-dark">Ukuran kacamata & Keterangan Lensa</div>
    </div>

    <!-- Tabel Refraksi -->
    <div class="table-responsive">
        <table class="table table-sm table-bordered text-center align-middle mb-3" style="font-size: 0.85rem;">
            <thead class="bg-light text-muted">
                <tr>
                    <th class="fw-semibold">EYE</th>
                    <th class="fw-semibold">SPH</th>
                    <th class="fw-semibold">CYL</th>
                    <th class="fw-semibold">AXIS</th>
                    <th class="fw-semibold">ADD</th>
                    <th class="fw-semibold">MPD</th>
                    <th class="fw-semibold">PRISM</th>
                    <th class="fw-semibold">CC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold bg-light">OD</td>
                    <td >${odSph || '-'}</td>
                    <td>${odCyl || '-'}</td>
                    <td>${odAxis || '-'}</td>
                    <td>${odAdd || '-'}</td>
                    <td>${odMpd || '-'}</td>
                    <td>${odPrism || '-'}</td>
                    <td>${odCc || '-'}</td>
                </tr>
                <tr>
                    <td class="fw-bold bg-light">OS</td>
                    <td >${osSph || '-'}</td>
                    <td>${osCyl || '-'}</td>
                    <td>${osAxis || '-'}</td>
                    <td>${osAdd || '-'}</td>
                    <td>${osMpd || '-'}</td>
                    <td>${osPrism || '-'}</td>
                    <td>${osCc || '-'}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Keterangan Tambahan -->
    <div class="row g-3 border-top pt-3">
            <div class="col-sm-12">
            <div class="text-muted small mb-1"><i class="bi bi-card-text me-1"></i>Keterangan</div>
            <div class="fw-semibold p-2 bg-light rounded" style="min-height: 38px;">${ketFrame || '-'}</div>
        </div>
    </div>
</div>
    `;

        const listEl = document.getElementById('checkout-item-list');
        if (cart.length === 0) {
            listEl.innerHTML = '<div class="text-muted text-center small py-3">Tidak ada item di keranjang.</div>';
        } else {
            listEl.innerHTML = `
            <div class="mb-2 text-muted small">Produk yang dipilih</div>
            ${cart.map(item => `
                <div class="d-flex align-items-start gap-2 py-2 border-bottom" style="font-size:0.88rem">
                    <div class="cart-type-badge ${getCartTypeBadge(item.type)}" style="font-size:0.65rem">${item.type}</div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${item.nama}</div>
                        <div class="text-muted" style="font-size:0.82rem;">${formatFieldText(item.seri, '')}${item.warna ? ' • ' + item.warna : ''}${item.keterangan ? ' • ' + item.keterangan : ''}</div>
                    </div>
                    <div class="text-end" style="min-width:96px;">
                        <div class="text-muted small">×${item.qty}</div>
                        <strong>Rp ${formatRibuan(item.qty * item.harga)}</strong>
                    </div>
                </div>
            `).join('')}
        `;
        }

        const cartTotal = cart.reduce((s, i) => s + i.qty * i.harga, 0);
        const hargaJual = parseAngka(inputHargaJual.value);
        const potongan = parseAngka(inputPotongan.value);
        const potonganBpjs = parseAngka(inputPotonganBpjs.value);
        const dp = parseAngka(inputDp.value);
        const pelunasan = parseAngka(inputPelunasan.value);
        const sisa = parseAngka(inputSisa.value);

        document.getElementById('checkout-preview-payments').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Rincian Pembayaran</div>
            <div class="d-flex justify-content-between flex-wrap row g-2">
                <div class="col-3"><strong>Harga Jual</strong><div>Rp ${formatRibuan(hargaJual)}</div></div>
                ${typeFaktur.toLowerCase() === 'bpjs' ? `<div class="col-3"><strong>Potongan BPJS</strong><div>Rp ${formatRibuan(potonganBpjs)}</div></div>` : ''}
                <div class="col-3"><strong>DP</strong><div>Rp ${formatRibuan(dp)}</div></div>
                <div class="col-3"><strong>Pelunasan</strong><div>Rp ${formatRibuan(pelunasan)}</div></div>
                <div class="col-3"><strong>Sisa Tagihan</strong><div>Rp ${formatRibuan(sisa)}</div></div>
            </div>
        </div>
    `;

        document.getElementById('checkout-preview-additional').innerHTML = `
        <div class="border rounded-3 p-3 bg-white">
            <div class="mb-2 text-muted small">Data Tambahan</div>
            <div class="row g-2">
                <div class="col-sm-4"><strong>Lab</strong><div>${lab}</div></div>
                <div class="col-sm-4"><strong>Tgl Order</strong><div>${tglOrder}</div></div>
                <div class="col-sm-4"><strong>Tgl Lensa Datang</strong><div>${tglLensaDatang}</div></div>
                <div class="col-sm-4"><strong>Tgl Faset</strong><div>${tglFaset}</div></div>
                <div class="col-sm-4"><strong>Tempat Faset</strong><div>${tempatFaset}</div></div>
                <div class="col-sm-4"><strong>Tgl Selesai Faset</strong><div>${tglSelesaiFaset}</div></div>
                <div class="col-sm-4"><strong>Tgl Janji Customer</strong><div>${tglJanji}</div></div>
                <div class="col-sm-4"><strong>Status Ambil</strong><div>${diambil}</div></div>
                <div class="col-sm-4"><strong>Tgl Diambil</strong><div>${tglDiambil}</div></div>
                <div class="col-12"><strong>Catatan</strong><div>${catatan}</div></div>
            </div>
        </div>
    `;

        document.getElementById('finance-cart-summary').innerHTML = `
        <div class="s-row"><span class="s-label">Total item keranjang</span><span class="s-val">Rp ${formatRibuan(cartTotal)}</span></div>
        <div class="s-divider"></div>
        <div class="s-row"><span class="s-label">${cart.length} item</span><span class="s-val">${cart.map(i => i.type).join(', ') || '—'}</span></div>
    `;

        if (hargaJual === 0) {
            inputHargaJual.value = formatRibuan(cartTotal);
            lastAutoHarga = cartTotal;
        }

        calculateSisa();
    }

    /* ==========================================================
       HISTORY REKAM MEDIS
    ========================================================== */
    // Store currently selected history data for copy
    let _selectedHistoryData = null;

    function loadPatientHistory(pid) {
        if (!pid) pid = document.getElementById('patient_id').value;
        if (!pid) return;

        fetch(`{{ route('patients.latest-refraction', ':pid') }}`.replace(':pid', pid))
            .then(r => r.json())
            .then(data => {
                const dropdown = document.getElementById('history_dropdown');
                if (!dropdown) return;

                dropdown.innerHTML = '<option value="">-- Buat Rekam Medis Baru (Kosong) --</option>';

                if (data.history && data.history.length > 0) {
                    data.history.forEach(rm => {
                        const opt = document.createElement('option');
                        opt.value = rm.id;
                        opt.textContent = `${rm.tanggal_kunjungan} ${rm.is_bpjs ? '(BPJS)' : '(UMUM)'} -  ${rm.dokter}`;
                        dropdown.appendChild(opt);
                    });

                    document.getElementById('history-count').textContent = `${data.history.length} Riwayat`;

                    // Auto-select latest record
                    dropdown.value = data.history[0].id;
                    dropdown.dispatchEvent(new Event('change'));
                } else {
                    document.getElementById('history-count').textContent = `0 Riwayat`;
                    dropdown.value = "";
                    dropdown.dispatchEvent(new Event('change'));

                    Swal.fire({
                        icon: 'info',
                        title: 'Rekam Medis Kosong',
                        text: 'Pasien ini belum memiliki riwayat rekam medis.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => console.error('Error loading history:', err));
    }



    // --- Refraction ---


    function copyOdToOs() {
        ['sph', 'cyl', 'axis', 'add', 'mpd', 'prism'].forEach(f => {
            const od = document.getElementById('od_' + f);
            const os = document.getElementById('os_' + f);
            if (od && os) os.value = od.value;
        });
        snackbar('Disalin ke OS', 'info');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('diagnosis_search');
        const resultBox = document.getElementById('diagnosis_result');

        if (!input || !resultBox) return;

        let timeout = null;

        input.addEventListener('keyup', function () {
            const query = this.value;

            clearTimeout(timeout);

            if (query.length < 2) {
                resultBox.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('diagnoses.search') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        resultBox.innerHTML = '';

                        data.forEach(item => {
                            const el = document.createElement('a');
                            el.href = '#';
                            el.classList.add('list-group-item', 'list-group-item-action');
                            el.textContent = `${item.name}`;

                            el.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = `${item.name}`;
                                resultBox.innerHTML = '';
                            });

                            resultBox.appendChild(el);
                        });
                    })
                    .catch(() => {
                        resultBox.innerHTML = '';
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !resultBox.contains(e.target)) {
                resultBox.innerHTML = '';
            }
        });
    });

    /* ==========================================================
       SPA / EDIT LOGIC
    ========================================================== */
    function fillForm(trx) {
        if (!trx) { toast('info', 'Data tidak ditemukan.'); return; }

        document.getElementById('trx_id').value = trx.id || '';
        document.getElementById('patient_id').value = trx.patient_id || '';
        document.getElementById('display-no-trx').textContent = trx.no_transaksi || '—';

        const fm = document.getElementById('pos-form');
        const sv = (sel, val) => {
            const el = fm.querySelector(sel);
            if (el) {
                el.value = val;
                if (val) el.placeholder = val;
            }
        };

        // Parse date string safely WITHOUT timezone conversion
        // Laravel 'date' cast serializes as "2026-06-04T00:00:00.000000Z"
        // new Date() would interpret this in UTC and shift the day in GMT+7
        // Instead we split the string to extract Y-m-d directly
        const getOnlyDate = (d) => {
            if (!d || typeof d !== 'string') return '';
            // Handle ISO format: "2026-06-04T00:00:00.000000Z" or "2026-06-04T17:00:00.000000Z"
            // Just take the first 10 chars (YYYY-MM-DD) — this is the date as stored in DB
            if (d.length >= 10) return d.substring(0, 10);
            return d;
        };

        // Step 1
        sv('input[name="no_transaksi"]', trx.no_transaksi || '');
        sv('input[name="lab"]', trx.lab || '');
        sv('input[name="tempat_faset"]', trx.tempat_faset || '');
        sv('textarea[name="catatan"]', trx.catatan || '');

        // Set date fields via flatpickr API so both hidden + visible inputs update
        setDatepickerValue(document.getElementById('tgl_order'), getOnlyDate(trx.tgl_order));
        setDatepickerValue(document.getElementById('tgl_faset'), getOnlyDate(trx.tgl_faset));
        setDatepickerValue(document.getElementById('tgl_lensa_datang'), getOnlyDate(trx.tgl_datang_faset));
        setDatepickerValue(document.getElementById('tgl_selesai_faset'), getOnlyDate(trx.tgl_selesai_faset));
        setDatepickerValue(document.getElementById('tgl_selesai_janji'), getOnlyDate(trx.tgl_selesai_janji));

        // tgl_diambil: set value AND set radio button accordingly
        const tglDiambilVal = getOnlyDate(trx.tanggal_diambil || trx.tgl_diambil);
        setDatepickerValue(document.getElementById('tgl_diambil'), tglDiambilVal);
        if (tglDiambilVal) {
            document.getElementById('sudah').checked = true;
        } else {
            document.getElementById('belum').checked = true;
        }

        // tgl_faktur
        const tglFakturVal = getOnlyDate(trx.tgl_faktur) || getOnlyDate(trx.tgl_order) || '{{ date('Y-m-d') }}';
        setDatepickerValue(document.getElementById('tgl_faktur'), tglFakturVal);

        // Step 2 — Pasien
        const p = trx.patient || {};
        const selectedNik = p.nik || trx.nik || '';
        sv('input[name="nik"]', selectedNik);
        const selectedBpjs = p.no_bpjs || trx.no_bpjs || '';
        sv('input[name="no_bpjs"]', selectedBpjs);
        const selectedTipeBpjs = p.tipe_bpjs || trx.tipe_bpjs || '';
        sv('input[name="tipe_bpjs"]', selectedTipeBpjs);
        sv('input[name="nama"]', p.nama || trx.nama_pasien || '');
        sv('textarea[name="alamat"]', p.alamat || trx.alamat_pasien || '');
        sv('input[name="telp"]', p.no_hp || trx.telp_pasien || '');
        document.getElementById('nama_pasien').value = p.nama || trx.nama_pasien || '';
        // BPJS fields only visible when typefaktur == 2 (BPJS)
        const isBpjsTransaction = trx.typefaktur == 2;
        document.getElementById('no_bpjs_container').style.display = isBpjsTransaction ? '' : 'none';
        document.getElementById('tipe_bpjs_container').style.display = isBpjsTransaction ? '' : 'none';
        if (p.nama || trx.nama_pasien) {
            document.getElementById('ac_pasien').value = p.nama || trx.nama_pasien;
            document.getElementById('patient-selected-name').textContent = p.nama || trx.nama_pasien;
            document.getElementById('patient-selected-badge').classList.remove('d-none');
            document.getElementById('btn-load-history').style.display = '';
        }

        // Step 2 — Resep
        sv('input[name="nama_dokter"]', trx.nama_dokter || trx.asal_resep || '');
        sv('input[name="asal_resep"]', trx.asal_resep || '');
        sv('input[name="no_legalisasi"]', trx.no_legalisasi || '');
        setDatepickerValue(fm.querySelector('input[name="tgl_legalisasi"]'), getOnlyDate(trx.tgl_legalisasi));
        setDatepickerValue(fm.querySelector('input[name="tgl_refraksi"]'), getOnlyDate(trx.tgl_refraksi));
        sv('textarea[name="catatan_resep"]', trx.catatan_resep || '');

        // Step 2 — Refraksi (Load from Medical Record sub-tables)
        const allFields = ['sc', 'sph', 'cyl', 'axis', 'add', 'mpd', 'prism', 'cc'];
        const mr = trx.medical_record;

        masterDataSnapshot = {}; // Reset snapshot for comparison

        if (mr) {
            // Map Refraction (ref_)
            if (mr.refraction) {
                allFields.forEach(f => {
                    ['od', 'os'].forEach(eye => {
                        const val = mr.refraction[`${eye}_${f}`] || '';
                        const inputId = `ref_${eye}_${f}`;
                        const el = document.getElementById(inputId);
                        if (el) {
                            el.value = val;
                            if (val) el.placeholder = val;
                            masterDataSnapshot[inputId] = val;
                        }
                    });
                });
            }

            // Map Prescription (presc_)
            if (mr.prescription) {
                allFields.forEach(f => {
                    if (f === 'sc') return;
                    ['od', 'os'].forEach(eye => {
                        const val = mr.prescription[`${eye}_${f}`] || '';
                        const inputId = `presc_${eye}_${f}`;
                        const el = document.getElementById(inputId);
                        if (el) {
                            el.value = val;
                            if (val) el.placeholder = val;
                            masterDataSnapshot[inputId] = val;
                        }
                    });
                });
            }

            // Map Old Glasses (og_)
            if (mr.old_glasses) {
                allFields.forEach(f => {
                    if (f === 'sc') return;
                    ['od', 'os'].forEach(eye => {
                        const val = mr.old_glasses[`${eye}_${f}`] || '';
                        const inputId = `og_${eye}_${f}`;
                        const el = document.getElementById(inputId);
                        if (el) {
                            el.value = val;
                            if (val) el.placeholder = val;
                            masterDataSnapshot[inputId] = val;
                        }
                    });
                });
            }
        } else {
            // Fallback for legacy refraction data directly in transaction (Table 2/ref)
            allFields.forEach(f => {
                ['od', 'os'].forEach(eye => {
                    const dbField = `${eye}_${f}`;
                    const val = trx[dbField] || '';
                    const inputId = `ref_${dbField}`;
                    const el = document.getElementById(inputId);
                    if (el) {
                        el.value = val;
                        if (val) el.placeholder = val;
                        masterDataSnapshot[inputId] = val;
                    }
                });
            });
        }
        sv('input[name="lensa"]', trx.lensa || '');
        sv('input[name="keterangan_frame"]', trx.keterangan_frame || '');

        // Step 3 — Produk/Cart
        cart = [];
        if (trx.items && trx.items.length) {
            trx.items.forEach(item => {
                cart.push({
                    type: item.type || 'Lainnya',
                    nama: item.nama_produk || '',
                    seri: item.seri || trx.seri || '',
                    warna: item.warna || trx.warna || '',
                    keterangan: item.keterangan || trx.keterangan_frame || '',
                    harga: parseFloat(item.harga_satuan) || 0,
                    qty: item.qty || 1,
                    product_id: item.product_id || '',
                });
            });
        } else if (trx.kode_frame || trx.nama_produk) {
            // Fallback for old single-item format
            cart.push({
                type: 'Lainnya',
                nama: trx.nama_produk || trx.kode_frame || '',
                seri: trx.seri || '',
                warna: trx.warna || '',
                keterangan: trx.keterangan_frame || '',
                harga: trx.items && trx.items[0] ? parseFloat(trx.items[0].harga_satuan) : 0,
                qty: 1,
            });
        }

        // Step 5 — Finance (harga_jual otomatis dari cart via processCart)
        inputDp.value = formatRibuan(parseFloat(trx.dp || trx.bayar || 0));
        if (inputPelunasan) inputPelunasan.value = formatRibuan(parseFloat(trx.pelunasan || 0));

        // Radios (trigger logic first)
        const tf = trx.typefaktur == 2 ? 'bpjs' : 'tunai';
        const db = trx.diambil == 1 ? 'sudah' : 'belum';
        document.getElementById(tf).checked = true;
        document.getElementById(db).checked = true;

        // Potongan based on tipe faktur
        if (trx.typefaktur == 2) {
            document.getElementById('input_potongan_bpjs').value = formatRibuan(parseFloat(trx.potongan || trx.diskon_nominal || 0));
            document.getElementById('input_potongan').value = 0;

            // Auto fill ui bpjs if found
            clearBpjs('Frame');
            clearBpjs('Lensa');
            cart.forEach(item => {
                if (item.type === 'Frame') {
                    document.getElementById('bpjs_frame_id').value = item.product_id;
                    document.getElementById('bpjs_frame_nama').value = item.nama;
                    document.getElementById('bpjs_frame_harga').value = formatRibuan(item.harga);
                    document.getElementById('bpjs_frame_status').classList.remove('d-none');
                } else if (item.type === 'Lensa') {
                    document.getElementById('bpjs_lensa_id').value = item.product_id;
                    document.getElementById('bpjs_lensa_nama').value = item.nama;
                    document.getElementById('bpjs_lensa_harga').value = formatRibuan(item.harga);
                    document.getElementById('bpjs_lensa_status').classList.remove('d-none');
                }
            });
        } else {
            document.getElementById('input_potongan').value = formatRibuan(parseFloat(trx.potongan || trx.diskon_nominal || 0));
            document.getElementById('input_potongan_bpjs').value = 0;
        }

        // Call bindOptionEvents listeners indirectly
        const evt = new Event('change');
        document.getElementById(tf).dispatchEvent(evt);
        document.getElementById(db).dispatchEvent(evt);

        calculateSisa();
        processCart();

        document.getElementById('btn-simpan').innerHTML = '<i class="bi bi-save"></i> Update Transaksi';

        // Check for medical record changes
        if (typeof checkForChanges === 'function') {
            checkForChanges();
        }

        // Go to step 1 after load
        goStep(1);

        // Fix navigation URL
        window.history.pushState(null, '', `{{ url('transactions') }}/${trx.id}`);
    }

    /* ==========================================================
       SEARCH ENGINE & MODAL
    ========================================================== */
    function loadSearchData(q, page = 1) {
        const tbody = document.querySelector('#searchTable tbody');
        const paginationEl = document.getElementById('searchPagination');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border text-primary spinner-border-sm"></div></td></tr>';
        paginationEl.innerHTML = '';
        fetch(`{{ route('transactions.pos.search') }}?q=${encodeURIComponent(q)}&page=${page}`)
            .then(r => r.json())
            .then(data => {
                if (!data.data || !data.data.length) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted small py-3">Tidak ada data</td></tr>';
                    return;
                }
                tbody.innerHTML = data.data.map(d => `
                <tr>
                    <td class="small">${d.tanggal}</td>
                    <td class="fw-bold text-primary" style="cursor:pointer" onclick="selectSearchTrx(${d.id})">${d.no_transaksi}</td>
                    <td>${d.pasien}</td>
                    <td class="text-end fw-semibold">${d.total}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="selectSearchTrx(${d.id})"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="#" onclick="openPrintModal(${d.id}, ${d.is_bpjs})"><i class="bi bi-printer me-2"></i>Cetak</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `).join('');

                if (data.last_page > 1) {
                    let paginationHtml = '<nav><ul class="pagination pagination-sm justify-content-center">';
                    if (data.current_page > 1) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${data.current_page - 1})">«</a></li>`;
                    }
                    for (let i = Math.max(1, data.current_page - 2); i <= Math.min(data.last_page, data.current_page + 2); i++) {
                        paginationHtml += `<li class="page-item ${i === data.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${i})">${i}</a></li>`;
                    }
                    if (data.current_page < data.last_page) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadSearchData('${q}', ${data.current_page + 1})">»</a></li>`;
                    }
                    paginationHtml += '</ul></nav>';
                    paginationEl.innerHTML = paginationHtml;
                }
            });
    }

    function selectSearchTrx(id) {
        const searchModalEl = bootstrap.Modal.getInstance(document.getElementById('searchModal'));
        if (searchModalEl) searchModalEl.hide();

        // Instead of redirecting to old controller layout, we can fetch JSON and populate here
        // or we can redirect to the view which will populate.
        // The previous implementation redirected:
        window.location.href = `{{ url('transactions/create') }}/${id}`;
    }

    let searchTimer;
    document.getElementById('modalSearchInput').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadSearchData(this.value, 1), 300);
    });

    function openSearchModal() {
        const searchModalEl = new bootstrap.Modal(document.getElementById('searchModal'));
        searchModalEl.show();
        loadSearchData('', 1);
        setTimeout(() => document.getElementById('modalSearchInput').focus(), 300);
    }

    // On Page Load: Fill Form if ID is present in URL
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const reqStep = parseInt(urlParams.get('step')) || 1;

        // If the backend passes $transaction variable, we fill it.
        @if(isset($transaction))
            const trxData = @json($transaction);
            fillForm(trxData);
            setTimeout(() => {
                goStep(reqStep);
            }, 300);
        @else
            updateStepUI(reqStep);
            bindOptionEvents();
        @endif
    });

    /* ==========================================================
       FORM SUBMIT / DELETE / ETC
    ========================================================== */
    function printBpjsForm() {
        const originalId = document.getElementById('trx_id').value;
        if (!originalId) {
            toast('warning', 'Simpan transaksi dulu agar bisa cetak.');
            return;
        }
        doPrint('formulir_bpjs');
    }

    document.getElementById('pos-form').addEventListener('submit', function (e) {
        e.preventDefault();
        for (let s = 1; s <= 5; s++) {
            if (!validateStep(s)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Sepertinya ada isian yang terlewat pada bagian ' + STEP_LABELS[s] + '. Mohon periksa kembali ya.',
                    confirmButtonText: 'Baik, saya periksa',
                    confirmButtonColor: '#3085d6'
                });
                goStep(s);
                return;
            }
        }

        const btn = document.getElementById('btn-simpan');
        const oldHtml = btn.innerHTML;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Menyimpan...';
        btn.disabled = true;

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
            .then(async r => {
                const data = await r.json();
                if (!r.ok && data.status !== 'error') {
                    if (r.status === 422 && data.errors) {
                        let errorMsg = '<ul class="text-start mb-0">';
                        for (let field in data.errors) {
                            errorMsg += `<li>${data.errors[field][0]}</li>`;
                        }
                        errorMsg += '</ul>';
                        throw new Error(errorMsg);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan pada server saat memproses data.');
                }
                return data;
            })
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('trx_id').value = data.data.id;
                    Swal.fire({
                        title: 'Alhamdulillah, Transaksi Berhasil!',
                        text: 'Tunggu sebentar, kami sedang mengalihkan Anda...',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("transactions.index") }}?just_saved=' + data.data.id;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops, Terjadi Kendala',
                        html: data.message || 'Mohon periksa kembali isian Anda ya.',
                        confirmButtonText: 'Baik, saya mengerti',
                        confirmButtonColor: '#d33'
                    }).then(() => window.location.reload());
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Mohon Maaf, Ada Kesalahan',
                    html: err.message === '[object Object]' ? 'Mohon cek kembali isian yang diberikan.' : err.message,
                    confirmButtonText: 'Baik, saya perbaiki',
                    confirmButtonColor: '#f0ad4e'
                }).then(() => window.location.reload());
            })
            .finally(() => { btn.innerHTML = oldHtml; btn.disabled = false; });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            // Abaikan jika sedang di textarea atau input swal (agar tidak bentrok)
            if (e.target.tagName === 'TEXTAREA' || e.target.closest('.swal2-container')) return;

            // Jika event sudah di-handle oleh input pencarian produk (defaultPrevented), abaikan
            if (e.defaultPrevented) return;

            e.preventDefault();
            if (currentStep < 5) {
                goNextStep(currentStep);
            } else {
                const btnSimpan = document.getElementById('btn-simpan');
                if (btnSimpan && !btnSimpan.disabled) {
                    btnSimpan.click();
                }
            }
        }
    });

    // --- Autosave Sync Hook ---
    const posForm = document.getElementById('pos-form');
    if (posForm) {
        posForm.addEventListener('autosaveRestored', function (e) {
            const detail = e.detail;

            // Restore cart data
            if (detail.cart_data) {
                try {
                    cart = JSON.parse(detail.cart_data);
                    processCart();
                } catch (err) {
                    console.error("Gagal memulihkan keranjang", err);
                }
            }

            // Restore patient UI
            if (detail.nama) { // name="nama"
                document.getElementById('patient-selected-name').textContent = detail.nama;
                document.getElementById('patient-selected-badge').classList.remove('d-none');
                document.getElementById('ac_pasien').value = detail.nama;

                const btnLoadHistory = document.getElementById('btn-load-history');
                if (btnLoadHistory) btnLoadHistory.style.display = '';
            }

            // Sync check for medical record changes
            if (typeof checkForChanges === 'function') {
                checkForChanges();
            }

            // Restore BPJS UI visibility
            const isBpjs = document.getElementById('bpjs') && document.getElementById('bpjs').checked;
            if (isBpjs) {
                const bpjsContainer = document.getElementById('bpjs-product-container');
                const umumContainer = document.getElementById('umum-product-container');
                if (bpjsContainer) bpjsContainer.classList.remove('d-none');
                if (umumContainer) umumContainer.classList.add('d-none');

                if (detail.bpjs_frame_nama && document.getElementById('bpjs_frame_status')) {
                    document.getElementById('bpjs_frame_status').classList.remove('d-none');
                }
                if (detail.bpjs_lensa_nama && document.getElementById('bpjs_lensa_status')) {
                    document.getElementById('bpjs_lensa_status').classList.remove('d-none');
                }
            }
        });
    }
</script>