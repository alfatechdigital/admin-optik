{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('reports.penjualan') }}" class="text-decoration-none">
                <div class="card p-4 h-100 text-center" style="transition:transform .15s"
                    onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                    <div class="fs-1 mb-3">📊</div>
                    <h5 class="fw-bold">Laporan Penjualan</h5>
                    <p class="text-muted small">Rekap transaksi, omzet, dan diskon per periode</p>
                </div>
            </a>
        </div>
        <!-- <div class="col-md-4">
            <a href="{{ route('reports.produk-terlaris') }}" class="text-decoration-none">
                <div class="card p-4 h-100 text-center" style="transition:transform .15s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                    <div class="fs-1 mb-3">🏆</div>
                    <h5 class="fw-bold">Produk Terlaris</h5>
                    <p class="text-muted small">Ranking produk berdasarkan jumlah terjual</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('reports.stok') }}" class="text-decoration-none">
                <div class="card p-4 h-100 text-center" style="transition:transform .15s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                    <div class="fs-1 mb-3">📦</div>
                    <h5 class="fw-bold">Laporan Stok</h5>
                    <p class="text-muted small">Status stok produk, nilai inventori, dan stok menipis</p>
                </div>
            </a>
        </div> -->
    </div>
@endsection