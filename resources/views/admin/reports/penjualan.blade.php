@extends('layouts.admin')
@section('title','Laporan Penjualan')
@section('page-title','Laporan Penjualan')

@section('content')
{{-- Filter --}}
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
            <div>
                <label class="form-label small mb-1">Jenis Pasien</label>
                <select name="jenis" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tampilkan Semua</option>
                    <option value="bpjs" {{ request('jenis') == 'bpjs' ? 'selected' : '' }}>BPJS</option>
                    <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i>Cetak
            </button>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Transaksi</div>
            <div class="fs-3 fw-bold text-primary">{{ $summary['total_transaksi'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Omzet</div>
            <div class="fw-bold text-success" style="font-size:1.1rem">Rp {{ number_format($summary['total_omzet'],0,',','.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Diskon</div>
            <div class="fw-bold text-danger" style="font-size:1.1rem">Rp {{ number_format($summary['total_diskon'],0,',','.') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 text-center">
            <div class="text-muted small">Total Potongan BPJS</div>
            <div class="fw-bold text-info" style="font-size:1.1rem">Rp {{ number_format($summary['total_potongan_bpjs'],0,',','.') }}</div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header p-3">Detail Penjualan {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. Transaksi</th><th>Pasien</th><th>Items</th>
                    <th>Harga Jual</th><th>Diskon</th>
                    <th>Potongan BPJS</th><th>Uang Muka</th><th>Pelunasan</th>
                    <th>Total Bayar</th><th>Status</th><th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="ps-3">
                        <a href="{{ route('transactions.show',$trx) }}" class="text-decoration-none fw-semibold">
                            {{ $trx->no_transaksi }}
                        </a>
                    </td>
                    <td>{{ $trx->patient->nama ?? 'Umum' }}</td>
                    <td>{{ $trx->items->sum('qty') }} item</td>
                    <!-- harga jual adiambil dari tabel transaksi seperti yang lainnya -->
                    <td>Rp {{ number_format($trx->harga_jual,0,',','.') }}</td>
                    <td>{{ $trx->potongan > 0 ? 'Rp '.number_format($trx->potongan,0,',','.') : '-' }}</td>
                    <td>{{ $trx->potongan_bpjs > 0 ? 'Rp '.number_format($trx->potongan_bpjs,0,',','.') : '-' }}</td>
                    <td>{{ $trx->uang_muka > 0 ? 'Rp '.number_format($trx->uang_muka,0,',','.') : '-' }}</td>
                    <td>{{ $trx->pelunasan > 0 ? 'Rp '.number_format($trx->pelunasan,0,',','.') : '-' }}</td>
                    <td class="fw-semibold">Rp {{ number_format($trx->total_bayar,0,',','.') }}</td>
                    <td>
                        @if($trx->status == 'lunas')
                            <span class="badge bg-success">Lunas</span>
                        @elseif($trx->status == 'belum lunas')
                            <span class="badge bg-warning">Belum Lunas</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($trx->status) }}</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $trx->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada transaksi pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
