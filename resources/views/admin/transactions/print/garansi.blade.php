<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Garansi - {{ $transaction->no_transaksi }}</title>
    <style>
        @page {
            size: 86mm 54mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1a1a1a;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ===== KARTU ===== */
        .card-page {
            width: 86mm;
            height: 54mm;
            padding: 5mm 6mm;
            page-break-after: always;
            position: relative;
            overflow: hidden;
            background: #e6e6e6ff;
        }

        .card-page:last-child {
            page-break-after: auto;
        }

        /* ===== SISI DEPAN (Branding) ===== */
        .front .brand-name {
            position: absolute;
            top: 5mm;
            right: 6mm;
            text-align: right;
        }

        .front .brand-name h1 {
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: 2px;
            color: #1a1a1a;
            margin: 0;
            line-height: 1;
        }

        .front .brand-name .tagline {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-style: italic;
            font-size: 7pt;
            color: #555;
            margin-top: 1mm;
        }

        .front .logo-area {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-60%, -45%);
            font-size: 60pt;
            font-weight: 900;
            color: rgba(30, 30, 30, 0.12);
            font-family: 'Georgia', serif;
            line-height: 1;
        }

        .front .contact-bar {
            position: absolute;
            bottom: 4mm;
            left: 6mm;
            right: 6mm;
            display: flex;
            gap: 4mm;
            font-size: 6.5pt;
            color: #333;
        }

        .front .contact-bar span {
            display: flex;
            align-items: center;
            gap: 1mm;
        }

        /* ===== SISI BELAKANG (Data) ===== */
        .back {
            display: flex;
            flex-direction: column;
        }

        /* Header: Nama + Kode */
        .back .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3mm;
        }

        .back .patient-name {
            font-size: 14pt;
            font-weight: 900;
            color: #1a1a1a;
            line-height: 1.1;
            max-width: 55mm;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .back .right-info {
            text-align: right;
        }

        .back .logo-sm {
            width: 10mm;
            height: 10mm;
            border: 1.5pt solid #333;
            border-radius: 2pt;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Georgia', serif;
            font-size: 14pt;
            font-weight: 900;
            color: #333;
            margin-left: auto;
            margin-bottom: 1mm;
        }

        .back .trx-code {
            font-size: 7pt;
            color: #333;
            font-weight: 700;
        }

        /* Garis pemisah */
        .back .separator {
            border: none;
            border-top: 0.5pt solid #999;
            margin-bottom: 2mm;
        }

        /* Tabel refraksi */
        .back .ref-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2.5mm;
            font-size: 8pt;
        }

        .back .ref-table th {
            font-weight: 700;
            text-align: center;
            padding: 1pt 2pt;
            color: #1a1a1a;
            border-bottom: 0.5pt solid #aaa;
        }

        .back .ref-table th:first-child {
            text-align: left;
            width: 4mm;
        }

        .back .ref-table td {
            text-align: center;
            padding: 2pt 2pt;
            font-weight: 500;
            color: #1a1a1a;
        }

        .back .ref-table td:first-child {
            text-align: left;
        }

        /* Produk */
        .back .products {
            font-size: 7.5pt;
            margin-bottom: 2.5mm;
            font-weight: 600;
            line-height: 1.5;
        }

        .back .products .row {
            display: flex;
        }

        .back .products .lbl {
            width: 12mm;
        }

        /* Footer garansi */
        .back .terms {
            font-size: 5.5pt;
            text-align: center;
            color: #333;
            line-height: 1.35;
            margin-top: auto;
        }

        .back .terms b {
            font-weight: 700;
        }

        @media screen {
            body {
                background: #ccc;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4mm;
                padding: 10mm;
            }

            .card-page {
                box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
            }
        }
    </style>
</head>

<body onload="window.print()">

    {{-- ===== SISI DEPAN ===== --}}
    <div class="card-page front">
        <div class="brand-name">
            <h1>OPTIK PERKASA</h1>
            <div class="tagline" style="font-family: 'Georgia', 'handwriting', serif;">Solusi Penglihatan Anda</div>
        </div>

        <div class="logo-area"><img src="/icon.png" alt="Optik Perkasa" width="80" height="80"></div>

        <div class="contact-bar">
            <span><i class="fa fa-phone"></i> 0218800590</span>
            <span><i class="fa fa-instagram"></i> optikperkasa</span>
            <span><i class="fa fa-globe"></i> www.optikperkasa.com</span>
        </div>
    </div>

    {{-- ===== SISI BELAKANG ===== --}}
    <div class="card-page back">
        <div class="header">
            <div class="patient-name">{{ $transaction->patient->nama ?? ($transaction->nama_pasien ?? 'UMUM') }}</div>
            <div class="right-info">
                <div class="logo-sm"><img src="/icon.png" alt="Optik Perkasa" width="40" height="40"></div>
                <div class="trx-code">{{ $transaction->id }}/{{ $transaction->created_at->format('dmY') }}</div>
            </div>
        </div>

        <hr class="separator">

        <table class="ref-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Sph</th>
                    <th>Cyl</th>
                    <th>Axis</th>
                    <th>Add</th>
                    <th>Mpd</th>
                    <th>Prism</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td>{{ $transaction->od_sph ?: '' }}</td>
                    <td>{{ $transaction->od_cyl ?: '' }}</td>
                    <td>{{ $transaction->od_axis ?: '' }}</td>
                    <td>{{ $transaction->od_add ?: '' }}</td>
                    <td>{{ $transaction->od_mpd ?: '' }}</td>
                    <td>{{ $transaction->od_prism ?: '' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>{{ $transaction->os_sph ?: '' }}</td>
                    <td>{{ $transaction->os_cyl ?: '' }}</td>
                    <td>{{ $transaction->os_axis ?: '' }}</td>
                    <td>{{ $transaction->os_add ?: '' }}</td>
                    <td>{{ $transaction->os_mpd ?: '' }}</td>
                    <td>{{ $transaction->os_prism ?: '' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="products">
            @php
                $items = $transaction->items;
            @endphp
            @foreach($items as $item)
                <div class="row">
                    <span class="lbl">
                        @if($item->product && $item->product->category)
                            {{ $item->product->category->name }}
                        @else
                            {{ stripos($item->nama_produk, 'Lensa') !== false ? 'Lensa' : (stripos($item->nama_produk, 'Frame') !== false ? 'Frame' : 'Item') }}
                        @endif
                    </span>
                    <span>: {{ $item->nama_produk }}</span>
                </div>
            @endforeach
        </div>

        <div class="terms">
            Garansi berlaku selama <b>1 (satu) Tahun</b> sejak tanggal pembelian.<br>
            Garansi ini hanya berlaku untuk kerusakan lapisan coating (pelapisan lensa),<br>
            bukan akibat goresan, benturan benda tumpul, paparan bahan kimia, maupun suhu panas.<br>
            Klaim garansi hanya dapat diproses dengan menunjukkan kartu garansi dan bukti pembelian.
        </div>
    </div>
</body>

</html>