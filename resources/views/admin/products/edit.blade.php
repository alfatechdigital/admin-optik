@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">
<form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="card mb-3">
    <div class="card-header p-3">
        <i class="bi bi-pencil text-warning me-2"></i>Edit: {{ $product->nama }}
        <span class="badge bg-secondary ms-2">{{ $product->kode_produk }}</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                       value="{{ old('nama', $product->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Merek</label>
                <input type="text" name="merek" class="form-control" value="{{ old('merek', $product->merek) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Satuan</label>
                <select name="satuan" class="form-select">
                    @foreach(['pcs','pasang','box','lusin'] as $s)
                    <option value="{{ $s }}" {{ old('satuan',$product->satuan)==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                           value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Produk Aktif</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $product->deskripsi) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header p-3"><i class="bi bi-currency-dollar text-success me-2"></i>Harga & Stok</div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Harga Beli <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_beli" class="form-control"
                           value="{{ old('harga_beli', $product->harga_beli) }}" min="0" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_jual" class="form-control"
                           value="{{ old('harga_jual', $product->harga_jual) }}" min="0" required>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Stok Saat Ini <span class="text-danger">*</span></label>
                <input type="number" name="stok" class="form-control"
                       value="{{ old('stok', $product->stok) }}" min="0" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Minimum Stok</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', $product->stok_minimum) }}" min="0">
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header p-3"><i class="bi bi-image text-info me-2"></i>Foto Produk</div>
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-warning px-4">
        <i class="bi bi-check-lg me-1"></i>Update Produk
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
</form>
</div>
</div>

@endsection
