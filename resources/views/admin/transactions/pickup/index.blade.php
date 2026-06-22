{{-- resources/views/admin/pickup/index.blade.php --}}
@extends('layouts.admin')
@section('title', 'Pengambilan Kacamata')
@section('page-title', 'Pengambilan Kacamata')

@push('styles')
<style>
    /* ── Glass Card (konsisten dengan transaksi) ── */
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
    .table > :not(caption) > * > * {
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
    .badge-status {
        padding: 0.4em 0.8em;
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    .btn-action {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }

    /* ── Wizard Step Indicator ── */
    .wizard-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        padding: 1.5rem 2rem;
        background: rgba(248, 249, 250, 0.6);
        border-bottom: 1px solid rgba(0, 0, 0, .05);
    }
    .step-item {
        display: flex;
        align-items: center;
        flex: 1;
        max-width: 220px;
        position: relative;
    }
    .step-item:not(:last-child)::after {
        content: '';
        flex: 1;
        height: 2px;
        background: #dee2e6;
        margin: 0 0.75rem;
        transition: background 0.4s ease;
        min-width: 40px;
    }
    .step-item.completed:not(:last-child)::after {
        background: #198754;
    }
    .step-item.active:not(:last-child)::after {
        background: linear-gradient(to right, #0d6efd 0%, #dee2e6 100%);
    }
    .step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
        border: 2px solid #dee2e6;
        background: #fff;
        color: #adb5bd;
        transition: all 0.35s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .step-item.active .step-circle {
        border-color: #0d6efd;
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 4px 16px rgba(13, 110, 253, 0.35);
        transform: scale(1.08);
    }
    .step-item.completed .step-circle {
        border-color: #198754;
        background: #198754;
        color: #fff;
        box-shadow: 0 4px 16px rgba(25, 135, 84, 0.25);
    }
    .step-label {
        margin-left: 0.65rem;
        display: flex;
        flex-direction: column;
    }
    .step-label .step-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        transition: color 0.3s;
    }
    .step-label .step-sub {
        font-size: 0.7rem;
        color: #adb5bd;
    }
    .step-item.active .step-label .step-title  { color: #0d6efd; }
    .step-item.completed .step-label .step-title { color: #198754; }

    /* ── Step Panels ── */
    .step-panel {
        display: none;
        animation: fadeSlideIn 0.35s ease forwards;
    }
    .step-panel.active {
        display: block;
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Detail Card dalam Step 2 ── */
    .detail-section-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6c757d;
        border-bottom: 1px solid rgba(0,0,0,.06);
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px dashed rgba(0,0,0,.05);
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-row .label { font-size: 0.85rem; color: #6c757d; }
    .detail-row .value { font-size: 0.9rem; font-weight: 600; color: #212529; }

    /* ── Payment Input ── */
    .payment-box {
        background: rgba(13, 110, 253, 0.04);
        border: 1px solid rgba(13, 110, 253, 0.15);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
    }
    .payment-box.lunas {
        background: rgba(25, 135, 84, 0.05);
        border-color: rgba(25, 135, 84, 0.2);
    }

    /* ── Konfirmasi Step 3 ── */
    .confirm-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(13, 110, 253, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
    }
    .table-item-kacamata td { padding: 0.6rem 0.75rem; font-size: 0.875rem; }
    .table-item-kacamata tfoot td { font-weight: 700; border-top: 2px solid rgba(0,0,0,.08); }

    /* ── Print ── */
    @media print {
        .no-print { display: none !important; }
        .glass-card {
            box-shadow: none;
            border: 1px solid #dee2e6;
            backdrop-filter: none;
        }
    }
</style>
@endpush

@section('content')

{{--
    ╔══════════════════════════════════════════════════════════════╗
    ║  VARIABEL YANG DIHARAPKAN DARI CONTROLLER:                  ║
    ║  $step (int: 1|2|3)   — step aktif saat ini                 ║
    ║  $results (Collection) — hasil pencarian (step 1)           ║
    ║  $transaction (Model) — transaksi terpilih (step 2 & 3)     ║
    ║  $search (string)     — kata kunci terakhir                 ║
    ╚══════════════════════════════════════════════════════════════╝
--}}

@php $currentStep = $step ?? 1; @endphp

<div class="card glass-card border-0 mb-4">

    {{-- ─────────────────── WIZARD STEP INDICATOR ─────────────────── --}}
    <div class="wizard-steps">

        {{-- Step 1 --}}
        <div class="step-item {{ $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : '' }}">
            <div class="step-circle">
                @if($currentStep > 1)
                    <i class="bi bi-check-lg"></i>
                @else
                    <i class="bi bi-search"></i>
                @endif
            </div>
            <div class="step-label">
                <span class="step-title">Cari Transaksi</span>
                <span class="step-sub">No. / Nama / No. HP</span>
            </div>
        </div>

        {{-- Connector --}}
        <div class="flex-fill" style="height:2px;background:{{ $currentStep > 1 ? '#198754' : '#dee2e6' }};margin:0 0.5rem;transition:background .4s;"></div>

        {{-- Step 2 --}}
        <div class="step-item {{ $currentStep >= 2 ? ($currentStep > 2 ? 'completed' : 'active') : '' }}">
            <div class="step-circle">
                @if($currentStep > 2)
                    <i class="bi bi-check-lg"></i>
                @elseif($currentStep == 2)
                    <i class="bi bi-receipt"></i>
                @else
                    2
                @endif
            </div>
            <div class="step-label">
                <span class="step-title">Detail & Bayar</span>
                <span class="step-sub">Verifikasi Pembayaran</span>
            </div>
        </div>

        {{-- Connector --}}
        <div class="flex-fill" style="height:2px;background:{{ $currentStep > 2 ? '#198754' : '#dee2e6' }};margin:0 0.5rem;transition:background .4s;"></div>

        {{-- Step 3 --}}
        <div class="step-item {{ $currentStep >= 3 ? 'active' : '' }}">
            <div class="step-circle">
                @if($currentStep == 3)
                    <i class="bi bi-bag-check-fill"></i>
                @else
                    3
                @endif
            </div>
            <div class="step-label">
                <span class="step-title">Konfirmasi</span>
                <span class="step-sub">Selesai & Struk</span>
            </div>
        </div>

    </div>
    {{-- ─────────────────────────────────────────────────────────── --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  STEP 1 — CARI TRANSAKSI                                  --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="step-panel {{ $currentStep == 1 ? 'active' : '' }}">

        {{-- Header --}}
        <div class="card-header bg-transparent border-0 p-4 d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary d-flex align-items-center">
                <i class="bi bi-search me-2 fs-4"></i>Cari Transaksi Kacamata
            </h5>
        </div>

        {{-- Search Form --}}
        <div class="px-4 pb-4">
            <form method="GET" action="{{ route('pickup.index') }}">
                <input type="hidden" name="step" value="1">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="input-group rounded-pill overflow-hidden shadow-sm" style="max-width:360px;">
                        <span class="input-group-text bg-white border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search"
                               class="form-control border-0 ps-0"
                               placeholder="No. Transaksi / Nama Pasien / No. HP..."
                               value="{{ $search ?? '' }}"
                               autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm btn-action">
                        <i class="bi bi-funnel-fill me-1"></i> Cari
                    </button>
                    @if(!empty($search))
                    <a href="{{ route('pickup.index') }}" class="btn btn-outline-danger rounded-pill px-3 shadow-sm btn-action">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Hasil Pencarian --}}
        @isset($results)
        <div class="table-responsive px-3 pb-4">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 rounded-start">No. Transaksi</th>
                        <th>Nama Pasien</th>
                        <th>No. HP</th>
                        <th>Status Kacamata</th>
                        <th>Estimasi Selesai</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $trx)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $trx->no_transaksi }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width:35px;height:35px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="fw-semibold text-dark">
                                    {!! $trx->patient->nama ?? '<span class="text-muted small">Umum</span>' !!}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <i class="bi bi-phone me-1"></i>
                                {{ $trx->patient->no_hp ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $kacamataBadge = match($trx->status_kacamata ?? 'proses') {
                                    'siap'     => ['bg-success',  'bi-eyeglasses',       'Siap Diambil'],
                                    'proses'   => ['bg-warning text-dark', 'bi-hourglass-split', 'Sedang Diproses'],
                                    'diambil'  => ['bg-secondary','bi-check2-all',       'Sudah Diambil'],
                                    default    => ['bg-secondary','bi-question-circle',  'Tidak Diketahui'],
                                };
                            @endphp
                            <span class="badge {{ $kacamataBadge[0] }} rounded-pill badge-status shadow-sm">
                                <i class="bi {{ $kacamataBadge[1] }} me-1"></i>{{ $kacamataBadge[2] }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">
                                @if($trx->estimasi_selesai)
                                    <div class="fw-semibold text-dark">
                                        {{ \Carbon\Carbon::parse($trx->estimasi_selesai)->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <form method="GET" action="{{ route('pickup.index') }}" class="d-inline">
                                <input type="hidden" name="step" value="2">
                                <input type="hidden" name="transaction_id" value="{{ $trx->id }}">
                                <button type="submit"
                                        class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm btn-action"
                                        title="Pilih Transaksi Ini">
                                    <i class="bi bi-hand-index-thumb me-1"></i> Pilih
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="bi bi-eyeglasses fs-1 mb-3 opacity-50"></i>
                                <h5>Transaksi Tidak Ditemukan</h5>
                                <p class="small mb-0">Coba gunakan kata kunci yang berbeda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
        <div class="px-4 pb-4">
            {{ $results->appends(['step' => 1, 'search' => $search])->links() }}
        </div>
        @endif

        @else
        {{-- Belum ada pencarian --}}
        <div class="text-center py-5 mb-3">
            <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                <div style="width:80px;height:80px;border-radius:50%;background:rgba(13,110,253,0.07);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                    <i class="bi bi-eyeglasses fs-1 text-primary opacity-75"></i>
                </div>
                <h5 class="fw-semibold">Mulai Pencarian Transaksi</h5>
                <p class="small text-muted mb-0">Masukkan nomor transaksi, nama pasien, atau nomor HP di atas.</p>
            </div>
        </div>
        @endisset

    </div>
    {{-- /STEP 1 --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  STEP 2 — DETAIL & PEMBAYARAN                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="step-panel {{ $currentStep == 2 ? 'active' : '' }}">

        @isset($transaction)
        {{-- Header --}}
        <div class="card-header bg-transparent border-0 p-4 d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary d-flex align-items-center">
                <i class="bi bi-receipt me-2 fs-4"></i>Detail Transaksi &amp; Pembayaran
            </h5>
            <a href="{{ route('pickup.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm btn-action">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="px-4 pb-4">
            <div class="row g-4">

                {{-- Kolom Kiri: Info Pasien + Item --}}
                <div class="col-lg-7">

                    {{-- Info Pasien --}}
                    <div class="glass-card p-4 mb-4" style="border-radius:16px;">
                        <p class="detail-section-title"><i class="bi bi-person-badge me-1"></i>Informasi Pasien</p>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px;font-size:1.4rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-6 text-dark">
                                    {!! $transaction->patient->nama ?? '<span class="text-muted">Pasien Umum</span>' !!}
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-phone me-1"></i>{{ $transaction->patient->no_hp ?? '-' }}
                                </small>
                            </div>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-hash me-1"></i>No. Transaksi</span>
                            <span class="value text-primary">{{ $transaction->no_transaksi }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-calendar3 me-1"></i>Tanggal Transaksi</span>
                            <span class="value">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-headset me-1"></i>Kasir</span>
                            <span class="value">{{ $transaction->kasir->name ?? '-' }}</span>
                        </div>
                        @if($transaction->estimasi_selesai)
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-calendar-check me-1"></i>Estimasi Selesai</span>
                            <span class="value text-success">
                                {{ \Carbon\Carbon::parse($transaction->estimasi_selesai)->format('d M Y') }}
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Detail Item --}}
                    <div class="glass-card p-4" style="border-radius:16px;">
                        <p class="detail-section-title"><i class="bi bi-eyeglasses me-1"></i>Item Kacamata</p>
                        <table class="table table-item-kacamata mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Keterangan</th>
                                    <th class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaction->items ?? [] as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->nama_item }}</td>
                                    <td class="text-muted">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-muted text-center py-3">
                                        <i class="bi bi-inbox me-1"></i> Tidak ada item
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end text-muted">Subtotal</td>
                                    <td class="text-end">Rp {{ number_format($transaction->total_harga ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @if(($transaction->potongan_bpjs ?? 0) > 0)
                                <tr>
                                    <td colspan="2" class="text-end text-success">
                                        <i class="bi bi-shield-check me-1"></i>Potongan BPJS
                                    </td>
                                    <td class="text-end text-success">
                                        - Rp {{ number_format($transaction->potongan_bpjs, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="2" class="text-end fw-bold fs-6">Total Bayar</td>
                                    <td class="text-end fw-bold fs-6 text-primary">
                                        Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

                {{-- Kolom Kanan: Pembayaran --}}
                <div class="col-lg-5">
                    <div class="glass-card p-4 h-100" style="border-radius:16px;">
                        <p class="detail-section-title"><i class="bi bi-wallet2 me-1"></i>Status Pembayaran</p>

                        @if($transaction->status === 'lunas')
                        {{-- SUDAH LUNAS --}}
                        <div class="payment-box lunas mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-15 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:48px;height:48px;font-size:1.3rem;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-success">Pembayaran Lunas</div>
                                    <small class="text-muted">
                                        Total dibayar: <strong>Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</strong>
                                    </small>
                                </div>
                            </div>
                            <hr class="my-3 border-success opacity-25">
                            <div class="detail-row">
                                <span class="label">Metode Pembayaran</span>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm">
                                    <i class="bi bi-credit-card-2-front me-1 text-muted"></i>
                                    {{ ucfirst($transaction->metode_bayar) }}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Status Kacamata</span>
                                @php
                                    $stBadge = match($transaction->status_kacamata ?? 'proses') {
                                        'siap'    => ['bg-success', 'bi-eyeglasses', 'Siap Diambil'],
                                        'proses'  => ['bg-warning text-dark', 'bi-hourglass-split', 'Sedang Diproses'],
                                        'diambil' => ['bg-secondary','bi-check2-all','Sudah Diambil'],
                                        default   => ['bg-secondary','bi-question-circle','Tidak Diketahui'],
                                    };
                                @endphp
                                <span class="badge {{ $stBadge[0] }} rounded-pill badge-status shadow-sm">
                                    <i class="bi {{ $stBadge[1] }} me-1"></i>{{ $stBadge[2] }}
                                </span>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('pickup.index') }}">
                            <input type="hidden" name="step" value="3">
                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                            <button type="submit" class="btn btn-success w-100 rounded-pill shadow-sm btn-action py-2">
                                <i class="bi bi-bag-check-fill me-2"></i>Lanjutkan ke Pengambilan
                            </button>
                        </form>

                        @else
                        {{-- BELUM LUNAS --}}
                        @php
                            $sudahBayar  = $transaction->sudah_bayar ?? 0;
                            $sisaBayar   = $transaction->total_bayar - $sudahBayar;
                        @endphp
                        <div class="payment-box mb-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-warning bg-opacity-15 text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:48px;height:48px;font-size:1.3rem;">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-warning">Belum Lunas</div>
                                    <small class="text-muted">Selesaikan pembayaran untuk melanjutkan</small>
                                </div>
                            </div>
                            <div class="detail-row">
                                <span class="label">Total Tagihan</span>
                                <span class="value">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Sudah Dibayar</span>
                                <span class="value text-success">Rp {{ number_format($sudahBayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label fw-bold">Sisa Pembayaran</span>
                                <span class="value text-danger fw-bold fs-6">Rp {{ number_format($sisaBayar, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('pickup.pay', $transaction) }}">
                            @csrf
                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                            <label class="form-label small fw-semibold text-muted mb-1">
                                <i class="bi bi-cash-coin me-1"></i>Nominal Pembayaran
                            </label>
                            <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden">
                                <span class="input-group-text bg-white border-0 fw-semibold text-muted px-3">Rp</span>
                                <input type="number"
                                       name="nominal_bayar"
                                       class="form-control border-0 fw-bold fs-6 @error('nominal_bayar') is-invalid @enderror"
                                       placeholder="0"
                                       min="1"
                                       max="{{ $sisaBayar }}"
                                       value="{{ old('nominal_bayar', $sisaBayar) }}"
                                       required>
                            </div>
                            @error('nominal_bayar')
                                <div class="text-danger small mb-2"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</div>
                            @enderror

                            <label class="form-label small fw-semibold text-muted mb-1">
                                <i class="bi bi-credit-card me-1"></i>Metode Pembayaran
                            </label>
                            <select name="metode_bayar" class="form-select border-0 shadow-sm rounded-pill mb-4 px-3" required>
                                <option value="">Pilih Metode...</option>
                                <option value="tunai"  {{ old('metode_bayar') == 'tunai'  ? 'selected' : '' }}>Tunai</option>
                                <option value="transfer" {{ old('metode_bayar') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="qris"  {{ old('metode_bayar') == 'qris'   ? 'selected' : '' }}>QRIS</option>
                                <option value="debit" {{ old('metode_bayar') == 'debit'  ? 'selected' : '' }}>Kartu Debit</option>
                            </select>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm btn-action py-2">
                                <i class="bi bi-check2-circle me-2"></i>Bayar &amp; Lanjutkan
                            </button>
                        </form>
                        @endif

                    </div>
                </div>

            </div>
        </div>
        @endisset

    </div>
    {{-- /STEP 2 --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  STEP 3 — KONFIRMASI PENGAMBILAN                          --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="step-panel {{ $currentStep == 3 ? 'active' : '' }}">

        @isset($transaction)
        {{-- Header --}}
        <div class="card-header bg-transparent border-0 p-4 d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary d-flex align-items-center">
                <i class="bi bi-bag-check me-2 fs-4"></i>Konfirmasi Pengambilan
            </h5>
            <a href="{{ route('pickup.index', ['step' => 2, 'transaction_id' => $transaction->id]) }}"
               class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm btn-action no-print">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="px-4 pb-4">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-7">

                    {{-- Ringkasan Card --}}
                    <div class="glass-card p-4 mb-4" style="border-radius:16px;" id="printArea">

                        {{-- Icon --}}
                        <div class="confirm-icon-wrap">
                            <i class="bi bi-eyeglasses text-primary" style="font-size:2rem;"></i>
                        </div>
                        <h5 class="text-center fw-bold mb-1">Ringkasan Pengambilan</h5>
                        <p class="text-center text-muted small mb-4">Pastikan data berikut benar sebelum konfirmasi</p>

                        {{-- Info Pasien --}}
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3"
                             style="background:rgba(13,110,253,0.05);border:1px solid rgba(13,110,253,0.1);">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:48px;height:48px;font-size:1.3rem;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">
                                    {!! $transaction->patient->nama ?? '<span class="text-muted">Pasien Umum</span>' !!}
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-phone me-1"></i>{{ $transaction->patient->no_hp ?? '-' }}
                                </small>
                            </div>
                        </div>

                        <div class="detail-row">
                            <span class="label"><i class="bi bi-hash me-1"></i>No. Transaksi</span>
                            <span class="value text-primary fw-bold">{{ $transaction->no_transaksi }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-calendar3 me-1"></i>Tanggal Transaksi</span>
                            <span class="value">{{ $transaction->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-clock me-1"></i>Jam Transaksi</span>
                            <span class="value">{{ $transaction->created_at->format('H:i') }} WIB</span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-wallet2 me-1"></i>Total Pembayaran</span>
                            <span class="value text-success fw-bold">Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-credit-card me-1"></i>Metode Bayar</span>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-credit-card-2-front me-1 text-muted"></i>{{ ucfirst($transaction->metode_bayar) }}
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-check-circle me-1"></i>Status Bayar</span>
                            <span class="badge bg-success rounded-pill badge-status shadow-sm">
                                <i class="bi bi-check-lg me-1"></i>Lunas
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="label"><i class="bi bi-calendar-event me-1"></i>Tanggal Pengambilan</span>
                            <span class="value text-primary">{{ now()->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    @if(($transaction->status_kacamata ?? '') !== 'diambil')
                    <form method="POST" action="{{ route('pickup.confirm', $transaction) }}" class="no-print">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                        <button type="submit"
                                class="btn btn-success w-100 rounded-pill shadow btn-action py-2 mb-3 fw-semibold fs-6">
                            <i class="bi bi-bag-check-fill me-2"></i>Konfirmasi Pengambilan Kacamata
                        </button>
                    </form>
                    @else
                    <div class="alert alert-success rounded-4 shadow-sm d-flex align-items-center gap-3 mb-3 no-print">
                        <i class="bi bi-check-circle-fill fs-4 flex-shrink-0"></i>
                        <div>
                            <div class="fw-bold">Kacamata Sudah Diambil</div>
                            <small class="text-muted">
                                Diambil pada: {{ $transaction->tanggal_diambil
                                    ? \Carbon\Carbon::parse($transaction->tanggal_diambil)->format('d M Y, H:i') . ' WIB'
                                    : '-' }}
                            </small>
                        </div>
                    </div>
                    @endif

                    {{-- Tombol Cetak --}}
                    <button type="button"
                            onclick="window.print()"
                            class="btn btn-outline-primary w-100 rounded-pill shadow-sm btn-action py-2 no-print">
                        <i class="bi bi-printer-fill me-2"></i>Cetak Struk Pengambilan
                    </button>

                </div>
            </div>
        </div>
        @endisset

    </div>
    {{-- /STEP 3 --}}

</div>

@endsection

@push('scripts')
<script>
    // Auto-format nominal bayar dengan separator titik (display saja)
    const nominalInput = document.querySelector('input[name="nominal_bayar"]');
    if (nominalInput) {
        nominalInput.addEventListener('focus', function () {
            this.select();
        });
    }
</script>
@endpush