<style>
    .column-compact {
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
</style>

{{-- resources/views/admin/medical-records/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Rekam Medis')
@section('page-title', 'Detail Rekam Medis')

@section('content')
    @php
        // Formatter: leaves value as-is according to user input, returns '-' if null/empty
        $fmt = fn($v) => (is_null($v) || $v === '') ? '-' : $v;
    @endphp
    <div class="row justify-content-center">
        <div class="col-xl-9">

            {{-- Info Header --}}
            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $medicalRecord->patient->nama }}</h5>
                            <span class="badge bg-secondary me-2">{{ $medicalRecord->patient->no_rm }}</span>
                            <span class="badge bg-primary">{{ $medicalRecord->visit_date->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            @if(isset($patientRecords) && $patientRecords->count() > 0)
                                <div class="d-flex align-items-center gap-1">
                                    <label for="history_select" class="form-label mb-0 me-1 text-muted small fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i>Riwayat:
                                    </label>
                                    <select id="history_select" class="form-select form-select-sm border-primary shadow-sm" style="min-width: 220px;" onchange="if(this.value) window.location.href = this.value;">
                                        @foreach($patientRecords as $rec)
                                            @php
                                                $docName = $rec->refraction->doctor_name ?? $rec->prescription->doctor_name ?? '-';
                                                $visitDate = $rec->visit_date ? $rec->visit_date->format('d M Y') : '-';
                                            @endphp
                                            <option value="{{ route('medical-records.show', $rec) }}" {{ $rec->id == $medicalRecord->id ? 'selected' : '' }}>
                                                {{ $visitDate }} — Dokter: {{ $docName }} {{ $rec->id == $medicalRecord->id ? '(Saat Ini)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @can('medical_record.edit')
                                <!-- <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a> -->
                            @endcan
                            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted ps-0">Keluhan</td>
                                    <td>: {{ $medicalRecord->complaint ?? '-' }}</td>
                                </tr>
                            </table>
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
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_sph ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_cyl ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_axis ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_prism ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_add ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_mpd ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->od_cc ?? null) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <span class="badge bg-info fs-6">OS</span>
                                        <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                    </th>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_sph ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_cyl ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_axis ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_prism ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_add ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_mpd ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->oldGlasses->os_cc ?? null) }}</span>
                                    </td>
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
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_sph ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_cyl ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_axis ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_prism ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_add ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_mpd ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->od_cc ?? null) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <span class="badge bg-info fs-6">OS</span>
                                        <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                    </th>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_sph ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_cyl ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_axis ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_prism ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_add ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_mpd ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->prescription->os_cc ?? null) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        {{-- Diagnosis --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Diagnosis <span class="text-danger"></span>
                            </label>
                            <br>
                            <span>{{ $medicalRecord->prescription->diagnosis ?? '-' }}</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Asal Resep / Dokter</label>
                            <br>
                            <span>{{ $medicalRecord->prescription->doctor_name ?? '-' }}</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Resep</label>
                            <br>
                            <span>{{ $medicalRecord->prescription->exam_date ?? '-' }}</span>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <br>
                            <span>{{ $medicalRecord->prescription->notes ?? '-' }}</span>
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
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_sc ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_sph ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_cyl ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_axis ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_prism ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_add ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_mpd ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->od_cc ?? null) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <span class="badge bg-info fs-6">OS</span>
                                        <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                    </th>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_sc ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_sph ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_cyl ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_axis ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_prism ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_add ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_mpd ?? null) }}</span>
                                    </td>
                                    <td class="column-compact">
                                        <span>{{ $fmt($medicalRecord->refraction->os_cc ?? null) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        {{-- Diagnosis --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Diagnosis <span class="text-danger"></span>
                            </label>
                            <br>
                            <span>{{ $medicalRecord->refraction->diagnosis ?? '-' }}</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Asal Resep / Dokter</label>
                            <br>
                            <span>{{ $medicalRecord->refraction->doctor_name ?? '-' }}</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Resep</label>
                            <br>
                            <span>{{ $medicalRecord->refraction->exam_date ?? '-' }}</span>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <br>
                            <span>{{ $medicalRecord->refraction->notes ?? '-' }}</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Transaksi Terkait --}}
            @if($medicalRecord->transaction)
                <div class="card mb-3">
                    <div class="card-header p-3"><i class="bi bi-receipt text-success me-2"></i>Transaksi Terkait</div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ $medicalRecord->transaction->no_transaksi }}</div>
                                <div class="text-muted small">{{ $medicalRecord->transaction->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary fs-5">
                                    Rp {{ number_format($medicalRecord->transaction->total_bayar, 0, ',', '.') }}
                                </div>
                                <span class="badge badge-{{ $medicalRecord->transaction->status }}">
                                    {{ ucfirst($medicalRecord->transaction->status) }}
                                </span>
                            </div>
                            <a href="{{ route('transactions.show', $medicalRecord->transaction) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Lihat Struk
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2">
                <a href="{{ route('patients.show', $medicalRecord->patient) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-person me-1"></i>Profil Pasien
                </a>
                <a href="{{ route('medical-records.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>

        </div>
    </div>
@endsection