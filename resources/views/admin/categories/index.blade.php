@extends('layouts.admin')
@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')

@section('content')
<div class="row g-3">
    {{-- Form Tambah --}}
    @can('category.create')
    <div class="col-md-4">
        <div class="card p-3">
            <div class="card-header px-0 pt-0 pb-3 mb-3">
                <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Kategori
            </div>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama') }}" required placeholder="Contoh: Frame Kacamata">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"
                              placeholder="Deskripsi kategori...">{{ old('deskripsi') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
                </button>
            </form>
        </div>
    </div>
    @endcan

    {{-- Daftar Kategori --}}
    <div class="{{ auth()->user()->can('category.create') ? 'col-md-8' : 'col-12' }}">
        <div class="card">
            <div class="card-header p-3">
                <i class="bi bi-tags text-primary me-2"></i>Daftar Kategori
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Produk</th>
                            <th class="text-center">Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $i => $cat)
                        <tr>
                            <td class="ps-3 text-muted">{{ $categories->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $cat->nama }}</td>
                            <td class="text-muted small">{{ $cat->deskripsi ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $cat->products_count }} produk
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $cat->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @can('category.edit')
                                    <button class="btn btn-xs btn-outline-warning"
                                            onclick="editKategori({{ $cat->id }}, '{{ addslashes($cat->nama) }}', '{{ addslashes($cat->deskripsi) }}', {{ $cat->is_active ? 1 : 0 }})"
                                            title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endcan
                                    @can('category.delete')
                                    <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Hapus kategori {{ $cat->nama }}?')">
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
                            <td colspan="6" class="text-center text-muted py-4">Belum ada kategori</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
            <div class="card-footer">{{ $categories->links() }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="edit-form">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" id="edit-deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit-active" value="1">
                        <label class="form-check-label" for="edit-active">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-lg me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>.btn-xs { padding: 3px 8px; font-size: .75rem; }</style>
@endpush

@push('scripts')
<script>
function editKategori(id, nama, deskripsi, isActive) {
    document.getElementById('edit-form').action = `/categories/${id}`;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-deskripsi').value = deskripsi;
    document.getElementById('edit-active').checked = isActive === 1;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endpush
@endsection
