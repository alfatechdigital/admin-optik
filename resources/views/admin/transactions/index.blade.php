{{-- resources/views/admin/transactions/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi')

@push('styles')
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
            overflow: hidden;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
            border-bottom-color: rgba(0, 0, 0, .05);
        }

        .table-light th {
            background-color: rgba(248, 249, 250, 0.8);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(0, 0, 0, .05);
        }

        .btn-action {
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
    <div class="card glass-card border-0 mb-4">
        <div class="card-header bg-transparent border-0 p-4 d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary d-flex align-items-center">
                <i class="bi bi-receipt-cutoff me-2 fs-4"></i>Semua Transaksi
            </h5>
            <form class="d-flex flex-wrap gap-2" method="GET">
                {{-- Filter Pencarian --}}
                <div class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm" style="width:230px;">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 ps-0"
                        placeholder="No. Transaksi/Pasien..." value="{{ request('search') }}" onchange="this.form.submit()">
                </div>

                {{-- Filter Dropdown: Status Pengambilan --}}
                <div class="input-group input-group-sm rounded-pill border shadow-sm bg-white overflow-hidden" style="width: 200px; transition: all 0.2s ease;">
    <span class="input-group-text bg-white border-0 text-muted pe-1">
        <i class="bi bi-box-seam-fill"></i>
    </span>
    
    <select name="diambil" 
            class="form-select border-0 ps-2 fw-semibold text-muted shadow-none" 
            style="font-size: 0.85rem; cursor: pointer; background-color: transparent;" 
            onchange="this.form.submit()">
        <option value="" class="text-muted fw-normal">Semua Status Ambil</option>
        <option value="1" {{ request('diambil') == '1' ? 'selected' : '' }} class="text-muted"> Sudah Diambil</option>
        <option value="0" {{ request('diambil') == '0' ? 'selected' : '' }} class="text-muted"> Belum Diambil</option>
    </select>
</div>

                {{-- Filter Range Tanggal --}}
                <div class="input-group input-group-sm rounded-pill overflow-hidden shadow-sm flex-nowrap" style="width: auto;">
                    <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="from" class="form-control border-0 px-2" value="{{ request('from') }}"
                        title="Dari Tanggal" onchange="this.form.submit()">
                    <span class="input-group-text bg-light border-0 text-muted px-2">-</span>
                    <input type="date" name="to" class="form-control border-0 px-2" value="{{ request('to') }}"
                        title="Sampai Tanggal" onchange="this.form.submit()">
                </div>
            </form>
        </div>
        
        <div class="table-responsive px-3 pb-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 rounded-start">No. Transaksi</th>
                        <th>Pasien</th>
                        <th>Dokter/Kasir</th>
                        <th>Total</th>
                        <!-- <th>Potongan BPJS</th> -->
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        @php
                            $markup = $trx->tambahan['markup'] ?? null;
                            $d_total_bayar = $markup ? ($markup['total_bayar'] ?? $trx->total_bayar) : $trx->total_bayar;
                            $d_potongan_bpjs = $markup ? ($markup['potongan_bpjs'] ?? $trx->potongan_bpjs) : $trx->potongan_bpjs;
                            $d_sisa = $markup ? ($markup['sisa'] ?? $trx->sisa) : $trx->sisa;
                        @endphp
                        <tr class="{{ $trx->status == 'batal' ? 'opacity-50 grayscale' : '' }}">
                            <td class="ps-4 fw-bold text-primary">
                                {{ $trx->no_transaksi }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 35px; height: 35px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">
                                            @if($trx->typefaktur == 1)
                                                <span class="text-muted small">Umum</span>
                                                @if($trx->patient)
                                                    <div class="small fw-normal">({{ $trx->patient->nama }})</div>
                                                @elseif($trx->nama_pasien)
                                                    <div class="small fw-normal">({{ $trx->nama_pasien }})</div>
                                                @endif
                                            @else
                                                {!! $trx->patient->nama ?? ($trx->nama_pasien ?? '<span class="text-danger small">BPJS (Tanpa Nama)</span>') !!}
                                            @endif
                                        </div>
                                        @if(isset($trx->patient->no_hp))
                                            <small class="text-muted">{{ $trx->patient->no_hp }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small"><i class="bi bi-person-badge me-1"></i>{{ $trx->nama_dokter ?? '-' }}</span>
                                <br>
                                <span class="text-muted small"><i class="bi bi-headset me-1"></i>{{ $trx->kasir->name ?? '-' }}</span>
                            </td>
                            <td class="fw-bold">Rp {{ number_format($d_total_bayar, 0, ',', '.') }}
                                @if($markup)
                                    <span class="text-danger" title="Data telah disesuaikan">*</span>
                                @endif
                                @if($d_potongan_bpjs > 0)
                                    <br><span class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i>Rp {{ number_format($d_potongan_bpjs, 0, ',', '.') }}</span>
                                @endif
                            </td> 
                            <td>
                                @if($trx->status == 'batal')
                                    <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.7rem">BATAL</span>
                                @elseif($trx->diambil == 1 || strtolower($trx->diambil) == 'ya' || $trx->diambil == '1')
                                    <span class="badge bg-primary rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.7rem">DIAMBIL</span>
                                @elseif($d_sisa > 0)
                                    <span class="badge bg-danger rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.7rem">
                                        -Rp {{ number_format($d_sisa, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge bg-success rounded-pill px-2 py-1 shadow-sm" style="font-size: 0.7rem">LUNAS</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <div class="fw-semibold text-dark">{{ $trx->created_at->format('d M Y') }}</div>
                                    {{ $trx->created_at->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="text-end pe-4" x-data="{ statusSisa: {{ $d_sisa }} }">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm" 
                                            type="button" 
                                            id="dropdownMenu{{ $trx->id }}" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false"
                                            style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-three-dots-vertical fs-5 text-secondary"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="dropdownMenu{{ $trx->id }}" style="min-width: 200px;">
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('transactions.show', $trx) }}">
                                                <i class="bi bi-eye me-2 text-primary"></i> Lihat Detail
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item py-2 d-flex align-items-center text-secondary" 
                                                    onclick="openPrintModal({{ $trx->id }}, {{ $trx->typefaktur == 2 ? 'true' : 'false' }})">
                                                <i class="bi bi-printer me-2 text-secondary"></i> Cetak Transaksi
                                            </button>
                                        </li>

                                        {{-- Menu Aksi Berdasarkan Status Pembayaran Asli Database ($trx->sisa) --}}
                                        @if($trx->status != 'batal' && $trx->diambil != 1 && strtolower($trx->diambil) != 'ya' && $trx->diambil != '1')
                                            <li>
                                                @if($trx->sisa <= 0)
                                                    {{-- Hanya muncul jika di database sudah LUNAS (tidak ada sisa tagihan) --}}
                                                    <form action="{{ route('pickup.confirm', $trx) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center text-success fw-bold" onclick="return confirm('Proses serah terima barang? Status akan diubah menjadi Sudah Diambil.')">
                                                            <i class="bi bi-check-circle-fill me-2 text-success"></i> Ambil Kacamata
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- Muncul jika masih ada sisa tagihan di database --}}
                                                   
                                                @endif
                                            </li>
                                        @endif

                                        @if($trx->status != 'batal')
                                            <li>
                                                <form action="{{ route('transactions.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Stok akan dikembalikan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 d-flex align-items-center text-danger">
                                                        <i class="bi bi-trash me-2 text-danger"></i> Hapus Transaksi
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        <li>
                                            <button type="button" class="dropdown-item py-2 d-flex align-items-center text-secondary" 
                                                    onclick="openMarkupModal({{ $trx->id }}, {{ json_encode($trx->tambahan['markup'] ?? null) }}, {{ json_encode($trx->items) }}, {{ $trx->harga_jual }}, {{ $trx->dp }}, {{ $trx->sisa }}, {{ $trx->potongan }}, {{ $trx->potongan_bpjs }}, {{ $trx->total_bayar }})">
                                                <i class="bi bi-pencil-square me-2 text-info"></i> Edit Data Transaksi
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider bg-light"></li>
<!-- 
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('transactions.edit', ['transaction' => $trx->id, 'step' => 1]) }}">
                                                <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Data Transaksi (Step 1)
                                            </a>
                                        </li> -->
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('transactions.edit', ['transaction' => $trx->id, 'step' => 2]) }}">
                                                <i class="bi bi-person me-2 text-warning"></i> Edit Pasien
                                            </a>
                                        </li>
                                        <!-- <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('transactions.edit', ['transaction' => $trx->id, 'step' => 3]) }}">
                                                <i class="bi bi-box me-2 text-warning"></i> Edit Produk
                                            </a>
                                        </li> -->
                                        <li>
                                            <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('transactions.edit', ['transaction' => $trx->id, 'step' => 4]) }}">
                                                <i class="bi bi-file-earmark-text me-2 text-warning"></i> Edit Data Tambahan
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="bi bi-inbox fs-1 mb-3 opacity-50"></i>
                                    <h5>Belum Ada Transaksi</h5>
                                    <p class="small mb-0">Coba ubah filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="card-footer bg-transparent border-0 px-4 pb-4 pt-2">
                {{ $transactions->appends(request()->query())->links('vendor.pagination.custom-bootstrap') }}
            </div>
        @endif
    </div>

    <input type="hidden" id="trx_id">

    @include('admin.transactions.partials.modal-print')
    @include('admin.transactions.partials.modal-markup')
@endsection

@push('scripts')

    <script>
        // Post-Save Detection & Auto Pop-up
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const lastId = urlParams.get('just_saved');

            if (lastId) {
                let isBpjsSaved = false;
                @if($transactions->count() > 0 && $transactions->first()->typefaktur == 2)
                    isBpjsSaved = true;
                @endif

                openPrintModal(lastId, isBpjsSaved);

                setTimeout(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                }, 500);

                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
@endpush