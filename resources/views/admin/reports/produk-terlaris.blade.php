@extends('layouts.admin')
@section('title','Produk Terlaris')
@section('page-title','Laporan Produk Terlaris')

@section('content')
<div class="card mb-3">
    <div class="card-body p-3">
        <form class="d-flex flex-wrap gap-2 align-items-end" method="GET">
            <div>
                <label class="form-label small mb-1">Dari Tanggal</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div>
                <label class="form-label small mb-1">Sampai Tanggal</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header p-3">
        <i class="bi bi-trophy text-warning me-2"></i>
        Top 15 Produk Terlaris —
        {{ \Carbon\Carbon::parse($from)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3 text-center" style="width:50px">Rank</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th class="text-center">Terjual (qty)</th>
                    <th class="text-end pe-3">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $i => $item)
                <tr>
                    <td class="text-center ps-3">
                        @if($i == 0)
                            <span class="badge bg-warning text-dark fs-6">🥇</span>
                        @elseif($i == 1)
                            <span class="badge bg-secondary fs-6">🥈</span>
                        @elseif($i == 2)
                            <span class="badge" style="background:#cd7f32;font-size:.85rem">🥉</span>
                        @else
                            <span class="text-muted fw-bold">{{ $i + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $item->product->nama ?? 'Produk dihapus' }}</div>
                        @if($item->product)
                        <span class="badge bg-secondary" style="font-size:.7rem">{{ $item->product->kode_produk }}</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $item->product?->category?->nama ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-6">
                            {{ number_format($item->total_terjual) }} pcs
                        </span>
                    </td>
                    <td class="text-end pe-3 fw-bold text-success">
                        Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="bi bi-trophy fs-1 d-block mb-2 opacity-25"></i>
                        Tidak ada data pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
