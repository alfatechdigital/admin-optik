{{-- resources/views/admin/transactions/show.blade.php --}}
@extends('layouts.admin')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

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
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0d6efd;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            border-bottom: 2px dashed rgba(13, 110, 253, 0.2);
            padding-bottom: 0.75rem;
        }

        .section-title i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.2rem;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 1rem;
        }

        .finance-card {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            color: white;
        }

        .finance-card .info-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .finance-card .info-value,
        .finance-card .section-title {
            color: white;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .refraction-table th {
            background: #f8f9fa;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .status-badge {
            padding: 0.4em 1em;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
    @php
        // Mengabaikan markup JSON, data diambil murni dari kolom utama database
        $d_items = $transaction->items;
        $d_harga = $transaction->total_harga ?? 0;
        $d_potongan = $transaction->potongan ?? 0;
        $d_potongan_bpjs = $transaction->potongan_bpjs ?? 0;
        $d_total_bayar = $transaction->total_bayar ?? max(0, $d_harga - $d_potongan - $d_potongan_bpjs);
        $d_dp = $transaction->dp ?? 0;
        $d_sisa = $transaction->sisa ?? max(0, $d_total_bayar - $d_dp);
    @endphp
    <div class="row g-4">
        <!-- Header Invoice -->
        <div class="col-12">
            <div class="card glass-card border-0 mb-0">
                <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-receipt fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-primary">{{ $transaction->no_transaksi }}</h4>
                            <div class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i> {{ $transaction->created_at->format('d F Y, H:i') }}
                                WIB
                                <span class="mx-2">|</span>
                                <i class="bi bi-person-badge me-1"></i> Kasir: {{ $transaction->kasir->name ?? 'Sistem' }}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center mt-3 mt-md-0">
                        @php
                            $badgeColor = match ($transaction->status) {
                                'lunas' => 'bg-success',
                                'pending' => 'bg-warning text-dark',
                                'batal' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <!-- <span class="badge {{ $badgeColor }} rounded-pill status-badge fs-6 me-3">{{ strtoupper($transaction->status) }}</span>
                                 -->
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <!-- <a href="{{ route('transactions.create', $transaction->id) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-pencil-square me-1"></i> Edit di POS
                                </a> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Sisi Kiri (Produk & Teknis) -->
        <div class="col-lg-7">

            <!-- Detail Produk & Frame -->
            <div class="card glass-card">
                <div class="card-body p-4">
                    <h6 class="section-title"><i class="bi bi-box-seam"></i> Produk & Frame</h6>
                    <div class="table-responsive bg-white rounded-3 border">
                        @php
                            $isUmum = ($transaction->typefaktur == 1);
                        @endphp

                        @if($d_items && count($d_items) > 0)
                            {{-- Tampilan Itemized untuk UMUM --}}
                            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center" width="80">Qty</th>
                                        <th class="text-end" width="150">Harga Satuan</th>
                                        <th class="text-end" width="150">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($d_items as $idx => $item)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                @if(isset($item->product))
                                                    <span
                                                        class="badge bg-dark bg-opacity-10 text-dark border">{{ $item->product->kode_produk }}</span>
                                                @elseif(isset($item->product_id) && $item->product_id)
                                                    <span
                                                        class="badge bg-dark bg-opacity-10 text-dark border">#{{ $item->product_id }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $item->nama_produk ?? $item->nama }}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-end">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            {{-- Tampilan Lensa & Frame untuk Pasien/BPJS --}}
                            <table class="table table-bordered mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Lensa</th>
                                        <th width="20%">Kode Frame</th>
                                        <th width="20%">Merk / Seri</th>
                                        <th width="25%">Warna</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">
                                            @if($transaction->lensa)
                                                {{ $transaction->lensa }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->kode_frame)
                                                <span
                                                    class="badge bg-dark bg-opacity-10 text-dark border">{{ $transaction->kode_frame }}</span>
                                                @if($transaction->nama_produk)
                                                    <div class="small text-muted mt-1">{{ $transaction->nama_produk }}</div>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $transaction->seri ?: '-' }}</td>
                                        <td>{{ $transaction->warna ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="p-3">
                                            <div class="info-label mb-1">Keterangan Tambahan</div>
                                            <div class="fw-medium">{{ $transaction->keterangan_frame ?: '-' }}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>



            <!-- 3 Tabel Rekam Medis Terkait -->
            @if($transaction->medicalRecord)
                <div class="card glass-card">
                    <div class="card-body p-4">
                        <h6 class="section-title">
                            <i class="bi bi-clipboard2-pulse"></i> Rekam Medis Terkait
                            <a href="{{ route('medical-records.show', $transaction->medicalRecord) }}"
                                class="btn btn-sm btn-outline-primary ms-auto rounded-pill px-3" style="font-size: 0.75rem;">
                                <i class="bi bi-eye me-1"></i>Detail Rekam Medis
                            </a>
                        </h6>
                        <div class="text-muted small mb-3">
                            <i class="bi bi-calendar3 me-1"></i>Kunjungan:
                            {{ $transaction->medicalRecord->visit_date->format('d F Y') }}
                            @if($transaction->medicalRecord->complaint)
                                <span class="mx-1">|</span>
                                <i class="bi bi-chat-left-text me-1"></i>{{ $transaction->medicalRecord->complaint }}
                            @endif
                        </div>

                        <!-- Ukuran Kacamata Lama -->
                        @if($transaction->medicalRecord->oldGlasses)
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2"
                                        style="font-size: 0.78rem;">
                                        <i class="bi bi-eyeglasses me-1"></i>Ukuran Kacamata Lama
                                    </span>
                                </div>
                                <div class="table-responsive bg-white rounded-3 border">
                                    <table class="table refraction-table text-center align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="80">Mata</th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>PRISM</th>
                                                <th>ADD</th>
                                                <th>MPD</th>
                                                <th>CC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-primary">OD</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_sph ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_cyl ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_axis ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_prism ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_add ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_mpd ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->od_cc ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-danger">OS</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_sph ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_cyl ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_axis ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_prism ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_add ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_pd ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->oldGlasses->os_cc ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Ukuran Resep Dokter -->
                        @if($transaction->medicalRecord->prescription)
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2"
                                        style="font-size: 0.78rem;">
                                        <i class="bi bi-file-medical me-1"></i>Ukuran Resep Dokter
                                    </span>
                                    @if($transaction->medicalRecord->prescription->doctor_name)
                                        <span class="text-muted small">—
                                            {{ $transaction->medicalRecord->prescription->doctor_name }}</span>
                                    @endif
                                </div>
                                <div class="table-responsive bg-white rounded-3 border">
                                    <table class="table refraction-table text-center align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="80">Mata</th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>PRISM</th>
                                                <th>ADD</th>
                                                <th>MPD</th>
                                                <th>CC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-primary">OD</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_sph ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_cyl ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_axis ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_prism ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_add ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_mpd ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->od_cc ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-danger">OS</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_sph ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_cyl ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_axis ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_prism ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_add ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_pd ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->prescription->os_cc ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if($transaction->medicalRecord->prescription->diagnosis || $transaction->medicalRecord->prescription->notes)
                                    <div class="row mt-2 px-1">
                                        @if($transaction->medicalRecord->prescription->diagnosis)
                                            <div class="col-md-6">
                                                <div class="text-muted small fw-semibold">Diagnosis</div>
                                                <div class="small">{{ $transaction->medicalRecord->prescription->diagnosis }}</div>
                                            </div>
                                        @endif
                                        @if($transaction->medicalRecord->prescription->notes)
                                            <div class="col-md-6">
                                                <div class="text-muted small fw-semibold">Catatan</div>
                                                <div class="small">{{ $transaction->medicalRecord->prescription->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Hasil Refraksi -->
                        @if($transaction->medicalRecord->refraction)
                            <div class="mb-0">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span
                                        class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2"
                                        style="font-size: 0.78rem;">
                                        <i class="bi bi-eye me-1"></i>Hasil Refraksi
                                    </span>
                                    @if($transaction->medicalRecord->refraction->doctor_name)
                                        <span class="text-muted small">—
                                            {{ $transaction->medicalRecord->refraction->doctor_name }}</span>
                                    @endif
                                </div>
                                <div class="table-responsive bg-white rounded-3 border">
                                    <table class="table refraction-table text-center align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="80">Mata</th>
                                                <th>SC</th>
                                                <th>SPH</th>
                                                <th>CYL</th>
                                                <th>AXIS</th>
                                                <th>PRISM</th>
                                                <th>ADD</th>
                                                <th>MPD</th>
                                                <th>CC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="fw-bold text-primary">OD</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_sc ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_sph ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_cyl ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_axis ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_prism ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_add ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_pd ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->od_cc ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-danger">OS</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_sc ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_sph ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_cyl ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_axis ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_prism ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_add ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_pd ?? '-' }}</td>
                                                <td>{{ $transaction->medicalRecord->refraction->os_cc ?? '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if($transaction->medicalRecord->refraction->diagnosis || $transaction->medicalRecord->refraction->notes)
                                    <div class="row mt-2 px-1">
                                        @if($transaction->medicalRecord->refraction->diagnosis)
                                            <div class="col-md-6">
                                                <div class="text-muted small fw-semibold">Diagnosis</div>
                                                <div class="small">{{ $transaction->medicalRecord->refraction->diagnosis }}</div>
                                            </div>
                                        @endif
                                        @if($transaction->medicalRecord->refraction->notes)
                                            <div class="col-md-6">
                                                <div class="text-muted small fw-semibold">Catatan</div>
                                                <div class="small">{{ $transaction->medicalRecord->refraction->notes }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!$transaction->medicalRecord->oldGlasses && !$transaction->medicalRecord->prescription && !$transaction->medicalRecord->refraction)
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-info-circle me-1"></i>Belum ada data pemeriksaan di rekam medis ini.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Jadwal & Pengerjaan Lab -->
            <div class="card glass-card">
                <div class="card-body p-4">
                    <h6 class="section-title"><i class="bi bi-tools"></i> Jadwal Pengerjaan & Laboratorium</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-label">Tanggal Order</div>
                            <div class="info-value">
                                {{ $transaction->tgl_order ? \Carbon\Carbon::parse($transaction->tgl_order)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tanggal Faset</div>
                            <div class="info-value">
                                {{ $transaction->tgl_faset ? \Carbon\Carbon::parse($transaction->tgl_faset)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Selesai Faset</div>
                            <div class="info-value text-success">
                                {{ $transaction->tgl_selesai_faset ? \Carbon\Carbon::parse($transaction->tgl_selesai_faset)->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-label">Lab Lensa</div>
                            <div class="info-value">{{ $transaction->lab ?: '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tempat Faset</div>
                            <div class="info-value">{{ $transaction->tempat_faset ?: '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tgl Lensa Datang</div>
                            <div class="info-value">
                                {{ $transaction->tgl_datang_faset ? \Carbon\Carbon::parse($transaction->tgl_datang_faset)->format('d/m/Y') : '-' }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-label">No Legalisasi</div>
                            <div class="info-value">{{ $transaction->no_legalisasi ?: '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tgl Legalisasi</div>
                            <div class="info-value">
                                {{ $transaction->tgl_legalisasi ? \Carbon\Carbon::parse($transaction->tgl_legalisasi)->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sisi Kanan (Pasien & Info Finansial) -->
        <div class="col-lg-5">

            <!-- Data Pasien -->
            <div class="card glass-card">
                <div class="card-body p-4">
                    <h6 class="section-title"><i class="bi bi-person-badge"></i> Data Pasien</h6>

                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3 border"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-person fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">
                                {!! $transaction->patient->nama ?? ($transaction->nama_pasien ?? '<span class="text-muted">Umum / Tidak Ada Nama</span>') !!}
                            </h5>
                            <div class="text-muted small mt-1">
                                @if($transaction->patient && $transaction->patient->no_bpjs)
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-1">BPJS:
                                        {{ $transaction->patient->no_bpjs }}</span>
                                @elseif($transaction->no_bpjs)
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-1">BPJS:
                                        {{ $transaction->no_bpjs }}</span>
                                @endif
                                <i class="bi bi-telephone-fill ms-1"></i>
                                {{ $transaction->patient->no_hp ?? ($transaction->telp_pasien ?? '-') }}
                            </div>
                        </div>
                    </div>

                    <div class="info-label">Alamat Lengkap</div>
                    <div class="info-value">{{ $transaction->patient->alamat ?? ($transaction->alamat_pasien ?? '-') }}
                    </div>

                    <div class="info-label">Asal Resep / Rekomendasi / Dokter</div>
                    <div class="info-value mb-0">
                        @php
                            $displayResep = $transaction->asal_resep ?: ($transaction->nama_dokter ?: '-');
                        @endphp
                        @if($displayResep !== '-')
                            <span class="badge bg-light text-dark border"><i class="bi bi-journal-medical me-1"></i>
                                {{ $displayResep }}</span>
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rincian Pembayaran -->
            <div class="card glass-card finance-card border-0 shadow-lg">
                <div class="card-body p-4">
                    <h6 class="section-title"><i class="bi bi-cash-stack"></i> Rincian Pembayaran</h6>

                    <div
                        class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white bg-opacity-10 rounded-3 border border-white border-opacity-25">
                        <div class="info-label mb-0 text-white">Tipe Transaksi</div>
                        <div class="fs-6 fw-bold text-white text-uppercase tracking-wide">
                            @if($transaction->typefaktur == 2)
                                <i class="bi bi-shield-plus me-1"></i> KLAIM BPJS
                            @else
                                <i class="bi bi-wallet2 me-1"></i> TUNAI / UMUM
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <div class="info-label pt-1 text-white opacity-75">Harga Jual / Total</div>
                        <div class="info-value fs-5 mb-0">Rp {{ number_format($d_harga, 0, ',', '.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <div class="info-label pt-1 text-white opacity-75">Potongan / Diskon (-)</div>
                        <div class="info-value mb-0 text-white opacity-75">Rp
                            {{ number_format($d_potongan_bpjs, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-white border-opacity-25">
                        <div class="info-label pt-1 text-white opacity-75">Deposit / Dibayar</div>
                        <div class="info-value mb-0">Rp {{ number_format($d_dp, 0, ',', '.') }}</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mt-2">
                        <div class="info-label mb-0 text-white opacity-75">Sisa Tagihan</div>
                        <div class="fs-2 fw-bold text-white lh-1">
                            <small class="fs-5 fw-normal opacity-75">Rp</small> {{ number_format($d_sisa, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($d_sisa <= 0)
                        <div class="mt-3 text-end">
                            <span class="badge bg-white text-success rounded-pill px-3 py-2 shadow-sm"><i
                                    class="bi bi-check-circle-fill me-1"></i> LUNAS</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Rencana Ambil -->
            <div class="card glass-card">
                <div class="card-body p-4">
                    <h6 class="section-title"><i class="bi bi-box-arrow-up-right"></i> Rencana & Status Pengambilan</h6>

                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="info-label">Tanggal Rencana Ambil</div>
                            <div class="info-value mb-0 fs-5 text-primary">
                                @if($transaction->tgl_selesai_janji)
                                    <i
                                        class="bi bi-calendar-check me-2"></i>{{ \Carbon\Carbon::parse($transaction->tgl_selesai_janji)->format('d F Y') }}
                                @else
                                    <span class="text-muted"><i class="bi bi-calendar-x me-2"></i> Belum dijadwalkan</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-end">
                            <div class="info-label">Status</div>
                            @if($transaction->diambil == 1 || strtolower($transaction->diambil) == 'ya' || $transaction->diambil == '1')
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2"><i
                                        class="bi bi-check2-all me-1"></i> SUDAH DIAMBIL</span>
                            @elseif($d_sisa > 0)
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2"><i
                                        class="bi bi-x-circle me-1"></i> BELUM LUNAS (SISA Rp
                                    {{ number_format($d_sisa, 0, ',', '.') }})</span>
                            @else
                                <span
                                    class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-3 py-2 mb-2 d-block"><i
                                        class="bi bi-check-circle me-1"></i> LUNAS (BISA DIAMBIL)</span>
                                <form action="{{ route('pickup.confirm', $transaction) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"
                                        onclick="return confirm('Proses serah terima barang? Status akan diubah menjadi Sudah Diambil.')">
                                        <i class="bi bi-box-seam me-1"></i> Ambil Kacamata Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection