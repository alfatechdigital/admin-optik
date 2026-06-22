@extends('layouts.admin')
@section('title', 'Produk')
@section('page-title', 'Manajemen Produk')

@section('content')
<div class="card">
    <div class="card-header p-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div><i class="bi bi-box-seam text-primary me-2"></i>Daftar Produk</div>
        <div class="d-flex flex-wrap gap-2">
            <form class="d-flex flex-wrap gap-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nama / kode / merek..." value="{{ request('search') }}" style="width:200px">
                <select name="category_id" class="form-select form-select-sm" style="width:150px">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama }}
                    </option>
                    @endforeach
                </select>
                <select name="stok" class="form-select form-select-sm" style="width:130px">
                    <option value="">Semua Stok</option>
                    <option value="menipis" {{ request('stok')=='menipis' ? 'selected' : '' }}>Stok Menipis</option>
                </select>
                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
            @can('product.create')
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Produk
            </a>
            @endcan
            @can('product.view')
            <a href="{{ route('products.export') }}" class="btn btn-sm btn-success">
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
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $p)
                <tr>
                    <td class="ps-3 text-muted">{{ $products->firstItem() + $i }}</td>
                    
                    <td><span class="badge bg-secondary">{{ $p->kode_produk }}</span></td>
                    <td>
                        <div class="fw-semibold">{{ $p->nama }}</div>
                        @if($p->merek)
                        <small class="text-muted">{{ $p->merek }}</small>
                        @endif
                    </td>
                    <td>{{ $p->category->nama }}</td>
                    <td class="fw-semibold">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $p->stok == 0 ? 'bg-danger' : ($p->stok_menipis ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $p->stok }} {{ $p->satuan }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('products.show', $p) }}" class="btn btn-xs btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('product.edit')
                            <a href="{{ route('products.edit', $p) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('product.delete')
                            <form method="POST" action="{{ route('products.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus produk {{ $p->nama }}?')">
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
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada produk
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer">{{ $products->links() }}</div>
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
                <h5 class="modal-title" id="importModalLabel">Import Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            Pastikan file memiliki kolom: Kode Produk, Nama, Deskripsi, Merek, Kategori, Harga Beli, Harga Jual, Stok, Stok Minimum, Satuan, Status
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
