@php
    $copies = $copies ?? 1;
    $mr = $transaction->medicalRecord;
    $presc = $mr->prescription ?? null;
    $og = $mr->oldGlasses ?? null;
    $isUmum = ($transaction->typefaktur == 1);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bon Pesanan - {{ $transaction->no_transaksi }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            color: #000000;
            background: #ffffff;
            width: 80mm;
            padding: 4mm 2mm 8mm;
            /* Diubah dari 4mm ke 2mm agar kanan-kiri tidak terlalu mepet margin printer */
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 4mm;
            padding-bottom: 2mm;
        }

        .header .brand {
            line-height: 1;
            margin-bottom: 1mm;
        }

        .header .brand .optik {
            font-size: 11pt;
            font-weight: bold;
            display: block;
            margin-bottom: -1mm;
        }

        .header .brand .perkasa {
            font-size: 22pt;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .header .tagline {
            font-style: italic;
            font-size: 8.5pt;
            margin-top: 1mm;
        }

        .header .address {
            font-size: 8pt;
            margin-top: 2mm;
            line-height: 1.3;
        }

        /* ===== INFO SECTION ===== */
        .info-section {
            margin-bottom: 3mm;
            border-top: 2px solid #000000;
            padding-top: 3mm;
        }

        .info-row {
            display: flex;
            margin-bottom: 1mm;
            line-height: 1.4;
        }

        .info-label {
            width: 24mm;
            /* Disesuaikan dari 26mm agar porsi value kanan memiliki ruang lebih luas */
            flex-shrink: 0;
        }

        .info-sep {
            width: 3mm;
            text-align: center;
            flex-shrink: 0;
        }

        .info-val {
            flex: 1;
            word-break: break-word;
        }

        .pelanggan-name {
            font-weight: 900;
        }

        /* ===== PRODUCT SECTION ===== */
        .product-section {
            margin-bottom: 3mm;
            border-top: 2px solid #000000;
            padding-top: 2mm;
        }

        /* ===== FINANCIAL TABLE ===== */
        .price-section {
            margin-bottom: 4mm;
            border-top: 2px solid #000000;
            padding-top: 2mm;
        }

        .price-table {
            width: 100%;
            border-collapse: collapse;
        }

        .price-table td {
            padding: 1mm 0;
        }

        .price-table .lbl {
            text-align: left;
        }

        .price-table .amt {
            text-align: right;
            width: 25mm;
        }

        .price-table .total-row td {
            border-top: 2px solid #000000;
            padding-top: 2mm;
            font-weight: 900;
            font-size: 11pt;
        }

        /* ===== DATA TABLES (PRESC & OG) ===== */
        .data-tables-section {
            margin-bottom: 4mm;
        }

        .table-title {
            font-weight: 900;
            text-decoration: underline;
            margin-bottom: 1.5mm;
            font-size: 9pt;
            text-align: center;
        }

        .ref-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 3mm;
            border: 2px solid #000000;
        }

        .ref-table th,
        .ref-table td {
            border: 1px solid #000000;
            padding: 1.5mm 1mm;
            text-align: center;
            font-weight: bold;
        }

        .ref-table th {
            background: none;
            font-weight: 900;
        }

        .ref-table td:first-child {
            font-weight: 900;
            width: 8mm;
        }

        /* ===== FOOTER ===== */
        .thanks {
            text-align: center;
            font-size: 16pt;
            font-weight: 900;
            margin: 4mm 0 3mm;
            letter-spacing: 2px;
        }

        .notes {
            font-size: 7.5pt;
            line-height: 1.4;
            border: 2px solid #000000;
            padding: 2mm;
            font-weight: normal;
        }

        .notes b {
            font-weight: 900;
        }

        .notes ol {
            margin-top: 1mm;
            padding-left: 4mm;
        }

        @media screen {
            body {
                margin: 10mm auto;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        /* ===== HIGH CONTRAST THERMAL PRINTER OVERRIDES ===== */
        body,
        body *,
        p,
        span,
        div,
        td,
        th,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        b,
        i,
        u {
            color: #000 !important;
            background-color: transparent !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-weight: 900 !important;
        }

        .header,
        .info-section,
        .product-section,
        .price-section,
        .price-table .total-row td,
        .ref-table,
        .ref-table th,
        .ref-table td,
        .notes,
        .terbilang-box {
            border-width: 2px !important;
            border-color: #000 !important;
        }

        thead {
            border-bottom: 2px dashed #000 !important;
        }

        .data-table tr td.value,
        .rf-dots,
        .ttd-block .ttd-name {
            border-bottom: 2px solid #000 !important;
        }

        .watermark {
            display: none !important;
        }
    </style>
</head>

<body onload="window.print()">
    @for($i = 0; $i < $copies; $i++)
        <div class="print-container">
            {{-- ===== HEADER ===== --}}
            <div class="header">
                <div class="brand">
                    <span class="optik">OPTIK</span>
                    <span class="perkasa">PERKASA</span>
                </div>
                <div class="tagline">Solusi Penglihatan Anda</div>
                <div class="address">
                    Jl. KH. Agus Salim No. 42 Bekasi<br>
                    No WA : 0821-80000-590
                </div>
            </div>

            {{-- ===== INFO PESANAN ===== --}}
            <div class="info-section">
                <div class="info-row">
                    <span class="info-label">No.Pesanan</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $transaction->no_transaksi }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">
                        {{ $transaction->created_at ? $transaction->created_at->format('d/m/y') : '-' }}
                        &nbsp;&nbsp;&nbsp;
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Selesai</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">
                        @php
                            $tanggal = '-';
                            if ($transaction->tgl_selesai_faset) {
                                $tanggal = \Carbon\Carbon::parse($transaction->tgl_selesai_faset)->format('d/m/y');
                            } elseif ($transaction->tgl_selesai_janji) {
                                $tanggal = \Carbon\Carbon::parse($transaction->tgl_selesai_janji)->subDay()->format('d/m/y');
                            }
                        @endphp

                        {{ $tanggal }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pelanggan</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">
                        <span class="pelanggan-name">
                            {{ $transaction->patient->nama ?? ($transaction->nama_pasien ?? 'UMUM') }}
                        </span><br>
                        {{ $transaction->patient->no_hp ?? ($transaction->telp_pasien ?? '-') }}
                    </span>
                </div>
                @if(!$isUmum)
                    <div class="info-row">
                        <span class="info-label">No Bpjs</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $transaction->no_bpjs ?? ($transaction->patient->no_bpjs ?? '-') }}</span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Resep</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $transaction->asal_resep ?: ($transaction->nama_dokter ?: '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Keterangan</span>
                    <span class="info-sep">:</span>
                    <span class="info-val">{{ $transaction->catatan ?: '-' }}</span>
                </div>
            </div>

            {{-- ===== PRODUK ===== --}}
            <div class="product-section">
                @if($transaction->items && count($transaction->items) > 0)
                    <div class="info-row" style="display: block;">
                        <span class="info-label" style="display: block; margin-bottom: 1mm;">Item :</span>
                        <table style="width:100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 2mm;">
                            <thead style="border-bottom: 1px dashed #000;">
                                <tr>
                                    <th style="text-align:left; padding: 1mm 0;">Nama Produk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->items as $item)
                                    <tr>
                                        <td style="text-align:left; padding: 1mm 0; vertical-align: top;">
                                            @if($item->product && $item->product->kode_produk)
                                                [{{ $item->product->kode_produk }}]
                                            @endif
                                            {{ $item->nama_produk }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="info-row">
                        <span class="info-label">Lensa</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $transaction->lensa ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Frame</span>
                        <span class="info-sep">:</span>
                        <span class="info-val">{{ $transaction->nama_produk ?? ($transaction->kode_frame ?? '-') }}</span>
                    </div>
                @endif
            </div>

            {{-- ===== HARGA ===== --}}
            @php
                $harga = $transaction->harga_jual ?? $transaction->total_harga ?? 0;
                $potongan = $transaction->potongan_bpjs ?? 0;
                $diskon = $transaction->potongan ?? $transaction->diskon_nominal ?? 0;
                $dp = $transaction->dp ?? $transaction->bayar ?? 0;
                $sisa = $transaction->sisa ?? max(0, $harga - $potongan - $diskon - $dp);
            @endphp


            {{-- ===== TABLES (RESEP & UKURAN) ===== --}}
            <div class="data-tables-section">
                <div class="table-title">UKURAN KACAMATA</div>
                <table class="ref-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Sph</th>
                            <th>Cyl</th>
                            <th>Axis</th>
                            <th>Add</th>
                            <th>Mpd</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>OD</td>
                            <td>{{ $og->od_sph ?? $transaction->od_sph ?? '-' }}</td>
                            <td>{{ $og->od_cyl ?? $transaction->od_cyl ?? '-' }}</td>
                            <td>{{ $og->od_axis ?? $transaction->od_axis ?? '-' }}</td>
                            <td>{{ $og->od_add ?? $transaction->od_add ?? '-' }}</td>
                            <td>{{ $og->od_mpd ?? $transaction->od_mpd ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>OS</td>
                            <td>{{ $og->os_sph ?? $transaction->os_sph ?? '-' }}</td>
                            <td>{{ $og->os_cyl ?? $transaction->os_cyl ?? '-' }}</td>
                            <td>{{ $og->os_axis ?? $transaction->os_axis ?? '-' }}</td>
                            <td>{{ $og->os_add ?? $transaction->os_add ?? '-' }}</td>
                            <td>{{ $og->os_mpd ?? $transaction->os_mpd ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        @if($i < $copies - 1)
            <div style="page-break-after: always; height:1px;"></div>
        @endif
    @endfor
</body>

</html>