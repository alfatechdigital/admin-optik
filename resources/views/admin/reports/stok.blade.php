{{-- resources/views/admin/reports/stok.blade.php --}}
@extends('layouts.admin')
@section('title','Laporan Stok')
@section('page-title','Laporan Stok Produk')
@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Produk</div>
            <div class="fs-3 fw-bold">{{ $summary['total_produk'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Stok Menipis</div>
            <div class="fs-3 fw-bold text-warning">{{ $summary['stok_menipis'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Stok Habis</div>
            <div class="fs-3 fw-bold text-danger">{{ $summary['stok_habis'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Nilai Stok</div>
            <div class="fw-bold text-success" style="font-size:.95rem">Rp {{ number_format($summary['nilai_stok'],0,',','.') }}</div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header p-3 d-flex justify-content-between">
        <div><i class="bi bi-boxes text-primary me-2"></i>Status Stok Semua Produk</div>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Kode</th><th>Nama Produk</th><th>Kategori</th>
                    <th class="text-center">Stok</th><th class="text-center">Min. Stok</th>
                    <th>Harga Beli</th><th>Harga Jual</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                <tr class="{{ $p->stok == 0 ? 'table-danger' : ($p->stok_menipis ? 'table-warning' : '') }}">
                    <td class="ps-3"><span class="badge bg-secondary">{{ $p->kode_produk }}</span></td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->category->nama }}</td>
                    <td class="text-center fw-bold">{{ $p->stok }}</td>
                    <td class="text-center text-muted">{{ $p->stok_minimum }}</td>
                    <td>Rp {{ number_format($p->harga_beli,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->harga_jual,0,',','.') }}</td>
                    <td>
                        @if($p->stok == 0)
                            <span class="badge bg-danger">Habis</span>
                        @elseif($p->stok_menipis)
                            <span class="badge bg-warning text-dark">Menipis</span>
                        @else
                            <span class="badge bg-success">Aman</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
