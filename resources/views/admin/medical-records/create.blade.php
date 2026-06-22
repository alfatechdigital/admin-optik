<style>
    .input-compact {
        min-width: 75px;
        text-align: center;
    }
</style>

@extends('layouts.admin')
@section('title', 'Rekam Medis Baru')
@section('page-title', 'Input Rekam Medis')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form method="POST" action="{{ route('medical-records.store') }}" id="mrForm">
                @csrf

                {{-- Pilih Pasien --}}
                <div class="card mb-3">
                    <div class="card-header p-3"><i class="bi bi-person text-primary me-2"></i>Data Pasien & Kunjungan</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    Pasien <span class="text-danger">*</span>
                                </label>

                                {{-- Hidden ID --}}
                                <input type="hidden" name="patient_id" id="patient_id"
                                    value="{{ old('patient_id', $selectedPatient?->id) }}">

                                {{-- Input tampil --}}
                                <input type="text" id="patient_search"
                                    class="form-control @error('patient_id') is-invalid @enderror"
                                    placeholder="Cari No RM / Nama pasien..."
                                    value="{{ old('patient_id') ? $selectedPatient?->no_rm . ' — ' . $selectedPatient?->nama : '' }}"
                                    autocomplete="off" required>

                                {{-- Dropdown hasil --}}
                                <div id="patient_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('patient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Dokter / Pemeriksa <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="nama_dokter" id="doctor_search"
                                    class="form-control @error('nama_dokter') is-invalid @enderror"
                                    value="{{ old('nama_dokter', auth()->user()->name) }}"
                                    placeholder="Masukkan nama dokter" autocomplete="off" required>

                                {{-- Dropdown hasil --}}
                                <div id="doctor_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('nama_dokter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Kunjungan <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kunjungan"
                                    class="form-control @error('tanggal_kunjungan') is-invalid @enderror"
                                    value="{{ old('tanggal_kunjungan', today()->format('Y-m-d')) }}" required>
                                @error('tanggal_kunjungan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keluhan Utama</label>
                                <input type="text" name="keluhan" class="form-control" value="{{ old('keluhan') }}"
                                    placeholder="Penglihatan buram, mata lelah, sakit kepala...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ukuran Kacamata Lama --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Ukuran Kacamata Lama</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>PRISM</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </th>
                                        <td><input type="text" name="og_od_sph" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_od_cyl" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_od_axis" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_od_prism" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_od_add" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_od_mpd" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_od_cc" class="form-control input-compact"
                                                placeholder="-"></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                        </th>
                                        <td><input type="text" name="og_os_sph" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_os_cyl" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_os_axis" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_os_prism" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_os_add" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_os_mpd" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="og_os_cc" class="form-control input-compact"
                                                placeholder="-"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Resep Dokter --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Ukuran Resep Dokter</div>
                    <div class="card-body">

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>PRISM</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </th>
                                        <td><input type="text" name="presc_od_sph" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_od_cyl" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_od_axis" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_od_prism" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_od_add" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_od_mpd" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_od_cc" class="form-control input-compact"
                                                placeholder="-"></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                        </th>
                                        <td><input type="text" name="presc_os_sph" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_os_cyl" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_os_axis" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_os_prism" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_os_add" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_os_mpd" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="presc_os_cc" class="form-control input-compact"
                                                placeholder="-"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            {{-- Diagnosis --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Diagnosa
                                </label>

                                {{-- Input tampil --}}
                                <input type="text" id="rx_diagnosis_search" name="presc_diagnosis"
                                    class="form-control @error('presc_diagnosis') is-invalid @enderror"
                                    placeholder="Cari atau ketik Diagnosa..."
                                    value="{{ old('presc_diagnosis', $selectedDiagnosis?->name) }}" autocomplete="off">

                                {{-- Dropdown hasil --}}
                                <div id="rx_diagnosis_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('presc_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Asal Resep / Dokter</label>
                                <input type="text" name="presc_doctor_name" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Resep</label>
                                <input type="date" name="presc_exam_date" class="form-control">
                            </div>

                            <div class="col-md-12 mt-2">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="presc_notes" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Hasil Refraksi --}}
                <div class="card mb-3">
                    <div class="card-header fw-semibold">Hasil Refraksi</div>
                    <div class="card-body">

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle text-center">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>SC</th>
                                        <th>SPH</th>
                                        <th>CYL</th>
                                        <th>AXIS</th>
                                        <th>PRISM</th>
                                        <th>ADD</th>
                                        <th>MPD</th>
                                        <th>CC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <span class="badge bg-danger fs-6">OD</span>
                                            <div class="text-muted" style="font-size:.72rem">Kanan</div>
                                        </th>
                                        <td><input type="text" name="ref_od_sc" class="form-control input-compact"
                                                placeholder="-"></td>
                                        <td><input type="text" name="ref_od_sph" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_od_cyl" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_od_axis" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_od_prism" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_od_add" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_od_mpd" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_od_cc" class="form-control input-compact"
                                                placeholder="-"></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="badge bg-info fs-6">OS</span>
                                            <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                        </th>
                                        <td><input type="text" name="ref_os_sc" class="form-control input-compact"
                                                placeholder="-"></td>
                                        <td><input type="text" name="ref_os_sph" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_os_cyl" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_os_axis" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_os_prism" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_os_add" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_os_mpd" class="form-control input-compact"
                                                step="0.25" min="-30" max="30" placeholder="-"></td>
                                        <td><input type="text" name="ref_os_cc" class="form-control input-compact"
                                                placeholder="-"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            {{-- Diagnosis --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Diagnosa
                                </label>

                                {{-- Input tampil --}}
                                <input type="text" id="ref_diagnosis_search" name="ref_diagnosis"
                                    class="form-control @error('ref_diagnosis') is-invalid @enderror"
                                    placeholder="Cari atau ketik Diagnosa..."
                                    value="{{ old('ref_diagnosis', $selectedDiagnosis?->name) }}" autocomplete="off">

                                {{-- Dropdown hasil --}}
                                <div id="ref_diagnosis_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('ref_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Asal Resep / Dokter</label>
                                <input type="text" name="ref_doctor_name" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Resep</label>
                                <input type="date" name="ref_exam_date" class="form-control">
                            </div>

                            <div class="col-md-12 mt-2">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="ref_notes" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Simpan Rekam Medis
                    </button>
                    <a href="{{ route('medical-records.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('mrForm').addEventListener('submit', function(e) {
    const patientId = document.getElementById('patient_id').value;
    if (!patientId) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Pasien wajib dipilih dari hasil pencarian.',
            confirmButtonText: 'Baik, saya perbaiki'
        });
        document.getElementById('patient_search').classList.add('is-invalid');
        document.getElementById('patient_search').focus();
        return false;
    }
    
    const doctorSearch = document.getElementById('doctor_search').value.trim();
    if (!doctorSearch) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Nama Dokter / Pemeriksa wajib diisi.',
            confirmButtonText: 'Baik, saya perbaiki'
        });
        document.getElementById('doctor_search').classList.add('is-invalid');
        document.getElementById('doctor_search').focus();
        return false;
    }
    
    const visitDate = document.querySelector('input[name="tanggal_kunjungan"]').value;
    if (!visitDate) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap',
            text: 'Tanggal Kunjungan wajib diisi.',
            confirmButtonText: 'Baik, saya perbaiki'
        });
        document.querySelector('input[name="tanggal_kunjungan"]').classList.add('is-invalid');
        document.querySelector('input[name="tanggal_kunjungan"]').focus();
        return false;
    }
});
</script>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('patient_search');
        const resultBox = document.getElementById('patient_result');
        const hidden = document.getElementById('patient_id');

        let timeout = null;

        input.addEventListener('keyup', function () {
            const query = this.value;

            clearTimeout(timeout);

            if (query.length < 2) {
                resultBox.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('patients.autocomplete') }}?q=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        resultBox.innerHTML = '';

                        data.forEach(item => {
                            const el = document.createElement('a');
                            el.href = "#";
                            el.classList.add('list-group-item', 'list-group-item-action');
                            el.textContent = `${item.no_rm} — ${item.nama}`;

                            el.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = `${item.no_rm} — ${item.nama}`;
                                hidden.value = item.id;
                                resultBox.innerHTML = '';
                            });

                            resultBox.appendChild(el);
                        });
                    });
            }, 300);
        });

        // klik luar -> close dropdown
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target)) {
                resultBox.innerHTML = '';
            }
        });
    });

    // refraksi diagnosis autocomplete
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('ref_diagnosis_search');
        const resultBox = document.getElementById('ref_diagnosis_result');

        let timeout = null;

        input.addEventListener('keyup', function () {
            const query = this.value;

            clearTimeout(timeout);

            if (query.length < 2) {
                resultBox.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('diagnoses.search') }}?q=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        resultBox.innerHTML = '';

                        data.forEach(item => {
                            const el = document.createElement('a');
                            el.href = "#";
                            el.classList.add('list-group-item', 'list-group-item-action');
                            el.textContent = `${item.name}`;

                            el.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = `${item.name}`;
                                resultBox.innerHTML = '';
                            });

                            resultBox.appendChild(el);
                        });
                    });
            }, 300);
        });

        // klik luar -> close dropdown
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target)) {
                resultBox.innerHTML = '';
            }
        });
    });

    // recipe diagnosis autocomplete
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('rx_diagnosis_search');
        const resultBox = document.getElementById('rx_diagnosis_result');

        let timeout = null;

        input.addEventListener('keyup', function () {
            const query = this.value;

            clearTimeout(timeout);

            if (query.length < 2) {
                resultBox.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('diagnoses.search') }}?q=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        resultBox.innerHTML = '';

                        data.forEach(item => {
                            const el = document.createElement('a');
                            el.href = "#";
                            el.classList.add('list-group-item', 'list-group-item-action');
                            el.textContent = `${item.name}`;

                            el.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = `${item.name}`;
                                resultBox.innerHTML = '';
                            });

                            resultBox.appendChild(el);
                        });
                    });
            }, 300);
        });

        // klik luar -> close dropdown
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target)) {
                resultBox.innerHTML = '';
            }
        });
    });

    // doctor autocomplete
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('doctor_search');
        const resultBox = document.getElementById('doctor_result');

        let timeout = null;

        input.addEventListener('keyup', function () {
            const query = this.value;

            clearTimeout(timeout);

            if (query.length < 2) {
                resultBox.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('doctors.autocomplete') }}?q=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        resultBox.innerHTML = '';

                        data.forEach(item => {
                            const el = document.createElement('a');
                            el.href = "#";
                            el.classList.add('list-group-item', 'list-group-item-action');
                            el.textContent = `${item.name}`;

                            el.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = `${item.name}`;
                                resultBox.innerHTML = '';
                            });

                            resultBox.appendChild(el);
                        });
                    });
            }, 300);
        });

        // klik luar -> close dropdown
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target)) {
                resultBox.innerHTML = '';
            }
        });
    });
</script>