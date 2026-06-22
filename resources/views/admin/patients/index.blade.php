@extends('layouts.admin')
@section('title', 'Data Pasien')
@section('page-title', 'Data Pasien')

@section('content')
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="fw-semibold"><i class="bi bi-people text-primary me-2"></i>Daftar Pasien</div>
        <div class="d-flex gap-2 flex-wrap">
            <form class="d-flex gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / No. RM..." value="{{ request('search') }}" style="width:220px">
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
            @can('patient.create')
            <a href="{{ route('patients.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Pasien
            </a>
            @endcan
            @can('patient.view')
            <a href="{{ route('patients.export') }}" class="btn btn-sm btn-success">
                <i class="bi bi-download me-1"></i>Export Excel
            </a>
            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i>Import Excel
            </button>
            @endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>No. RM</th>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>J/K</th>
                    <th>No. HP</th>
                    <th>No. BPJS</th>
                    <th>Kunjungan Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $i => $p)
                <tr>
                    <td class="ps-3 text-muted">{{ $patients->firstItem() + $i }}</td>
                    <td><span class="badge bg-secondary">{{ $p->no_rm }}</span></td>
                    <td>
                        <a href="{{ route('patients.show', $p) }}" class="fw-semibold text-decoration-none">
                            {{ $p->nama }}
                        </a>
                        @if($p->tanggal_lahir)
                        <div class="text-muted" style="font-size:.75rem">{{ $p->umur }} tahun</div>
                        @endif
                    </td>
                    <td>{{ $p->tanggal_lahir ? $p->tanggal_lahir->format('d-m-Y') : '-' }}</td>
                    <td>{{ $p->jenis_kelamin == 'L' ? 'Laki-laki' : ($p->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>{{ $p->no_bpjs ?? '-' }}</td>
                    <td>
                        @if($p->latestRecord)
                            {{ $p->latestRecord->visit_date->format('d M Y') }}
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('patients.show', $p) }}" class="btn btn-xs btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('patient.edit')
                            <a href="{{ route('patients.edit', $p) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('patient.delete')
                            <form method="POST" action="{{ route('patients.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus pasien {{ $p->nama }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada data pasien
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="card-footer">
        {{ $patients->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>.btn-xs { padding: 3px 8px; font-size: .75rem; }</style>
@endpush

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Pasien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                        <a href="/template.xlsx">download template</a>    
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
