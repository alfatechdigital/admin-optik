<div class="wizard-panel" id="panel-step-2">
    <div class="step-error-msg" id="err-step-2">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span id="err-step-2-msg">Mohon lengkapi data yang wajib diisi.</span>
    </div>

    <div class="row g-3">
        {{-- KIRI: Pasien (Logika UMUM/BPJS tetap dipertahankan) --}}
        <div class="col-lg-5">
            <div class="glass-card">
                <div class="card-body p-4">
                    <h6 class="card-header-section">
                        <i class="bi bi-person-badge"></i> Data Pasien
                        <span class="badge-opt ms-1">opsional — kosong = UMUM</span>
                    </h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <input type="hidden" name="nama" id="nama_pasien" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Cari Pasien (Nama / BPJS / Telp)</label>
                            <div class="input-group input-group-sm position-relative">
                                <input type="text" id="ac_pasien" class="form-control"
                                    placeholder="Ketik nama atau No BPJS..." autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearPatient()">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                <div id="dd_pasien" class="ac-dropdown d-none"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-2">
                                <div id="patient-selected-badge" class="d-none">
                                    <span class="badge bg-info-subtle text-info py-1">
                                        <i class="bi bi-person-check me-1"></i>
                                        <span id="patient-selected-name"></span>
                                    </span>
                                </div>
                                <button type="button" id="btn-load-history"
                                    class="btn btn-sm btn-outline-secondary d-none" onclick="loadPatientHistory()">
                                    <i class="bi bi-clock-history me-1"></i>Lihat Riwayat
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6" id="nik_container">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telp" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6" id="no_bpjs_container" style="display:none">
                            <label class="form-label">No BPJS</label>
                            <input type="text" name="no_bpjs" id="no_bpjs" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6" id="tipe_bpjs_container" style="display:none">
                            <label class="form-label">Tipe BPJS</label>
                            <select name="tipe_bpjs" id="tipe_bpjs" class="form-select form-select-sm">
                                <option value="">-- Pilih Kelas --</option>
                                <option value="1">Kelas 1</option>
                                <option value="2">Kelas 2</option>
                                <option value="3">Kelas 3</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RESEP --}}
            <div class="glass-card">
                <div class="card-body p-4">
                    <h6 class="card-header-section">
                        <i class="bi bi-file-medical"></i> Resep & Dokter
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nama Dokter / Optometrist <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_dokter" id="nama_dokter" class="form-control form-control-sm"
                                value="" oninput="checkForChanges()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asal Resep / Source</label>
                            <input type="text" name="asal_resep" id="asal_resep" class="form-control form-control-sm"
                                placeholder="">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Resep</label>
                            <input type="text" name="tgl_resep" class="form-control form-control-sm datepicker"
                                value="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No Legalisasi</label>
                            <input type="text" name="no_legalisasi" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tgl Legalisasi</label>
                            <input type="text" name="tgl_legalisasi" class="form-control form-control-sm datepicker"
                                placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Resep</label>
                            <textarea name="catatan_resep" class="form-control form-control-sm" rows="2"
                                placeholder="Instruksi tambahan..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KANAN: 3 Tabel Ukuran (Lama, Resep, Transaksi) --}}
        <div class="col-lg-7">
            <div class="glass-card h-100">
                <div class="card-body p-4">

                    {{-- Dropdown Utama Rekam Medis --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="card-header-section mb-0 text-dark">
                                <i class="bi bi-clock-history"></i> Pilih Riwayat Rekam Medis
                            </h6>
                            <span class="badge bg-primary-subtle text-primary" id="history-count">0 Riwayat</span>
                        </div>
                        <select id="history_dropdown" class="form-select form-select-sm border-primary shadow-sm"
                            onchange="handleHistoryChange(this)">
                            <option value="">-- Buat Rekam Medis Baru (Kosong) --</option>
                        </select>
                        <input type="hidden" name="save_as_new_medical" id="save_as_new_medical" value="1">
                    </div>

                    @php
                        $sections = [
                            ['id' => 'presc', 'title' => 'Resep Dokter', 'icon' => 'bi-file-earmark-medical', 'color' => 'warning'],
                            ['id' => 'ref', 'title' => 'Hasil Refraksi', 'icon' => 'bi-pencil-square', 'color' => 'primary'],
                            ['id' => 'og', 'title' => 'Ukuran Kacamata', 'icon' => 'bi-eyeglasses', 'color' => 'secondary'],
                        ];
                    @endphp

                    @foreach($sections as $sec)
                        <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="card-header-section mb-0 text-{{ $sec['color'] }}" style="font-size: .85rem">
                                    <i class="bi {{ $sec['icon'] }}"></i> {{ $sec['title'] }}
                                </h6>
                            </div>

                            <div class="table-responsive custom-table-responsive">
                                <table class="table table-sm table-borderless mb-0 align-middle">
                                    <thead class="text-center text-muted" style="font-size: .75rem;">
                                        <tr>
                                            <th style="width:40px; min-width:40px;">Mata</th>
                                            @php
                                                $fields = ($sec['id'] == 'ref')
                                                    ? ['sc', 'sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc']
                                                    : ['sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc'];
                                            @endphp
                                            @foreach($fields as $f)
                                                <th style="min-width:54px; max-width:70px;">{{ strtoupper($f) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['od' => 'text-primary', 'os' => 'text-danger'] as $eye => $color)
                                            <tr>
                                                <td class="{{ $color }} fw-bold text-center"
                                                    style="font-size: .8rem; min-width:40px;">{{ strtoupper($eye) }}</td>
                                                @foreach($fields as $field)
                                                    @php
                                                        $name = "{$sec['id']}_{$eye}_{$field}";
                                                        if ($sec['id'] == 'ref')
                                                            $name = "{$eye}_{$field}";
                                                    @endphp
                                                    <td style="min-width:54px; max-width:70px;">
                                                        <input type="text" name="{{ $name }}"
                                                            id="{{ $sec['id'] }}_{{ $eye }}_{{$field}}"
                                                            class="form-control form-control-sm text-center track-change input-3digit"
                                                            style="width: 60px; min-width: 54px; max-width: 70px; font-size: .92rem; padding: 2px 4px;"
                                                            placeholder="{{ $field == 'sc' || $field == 'cc' ? '-' : '-' }}"
                                                            oninput="checkForChanges()">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            </style>
                            <style>
                                /* Responsive table for ukuran input */
                                .custom-table-responsive {
                                    overflow-x: auto;
                                }

                                .custom-table-responsive table {
                                    min-width: 420px;
                                }

                                @media (max-width: 768px) {
                                    .custom-table-responsive table {
                                        font-size: 0.85rem;
                                    }

                                    .custom-table-responsive th,
                                    .custom-table-responsive td {
                                        padding: 2px 2px !important;
                                    }

                                    .input-3digit {
                                        width: 48px !important;
                                        min-width: 40px !important;
                                        max-width: 60px !important;
                                        font-size: 0.85rem !important;
                                    }
                                }

                                /* Input 3 digit angka tetap rapi */
                                .input-3digit {
                                    text-align: center;
                                    width: 60px;
                                    min-width: 54px;
                                    max-width: 70px;
                                    font-size: .92rem;
                                    padding: 2px 4px;
                                }
                            </style>
                        </div>
                    @endforeach

                    <!-- <div class="alert alert-light border-start border-4 border-info py-2 px-3 mb-3"
                        style="font-size: .75rem;">
                        <i class="bi bi-info-circle-fill text-info"></i>
                        Jika Anda mengubah data dari riwayat yang dipilih, sistem akan menyimpannya sebagai **Rekam
                        Medis Baru** untuk transaksi ini.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size: 0.72rem;">JENIS LENSA</label>
                            <input type="text" name="lensa" id="lensa_ket" class="form-control form-control-sm" placeholder="Essilor, Crizal, dll...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size: 0.72rem;">KETERANGAN FRAME</label>
                            <input type="text" name="keterangan_frame" class="form-control form-control-sm" placeholder="Frame patah, ganti senar, dll...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.72rem;">DIAGNOSIS (REFRAKSI)</label>
                            <input type="text" name="ref_diagnosis" id="ref_diagnosis" class="form-control form-control-sm" placeholder="Diagnosis hasil refraksi...">
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-nav-bar mt-4">
        <button type="button" class="btn btn-action btn-light border shadow-sm" onclick="goStep(1)">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
        <div class="nav-right">
            <button type="button" class="btn btn-action btn-primary shadow-sm px-4" onclick="goNextStep(2)">
                Lanjut ke Produk <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>
</div>

<script>
    // Penampung data asli untuk membandingkan perubahan
    let masterDataSnapshot = {};

    /**
     * Dipanggil saat dropdown riwayat rekam medis dipilih
     */
    function handleHistoryChange(select) {
        const id = select.value;
        if (!id) {
            resetAllInputs();
            return;
        }

        fetch(`/api/rekam-medis-detail/${id}`)
            .then(res => res.json())
            .then(data => {
                const prefixes = ['og', 'presc', 'ref'];
                const eyes = ['od', 'os'];
                const fields = ['sc', 'sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc'];

                masterDataSnapshot = {}; // Reset snapshot

                prefixes.forEach(p => {
                    eyes.forEach(e => {
                        fields.forEach(f => {
                            // Mencari key di data API (asumsi key format: p_e_f)
                            const val = data[`${p}_${e}_${f}`] || '';
                            const inputId = `${p}_${e}_${f}`;
                            const el = document.getElementById(inputId);

                            if (el) {
                                if (p === 'og') {
                                    // Kosongkan value ukuran kacamata agar di input manual
                                    el.value = '';
                                    masterDataSnapshot[inputId] = '';
                                } else {
                                    el.value = val;
                                    masterDataSnapshot[inputId] = val; // Simpan ke snapshot
                                }
                                el.classList.remove('border-warning');
                            }
                        });
                    });
                });

                checkForChanges();
            })
            .catch(err => console.error("Gagal memuat rekam medis:", err));
    }

    /**
     * Memeriksa apakah ada perbedaan antara input saat ini dengan data riwayat
     */
    function checkForChanges() {
        let hasChanges = false;
        const currentHistoryId = document.getElementById('history_dropdown').value;
        const inputs = document.querySelectorAll('.track-change');

        inputs.forEach(input => {
            const originalVal = masterDataSnapshot[input.id] || '';
            if (input.value != originalVal) {
                input.classList.add('border-warning');
                hasChanges = true;
            } else {
                input.classList.remove('border-warning');
            }
        });

        const dropdown = document.getElementById('history_dropdown');
        const firstOption = dropdown.options[0];
        const flagNew = document.getElementById('save_as_new_medical');

        // Jika tidak ada riwayat terpilih ATAU ada perubahan data, tandai sebagai rekam medis baru
        if (!currentHistoryId || hasChanges) {
            const dateNow = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const typeFaktur = document.getElementById('bpjs')?.checked ? 'BPJS' : 'Umum';
            const dokter = document.getElementById('nama_dokter')?.value || 'Dokter';

            firstOption.textContent = `${dateNow} (${typeFaktur}) - ${dokter} (Baru)`;
            flagNew.value = "1";
        } else {
            firstOption.textContent = "-- Buat Rekam Medis Baru (Kosong) --";
            flagNew.value = "0";
        }
    }

    /**
     * Menghapus semua input saat dropdown dikosongkan
     */
    function resetAllInputs() {
        document.querySelectorAll('.track-change').forEach(input => {
            input.value = '';
            input.classList.remove('border-warning');
        });
        masterDataSnapshot = {};
        checkForChanges();
    }
</script>