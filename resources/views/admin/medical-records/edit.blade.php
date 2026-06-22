@extends('layouts.admin')
@section('title', 'Edit Rekam Medis')
@section('page-title', 'Edit Rekam Medis')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form method="POST" action="{{ route('medical-records.update', $medicalRecord) }}" id="mrForm">
                @csrf @method('PUT')

                <div class="card mb-3">
                    <div class="card-header p-3">
                        <i class="bi bi-pencil text-warning me-2"></i>Edit Rekam Medis — {{ $medicalRecord->patient->nama }}
                    </div>
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
                                @php
                                    $displayPatient = old('patient_id')
                                        ? $selectedPatient
                                        : ($medicalRecord->patient ?? null);
                                @endphp

                                <input type="text" id="patient_search"
                                    class="form-control @error('patient_id') is-invalid @enderror"
                                    placeholder="Cari No RM / Nama pasien..."
                                    value="{{ $displayPatient ? $displayPatient->no_rm . ' — ' . $displayPatient->nama : '' }}"
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
                                    value="{{ old('nama_dokter', $medicalRecord->refraction->doctor_name ?? $medicalRecord->prescription->doctor_name ?? '') }}"
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
                                <input type="date" name="tanggal_kunjungan" class="form-control" required
                                    value="{{ old('visit_date', $medicalRecord->visit_date->format('Y-m-d')) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Keluhan Utama</label>
                                <input type="text" name="keluhan" class="form-control"
                                    value="{{ old('complaint', $medicalRecord->complaint) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ukuran Kacamata Lama --}}
                <div class="card mb-3">
                    <div class="card-header p-3"><i class="bi bi-eyeglasses text-primary me-2"></i>Ukuran Kacamata Lama
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Mata</th>
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
                                    @foreach([['od', 'Kanan', 'danger'], ['os', 'Kiri', 'info']] as [$eye, $label, $color])
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $color }} fs-6">{{ strtoupper($eye) }}</span>
                                                <div class="text-muted" style="font-size:.72rem">{{ $label }}</div>
                                            </td>
                                            @foreach(['sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc'] as $field)
                                                <td>
                                                    <input type="text" name="og_{{ $eye }}_{{ $field }}"
                                                        class="form-control text-center"
                                                        step="{{ in_array($field, ['axis']) ? 1 : 0.25 }}"
                                                        value="{{ old('og_'.$eye.'_'.$field, $medicalRecord->oldGlasses?->{"{$eye}_{$field}"} ?? '') }}"
                                                        placeholder="-">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Ukuran Resep Dokter --}}
                <div class="card mb-3">
                    <div class="card-header p-3"><i class="bi bi-eyeglasses text-primary me-2"></i>Ukuran Resep Dokter</div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Mata</th>
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
                                    @foreach([['od', 'Kanan', 'danger'], ['os', 'Kiri', 'info']] as [$eye, $label, $color])
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $color }} fs-6">{{ strtoupper($eye) }}</span>
                                                <div class="text-muted" style="font-size:.72rem">{{ $label }}</div>
                                            </td>
                                            @foreach(['sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc'] as $field)
                                                <td>
                                                    <input type="text" name="presc_{{ $eye }}_{{ $field }}"
                                                        class="form-control text-center"
                                                        step="{{ in_array($field, ['axis']) ? 1 : 0.25 }}"
                                                        value="{{ old('presc_'.$eye.'_'.$field, $medicalRecord->prescription?->{"{$eye}_{$field}"} ?? '') }}"
                                                        placeholder="-">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row g-3 mt-1">
                            {{-- Diagnosis --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Diagnosa</label>

                                {{-- Input tampil --}}
                                <input type="text" id="rx_diagnosis_search" name="presc_diagnosis"
                                    class="form-control @error('presc_diagnosis') is-invalid @enderror"
                                    placeholder="Cari atau ketik Diagnosa..."
                                    value="{{ old('presc_diagnosis', $medicalRecord->prescription?->diagnosis ?? '') }}"
                                    autocomplete="off">

                                {{-- Dropdown hasil --}}
                                <div id="rx_diagnosis_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('presc_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Resep</label>
                                <input type="date" name="presc_exam_date" class="form-control"
                                    value="{{ old('presc_exam_date', $medicalRecord->prescription?->exam_date) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Dokter</label>
                                <textarea name="presc_notes" class="form-control"
                                    rows="3">{{ old('presc_notes', $medicalRecord->prescription?->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hasil Refraksi --}}
                <div class="card mb-3">
                    <div class="card-header p-3"><i class="bi bi-eyeglasses text-primary me-2"></i>Hasil Refraksi</div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Mata</th>
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
                                    @foreach([['od', 'Kanan', 'danger'], ['os', 'Kiri', 'info']] as [$eye, $label, $color])
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $color }} fs-6">{{ strtoupper($eye) }}</span>
                                                <div class="text-muted" style="font-size:.72rem">{{ $label }}</div>
                                            </td>
                                            @foreach(['sc', 'sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc'] as $field)
                                                <td>
                                                    <input type="text" name="ref_{{ $eye }}_{{ $field }}"
                                                        class="form-control text-center"
                                                        step="{{ in_array($field, ['axis']) ? 1 : 0.25 }}"
                                                        value="{{ old("ref_{$eye}_{$field}", $medicalRecord->refraction?->{$eye . '_' . $field}) ?? '' }}"
                                                        placeholder="{{ empty($value) ? '-' : '' }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row g-3 mt-1">
                            {{-- diagnosa --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Diagnosa
                                </label>

                                {{-- Input tampil --}}
                                <input type="text" id="ref_diagnosis_search" name="ref_diagnosis"
                                    class="form-control @error('ref_diagnosis') is-invalid @enderror"
                                    placeholder="Cari atau ketik Diagnosa..."
                                    value="{{ old('ref_diagnosis', $medicalRecord->refraction?->diagnosis ?? '') }}"
                                    autocomplete="off">

                                {{-- Dropdown hasil --}}
                                <div id="ref_diagnosis_result" class="list-group position-absolute w-100 shadow"
                                    style="z-index:1000;"></div>

                                @error('ref_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tanggal Resep</label>
                                <input type="date" name="ref_exam_date" class="form-control"
                                    value="{{ old('ref_exam_date', $medicalRecord->refraction?->exam_date) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Dokter</label>
                                <textarea name="ref_notes" class="form-control"
                                    rows="3">{{ old('ref_notes', $medicalRecord->refraction?->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="bi bi-check-lg me-1"></i>Update Rekam Medis
                    </button>
                    <a href="{{ route('medical-records.show', $medicalRecord) }}"
                        class="btn btn-outline-secondary">Batal</a>
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
        const input = document.getElementById('ref_diagnosis_search');
        const resultBox = document.getElementById('ref_diagnosis_result');

        let timeout = null;

        input.addEventListener('input', function () {
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

                        // 🔹 tampilkan hasil dari DB
                        data.forEach(item => {
                            const el = document.createElement('a');
                            el.href = "#";
                            el.className = 'list-group-item list-group-item-action';
                            el.textContent = item.name;

                            el.addEventListener('click', function (e) {
                                e.preventDefault();
                                input.value = item.name;
                                resultBox.innerHTML = '';
                            });

                            resultBox.appendChild(el);
                        });

                        // 🔸 tambahan: opsi input manual
                        if (data.length === 0) {
                            const el = document.createElement('div');
                            el.className = 'list-group-item text-muted';
                            el.textContent = 'Tidak ditemukan, tekan Enter untuk input manual';
                            resultBox.appendChild(el);
                        }
                    });
            }, 300);
        });

        // klik luar → close
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !resultBox.contains(e.target)) {
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