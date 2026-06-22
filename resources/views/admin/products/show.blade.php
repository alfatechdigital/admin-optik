@extends('layouts.admin')
@section('title', 'Detail Produk')
@section('page-title', 'Detail Produk')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-3 text-center mb-3">
          
            <span class="badge bg-secondary mb-1">{{ $product->kode_produk }}</span>
            <h5 class="fw-bold mb-1">{{ $product->nama }}</h5>
            @if($product->merek)
            <div class="text-muted small mb-2">{{ $product->merek }}</div>
            @endif
            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }} mb-3">
                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
            <div class="d-flex gap-2">
                @can('product.edit')
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm flex-fill">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                @endcan
                <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-cart-plus me-1"></i>Jual
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header p-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Produk</div>
            <div class="card-body p-3">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted" style="width:160px">Kategori</td><td>{{ $product->category->nama }}</td></tr>
                    <tr><td class="text-muted">Harga Beli</td><td>Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Harga Jual</td><td class="fw-bold text-primary fs-6">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td></tr>
                    <tr><td class="text-muted">Margin</td>
                        <td class="text-success">
                            Rp {{ number_format($product->harga_jual - $product->harga_beli, 0, ',', '.') }}
                            ({{ $product->harga_beli > 0 ? round((($product->harga_jual - $product->harga_beli) / $product->harga_beli) * 100) : 0 }}%)
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Stok</td>
                        <td>
                            <span class="badge fs-6 {{ $product->stok == 0 ? 'bg-danger' : ($product->stok_menipis ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ $product->stok }} {{ $product->satuan }}
                            </span>
                            <small class="text-muted ms-2">Min: {{ $product->stok_minimum }}</small>
                        </td>
                    </tr>
                    @if($product->deskripsi)
                    <tr><td class="text-muted">Deskripsi</td><td>{{ $product->deskripsi }}</td></tr>
                    @endif
                    <tr><td class="text-muted">Ditambahkan</td><td class="text-muted small">{{ $product->created_at->format('d M Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
