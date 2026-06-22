@extends('layouts.admin')
@section('title', 'Rekam Medis')
@section('page-title', 'Rekam Medis')

@section('content')
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div><i class="bi bi-clipboard2-pulse text-primary me-2"></i>Daftar Rekam Medis</div>
        <div class="d-flex flex-wrap gap-2">
            <form class="d-flex flex-wrap gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nama / No. RM..." value="{{ request('search') }}" style="width:200px">
                <input type="date" name="from" class="form-control form-control-sm"
                       value="{{ request('from') }}" style="width:140px">
                <input type="date" name="to" class="form-control form-control-sm"
                       value="{{ request('to') }}" style="width:140px">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
            @can('medical_record.create')
            <a href="{{ route('medical-records.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Rekam Medis Baru
            </a>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Tanggal</th>
                    <th>No. RM</th>
                    <th>Pasien</th>
                    <th>OD (Kanan)</th>
                    <th>OS (Kiri)</th>
                    <th>Created By</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $rm)
                <tr>
                    <td class="ps-3 text-muted">{{ $records->firstItem() + $i }}</td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $rm->visit_date->format('d M Y') }}
                        </span>
                    </td>
                    <td><span class="badge bg-secondary">{{ $rm->patient?->no_rm ?? '-' }}</span></td>
                   <td class="fw-semibold">
    <a href="{{ route('medical-records.show', $rm) }}" class="text-secondary-emphasis text-decoration-none">
        {{ $rm->patient?->nama ?? 'Pasien Tidak Ditemukan / Dihapus' }}
    </a>
</td>
                    <td style="font-size:.82rem">
                        <span class="text-muted">SPH</span> {{ $rm->formatResep($rm->od_sph) }}
                        <span class="text-muted ms-1">CYL</span> {{ $rm->formatResep($rm->od_cyl) }}
                    </td>
                    <td style="font-size:.82rem">
                        <span class="text-muted">SPH</span> {{ $rm->formatResep($rm->os_sph) }}
                        <span class="text-muted ms-1">CYL</span> {{ $rm->formatResep($rm->os_cyl) }}
                    </td>
                    <td class="text-muted small">{{ $rm->createdBy->name ?? '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('medical-records.show', $rm) }}" class="btn btn-xs btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('medical_record.edit')
                            <a href="{{ route('medical-records.edit', $rm) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('medical_record.delete')
                            <form method="POST" action="{{ route('medical-records.destroy', $rm) }}"
                                  onsubmit="return confirm('Hapus rekam medis ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-clipboard2 fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada rekam medis
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="card-footer">{{ $records->links() }}</div>
    @endif
</div>

@push('styles')
<style>.btn-xs { padding: 3px 8px; font-size: .75rem; }</style>
@endpush
@endsection
