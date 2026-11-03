@php
    $medicalRecord = $patient->latestRecord;
@endphp

@extends('layouts.admin')
@section('title', 'Detail Pasien')
@section('page-title', 'Detail Pasien')

@section('content')
<div class="row g-3">
    {{-- Info Pasien --}}
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                    style="width:56px;height:56px;font-size:1.5rem;color:#1e2a5e">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <div class="fw-bold fs-6">{{ $patient->nama }}</div>
                    <span class="badge bg-secondary">{{ $patient->no_rm }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted ps-0">Jenis Kelamin</td>
                        <td>{{ $patient->jenis_kelamin == 'L' ? 'Laki-laki' : ($patient->jenis_kelamin == 'P' ?
                            'Perempuan' : '-') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tanggal Lahir</td>
                        <td>{{ $patient->tanggal_lahir ? $patient->tanggal_lahir->format('d M Y') . ' (' .
                            $patient->umur . ' th)' : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">No. NIK</td>
                        <td>{{ $patient->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">No. BPJS</td>
                        <td>{{ $patient->no_bpjs ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tipe BPJS</td>
                        <td>{{ $patient->tipe_bpjs ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Email</td>
                        <td>{{ $patient->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Alamat</td>
                        <td>{{ $patient->alamat ?? '-' }}</td>
                    </tr>
                    @if($patient->riwayat_penyakit)
                    <tr>
                        <td class="text-muted ps-0">Riwayat</td>
                        <td><span class="badge bg-warning text-dark">{{ $patient->riwayat_penyakit }}</span></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        <div class="d-flex gap-2">
            @can('patient.edit')
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-warning btn-sm flex-fill">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @endcan
            @can('medical_record.create')
            <a href="{{ route('medical-records.create', ['patient_id' => $patient->id]) }}"
                class="btn btn-primary btn-sm flex-fill">
                <i class="bi bi-clipboard2-plus me-1"></i>Rekam Medis
            </a>
            @endcan
        </div>
    </div>

    <div class="col-md-8">
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
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_sph ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_cyl ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_axis ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_prism ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_add ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_mpd ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->od_cc ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <span class="badge bg-info fs-6">OS</span>
                                    <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                </th>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_sph ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_cyl ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_axis ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_prism ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_add ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_mpd ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->oldGlasses->os_cc ?? '-' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_sc ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_sph ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_cyl ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_axis ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_prism ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_add ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_mpd ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->od_cc ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <span class="badge bg-info fs-6">OS</span>
                                    <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                </th>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_sc ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_sph ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_cyl ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_axis ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_prism ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_add ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_mpd ?? '-' }}</span>
                                </td>
                                <td class="column-compact"><span>{{ $medicalRecord->refraction->os_cc ?? '-' }}</span>
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
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_sph ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_cyl ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_axis ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_prism ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_add ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_mpd ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->od_cc ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <span class="badge bg-info fs-6">OS</span>
                                    <div class="text-muted" style="font-size:.72rem">Kiri</div>
                                </th>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_sph ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_cyl ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_axis ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_prism ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_add ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_mpd ?? '-'
                                        }}</span></td>
                                <td class="column-compact"><span>{{ $medicalRecord->prescription->os_cc ?? '-' }}</span>
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

        {{-- Histori Transaksi --}}
        <div class="card">
            <div class="card-header p-3">
                <i class="bi bi-receipt text-success me-2"></i>
                Histori Transaksi ({{ $patient->transactions->count() }})
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Metode</th>
                             <th>Status</th>
                             <th class="text-end pe-3">Aksi</th>
                         </tr>
                    </thead>
                    <tbody>
                        @forelse($patient->transactions as $trx)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('transactions.show', $trx) }}" class="text-decoration-none">
                                    {{ $trx->no_transaksi }}
                                </a>
                            </td>
                            <td>{{ $trx->created_at->format('d M Y') }}</td>
                            <td>Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($trx->metode_bayar) }}</td>
                            <td>
                                @php
                                    $badgeColor = match($trx->status) {
                                        'lunas' => 'bg-success',
                                        'paid' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'batal' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} rounded-pill px-2 py-1" style="font-size: 0.7rem">
                                    {{ strtoupper($trx->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                @if($trx->status != 'batal')
                                @can('transaction.edit')
                                <form action="{{ route('transactions.cancel', $trx) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan transaksi ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Batalkan">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                @endcan
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-xs {
        padding: 3px 8px;
        font-size: .75rem;
    }
</style>
@endpush
@endsection