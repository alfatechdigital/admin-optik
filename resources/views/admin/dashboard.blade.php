@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <style>
        .grafik-bar-wrap {
            height: 100px;
            display: flex;
            align-items: flex-end;
            gap: 6px;
        }

        .grafik-bar {
            flex: 1;
            background: #1e2a5e;
            opacity: .7;
            border-radius: 4px 4px 0 0;
            min-height: 4px;
            transition: opacity .2s;
        }

        .grafik-bar:hover {
            opacity: 1;
        }

        .grafik-label {
            font-size: .7rem;
            color: #6c757d;
            text-align: center;
        }
    </style>
@endpush

@section('content')

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card stat-card border-primary p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Total Pasien</div>
                        <div class="stat-val text-primary">{{ number_format($totalPasien) }}</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        {{-- pengambilan hari ini --}}
        <div class="col-6 col-md-4">
            <div class="card stat-card border-success p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Pengambilan Hari Ini</div>
                        <div class="stat-val text-success">{{ $pengambilanHari }}</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card stat-card border-info p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Transaksi Hari Ini</div>
                        <div class="stat-val text-info">{{ $transaksiHari }}</div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
        @if($isSuperAdmin)
            <div class="col-6 col-md-4">
                <div class="card stat-card border-success p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Omzet Hari Ini</div>
                            <div class="stat-val text-success" style="font-size:1.15rem">
                                Rp {{ number_format($omzetHari, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- jumlah user -->
            <div class="col-6 col-md-4">
                <div class="card stat-card border-info p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Jumlah User</div>
                            <div class="stat-val text-info">{{ $jumlahUser }}</div>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- jumlah produk tersisa -->
            <div class="col-6 col-md-4">
                <div class="card stat-card border-info p-3">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Jumlah Produk Tersisa</div>
                            <div class="stat-val text-info">{{ $jumlahProduk }}</div>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- <div class="col-6 col-md-3">
                        <div class="card stat-card border-warning p-3">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="stat-label">Produk Aktif</div>
                                    <div class="stat-val text-warning">{{ number_format($totalProduk) }}</div>
                                </div>
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-box-seam-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div> -->
    </div>

    <div class="row g-3 mb-3">
        {{-- Status Transaksi Chart (Public) --}}
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <div class="card-header px-0 pt-0 mb-3">
                    <i class="bi bi-pie-chart-fill text-info me-2"></i>Status Transaksi
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @php
                        $totalTrx = array_sum($grafikStatusTransaksi);
                        $colors = ['Lunas' => '#198754', 'Pending' => '#ffc107', 'Batal' => '#dc3545'];
                    @endphp
                    @foreach($grafikStatusTransaksi as $status => $count)
                        @php
                            $persen = $totalTrx ? ($count / $totalTrx * 100) : 0;
                            $color = $colors[$status] ?? '#6c757d';
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                                <span class="fw-semibold">{{ $status }}</span>
                                <span>{{ $count }} ({{ number_format($persen, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $persen }}%; background-color: {{ $color }};" aria-valuenow="{{ $persen }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Status Pengambilan Chart (Public) --}}
        <div class="col-md-6">
            <div class="card p-3 h-100">
                <div class="card-header px-0 pt-0 mb-3">
                    <i class="bi bi-bag-check text-success me-2"></i>Status Pengambilan
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @php
                        $totalPickup = array_sum($grafikStatusPengambilan);
                        $pickupColors = ['Sudah Diambil' => '#198754', 'Belum Diambil' => '#fd7e14'];
                    @endphp
                    @foreach($grafikStatusPengambilan as $status => $count)
                        @php
                            $persen = $totalPickup ? ($count / $totalPickup * 100) : 0;
                            $color = $pickupColors[$status] ?? '#6c757d';
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                                <span class="fw-semibold">{{ $status }}</span>
                                <span>{{ $count }} ({{ number_format($persen, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $persen }}%; background-color: {{ $color }};" aria-valuenow="{{ $persen }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Grafik Omzet (Super Admin Only) --}}
        @if($isSuperAdmin)
            <div class="card p-3 h-100">
                <div class="card-header px-0 pt-0 mb-3">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>Omzet 7 Hari Terakhir
                </div>
                @php $maxOmzet = $grafikOmzet->max() ?: 1; @endphp
                <div class="grafik-bar-wrap">
                    @foreach($grafikOmzet as $label => $val)
                        <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px;">
                            <small class="text-muted" style="font-size:.65rem">
                                Rp{{ number_format($val / 1000, 0) }}k
                            </small>
                            <div class="grafik-bar w-100" style="height:{{ max(4, ($val / $maxOmzet) * 90) }}px"
                                title="Rp {{ number_format($val, 0, ',', '.') }}"></div>
                            <div class="grafik-label">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <!-- <div class="col-md-4">
                        <div class="card p-3 h-100">
                            <div class="card-header px-0 pt-0 mb-3">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Stok Menipis
                            </div>
                            @forelse($stokMenipis as $p)
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.82rem">{{ $p->nama }}</div>
                                        <div class="text-muted" style="font-size:.72rem">{{ $p->category->nama }}</div>
                                    </div>
                                    <span class="badge {{ $p->stok == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $p->stok }} {{ $p->satuan }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-check-circle fs-3 text-success"></i>
                                    <div class="mt-1 small">Semua stok aman</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- Transaksi Terbaru --}}
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header p-3 d-flex justify-content-between align-items-center">
                                <div><i class="bi bi-clock-history text-primary me-2"></i>Transaksi Terbaru</div>
                                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary btn-sm">Lihat semua</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">No. Transaksi</th>
                                            <th>Pasien</th>
                                            <th>Kasir</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transaksiTerbaru as $trx)
                                            <tr>
                                                <td class="ps-3">
                                                    <a href="{{ route('transactions.show', $trx) }}"
                                                        class="text-decoration-none fw-semibold">
                                                        {{ $trx->no_transaksi }}
                                                    </a>
                                                </td>
                                                <td>{{ $trx->patient->nama ?? '-' }}</td>
                                                <td>{{ $trx->kasir->name ?? '-' }}</td>
                                                <td>Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $trx->status }}">{{ ucfirst($trx->status) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi hari ini</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> -->

        {{-- Top Produk --}}
        <!-- <div class="col-md-5">
                    <div class="card">
                        <div class="card-header p-3 d-flex justify-content-between align-items-center">
                            <div><i class="bi bi-bag-check text-success me-2"></i>Pesanan Mau Diambil (Hari Ini & Terlewat)</div>
                            <a href="{{ route('pickup.index') }}" class="btn btn-outline-success btn-sm">Lihat semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">No. Transaksi</th>
                                        <th>Pasien</th>
                                        <th>Tgl. Pengambilan</th>
                                        <th>Produk</th>
                                        <th>Sisa Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pesananDiambil as $trx)
                                        <tr>
                                            <td class="ps-3">
                                                <a href="{{ route('transactions.show', $trx) }}"
                                                    class="text-decoration-none fw-semibold">
                                                    {{ $trx->no_transaksi }}
                                                </a>
                                            </td>
                                            <td>{{ $trx->patient->nama ?? ($trx->nama_pasien ?? 'Umum') }}</td>
                                            <td>
                                                @if($trx->tgl_selesai_janji)
                                                    @php
                                                        $isPast = \Carbon\Carbon::parse($trx->tgl_selesai_janji)->isPast();
                                                        $isToday = \Carbon\Carbon::parse($trx->tgl_selesai_janji)->isToday();
                                                    @endphp
                                                    <span
                                                        class="{{ ($isPast && !$isToday) ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                                        {{ \Carbon\Carbon::parse($trx->tgl_selesai_janji)->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <ul class="mb-0 ps-3" style="font-size: 0.8rem">
                                                    @foreach($trx->items as $item)
                                                        <li>{{ $item->qty }}x {{ $item->nama_produk }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>
                                                @php
                                                    $harga = $trx->harga_jual ?? $trx->total_harga ?? 0;
                                                    $dp = $trx->dp ?? $trx->bayar ?? 0;
                                                    $potongan = $trx->potongan ?? $trx->diskon_nominal ?? 0;
                                                    $sisa = max(0, $harga - $potongan - $dp);
                                                @endphp
                                                @if($sisa > 0)
                                                    <span class="text-danger fw-semibold">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                                                @else
                                                    <span class="text-success"><i class="bi bi-check2-all"></i> Lunas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Tidak ada pesanan yang harus diambil
                                                hari ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-3 d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-bag-check text-success me-2"></i>Daftar Tunggu Pengambilan Kacamata</div>
                    <a href="{{ route('pickup.index') }}" class="btn btn-outline-success btn-sm">Lihat semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Transaksi</th>
                                <th>Pasien</th>
                                <th>Tanggal Janji</th>
                                <th>Produk</th>
                                <th>Sisa Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesananDiambil as $trx)
                                <tr>
                                    <td class="ps-3">
                                        <a href="{{ route('transactions.show', $trx) }}"
                                            class="text-decoration-none fw-semibold">
                                            {{ $trx->no_transaksi }}
                                        </a>
                                    </td>
                                    <td>{{ $trx->patient->nama ?? ($trx->nama_pasien ?? 'Umum') }}</td>
                                    <td>{{ $trx->tgl_selesai_janji ? \Carbon\Carbon::parse($trx->tgl_selesai_janji)->format('d/m/Y') : '-' }}
                                    </td>
                                    <!-- @if($trx->tgl_selesai_janji)
                                                    @php
                                                        $dt = \Carbon\Carbon::parse($trx->tgl_selesai_janji);
                                                        $isPast = $dt->isPast() && !$dt->isToday();
                                                        $isToday = $dt->isToday();
                                                        $isTomorrow = $dt->isTomorrow();
                                                    @endphp
                                                    <span
                                                        class="{{ $isPast ? 'text-danger fw-bold' : ($isToday ? 'text-warning fw-bold' : 'text-primary') }}">
                                                        {{ $dt->format('d/m/Y') }}
                                                        @if($isPast) <span class="badge bg-danger small" style="font-size:0.6rem">OVERDUE</span> @endif
                                                        @if($isToday) <span class="badge bg-warning text-dark small" style="font-size:0.6rem">HARI INI</span> @endif
                                                        @if($isTomorrow) <span class="badge bg-primary small" style="font-size:0.6rem">BESOK</span> @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif -->
                                    <!-- </td> -->
                                    <td>
                                        <ul class="mb-0 ps-3" style="font-size: 0.8rem">
                                            @foreach($trx->items as $item)
                                                <li>{{ $item->qty }}x {{ $item->nama_produk }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @php
                                            $harga = $trx->harga_jual ?? $trx->total_harga ?? 0;
                                            $dp = $trx->dp ?? $trx->bayar ?? 0;
                                            $potongan = $trx->potongan ?? $trx->diskon_nominal ?? 0;
                                            $sisa = max(0, $harga - $potongan - $dp);
                                        @endphp
                                        @if($sisa > 0)
                                            <span class="text-danger fw-semibold">Rp {{ number_format($sisa, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-success"><i class="bi bi-check2-all"></i> Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Semua pesanan sudah diambil</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection