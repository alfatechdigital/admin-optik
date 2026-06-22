<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir BPJS - {{ $transaction->no_transaksi }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            line-height: 1.2;
        }

        /* ===== HEADER BPJS ===== */
        .bpjs-header {
            text-align: center;
            margin-bottom: 4pt;
        }

        .bpjs-header .bpjs-title {
            font-size: 18pt;
            font-weight: bolder;
            letter-spacing: 1px;
            color: #000;
            display: inline-block;
            transform: scaleX(1.3);
            transform-origin: center;
        }

        .bpjs-header .bpjs-full {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #000;
            margin-top: 2pt;
        }

        .bpjs-header .bpjs-kantor {
            font-size: 10pt;
            font-weight: bold;
            color: #000;
            margin-top: 2pt;
        }

        /* ===== NAMA OPTIK ===== */
        .optik-container {
            margin-top: 15pt;
            margin-bottom: 15pt;
        }

        .nama-optik {
            font-size: 11pt;
            font-weight: bold;
            padding: 3pt 6pt;
            display: inline-block;
        }

        /* ===== JUDUL SURAT ===== */
        .judul-surat {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 12pt;
            text-decoration: underline;
        }

        /* ===== INTRO TEXT ===== */
        .intro-text {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10pt;
            text-align: justify;
        }

        /* ===== DATA ROWS ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-bottom: 15pt;
        }

        .data-table tr td {
            padding: 4pt 2pt;
            vertical-align: bottom;
        }

        .data-table tr td:first-child {
            width: 28%;
            font-weight: bold;
        }

        .data-table tr td.colon {
            width: 3%;
            text-align: center;
        }

        /* Efek titik-titik printer */
        .data-table tr td.value {
            border-bottom: 1pt dotted #000;
            padding-left: 5pt;
        }

        .data-table tr td.math-sign {
            width: 2%;
            text-align: right;
            font-weight: bold;
            padding-left: 2pt;
        }

        /* Refraction inline styling */
        .refraction-container {
            display: table;
            width: 100%;
        }

        .rf-row {
            display: table-row;
        }

        .rf-cell {
            display: table-cell;
            padding-right: 7pt;
        }

        .rf-label {
            font-weight: bold;
            display: inline-block;
            min-width: 55pt;
        }

        .rf-dots {
            border-bottom: 1pt dotted #000;
            display: inline-block;
            min-width: 60pt;
            text-align: center;
        }

        .rf-x {
            font-weight: bold;
            margin-right: 4pt;
            margin-left: 4pt;
        }

        /* ===== DISCLAIMER ===== */
        .disclaimer {
            margin-top: 15pt;
            font-size: 11pt;
            font-weight: bold;
        }

        /* ===== TTD SECTION ===== */
        .ttd-section {
            margin-top: 30pt;
            display: flex;
            justify-content: space-between;
        }

        .ttd-block {
            width: 40%;
            font-size: 11pt;
            text-align: center;
        }

        .ttd-block .kota-tanggal {
            margin-bottom: 15pt;
            text-align: center;
        }

        .ttd-block .ttd-label {
            font-weight: bold;
            line-height: 1.3;
        }

        .ttd-block .ttd-space {
            height: 60pt;
        }

        .ttd-block .ttd-name {
            font-weight: bold;
            border-bottom: 1pt dotted #000;
            display: inline-block;
            min-width: 130pt;
            padding-bottom: 2pt;
        }

        /* Screen preview */
        @media screen {
            body {
                background: #ccc;
                padding: 10mm;
                max-width: 210mm;
                margin: 0 auto;
            }

            .page {
                background: #fff;
                padding: 15mm 15mm 15mm 20mm;
                box-shadow: 0 2px 12px rgba(0, 0, 0, .3);
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="page">

        {{-- ===== HEADER ===== --}}
        <div class="bpjs-header">
            <div class="bpjs-title font-weight">BPJS KESEHATAN</div>
            <div class="bpjs-full">BADAN PENYELENGGARA JAMINAN SOSIAL</div>
            <div class="bpjs-kantor">KANTOR CABANG BEKASI</div>
        </div>
        <br><br>
        <hr style="border: none; border-top: 2px solid black;">
        {{-- ===== NAMA OPTIK ===== --}}

        <b><span>NAMA OPTIK : PERKASA OPTIKAL</span></b>


        <hr style="border: none; border-top: 2px solid black;"><br><br>

        {{-- ===== JUDUL ===== --}}
        <div class="judul-surat">SURAT KETERANGAN</div>

        {{-- ===== INTRO ===== --}}
        {{-- ===== INTRO ===== --}}
        @php
            $namaPasien = $transaction->nama_pasien ?? ($transaction->patient->nama ?? '-');
            $noBpjs = $transaction->no_bpjs ?? ($transaction->patient->no_bpjs ?? '-');
            $alamat = $transaction->alamat_pasien ?? ($transaction->patient->alamat ?? '-');
            $tglFaktur = $transaction->tgl_faktur
                ? \Carbon\Carbon::parse($transaction->tgl_faktur)->translatedFormat('d F Y')
                : \Carbon\Carbon::now()->translatedFormat('d F Y');

            // Refraction
            $odSph = $transaction->od_sph ?: '';
            $odCyl = $transaction->od_cyl ?: '';
            $odAxis = $transaction->od_axis ?: '';
            $odAdd = $transaction->od_add ?: '';
            $odMpd = $transaction->od_mpd ?: '';
            $osSph = $transaction->os_sph ?: '';
            $osCyl = $transaction->os_cyl ?: '';
            $osAxis = $transaction->os_axis ?: '';
            $osMpd = $transaction->os_mpd ?: '';

            // Pengelompokan Items menggunakan Array
            $listLensa = [];
            $listFrame = [];
            $listProdukLain = [];

            $hargaLensa = 0;
            $hargaFrame = 0;
            $hargaProdukLain = 0;

            foreach ($transaction->items as $item) {
                $namaItem = strtolower($item->nama_produk);

                if (str_contains($namaItem, 'lensa') || str_contains($namaItem, 'lens')) {
                    $listLensa[] = $item->nama_produk;
                    $hargaLensa += $item->subtotal;
                } elseif (str_contains($namaItem, 'frame') || str_contains($namaItem, 'kacamata') || str_contains($namaItem, 'gelas')) {
                    $namaFrame = ($transaction->kode_frame ? $transaction->kode_frame . ' ' : '') . $item->nama_produk;
                    $listFrame[] = $namaFrame;
                    $hargaFrame += $item->subtotal;
                } else {
                    // Menampung produk selain lensa dan frame (misal: cairan pembersih, case, dll)
                    $listProdukLain[] = $item->nama_produk;
                    $hargaProdukLain += $item->subtotal;
                }
            }

            // Fallback jika item kosong tapi property lensa bawaan transaksi ada
            if (empty($listLensa) && $transaction->lensa) {
                $listLensa[] = $transaction->lensa;
            }

            $totalBiaya = $transaction->harga_jual ?: ($hargaLensa + $hargaFrame + $hargaProdukLain);
            $potonganBpjs = $transaction->potongan_bpjs ?: $transaction->potongan ?: 0;
            $sisaBayar = max(0, $totalBiaya - $potonganBpjs);
        @endphp

        <div class="intro-text">
            Telah menerima resep kacamata yang telah dilegalisir oleh BPJS Kesehatan Kantor Cabang Bekasi.
        </div>

        {{-- ===== DATA TABLE ===== --}}
        <table class="data-table">
            <tbody>
                <tr>
                    <td>Nama Penderita</td>
                    <td class="colon">:</td>
                    <td class="value" colspan="2">{{ $namaPasien }}</td>
                </tr>
                <tr>
                    <td>Untuk dibuat kacamata</td>
                    <td class="colon">:</td>
                    <td class="value" colspan="2" style="border-bottom: none; padding-left: 0;">
                        <div class="refraction-container">
                            <div class="rf-row">
                                <div class="rf-cell"><span class="rf-label">OD.SPH</span> <span
                                        class="rf-dots">{{ $odSph }}</span></div>
                                <div class="rf-cell"><span class="rf-label">OD.CYL</span> <span
                                        class="rf-dots">{{ $odCyl }}</span></div>
                                <div class="rf-cell"><span class="rf-x">X</span> <span
                                        class="rf-dots">{{ $odAxis }}</span></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Dengan Ukuran</td>
                    <td class="colon">:</td>
                    <td class="value" colspan="2" style="border-bottom: none; padding-left: 0;">
                        <div class="refraction-container">
                            <div class="rf-row">
                                <div class="rf-cell"><span class="rf-label">OS.SPH</span> <span
                                        class="rf-dots">{{ $osSph }}</span></div>
                                <div class="rf-cell"><span class="rf-label">OS.CYL</span> <span
                                        class="rf-dots">{{ $osCyl }}</span></div>
                                <div class="rf-cell"><span class="rf-x">X</span> <span
                                        class="rf-dots">{{ $osAxis }}</span></div>
                            </div>
                        </div>
                        <div class="refraction-container" style="margin-top: 5pt;">
                            <div class="rf-row">
                                <div class="rf-cell" style="width: 100%;"><span class="rf-label">ADD +</span> <span
                                        class="rf-dots" style="min-width: 120pt;">{{ $odAdd }}</span></div>
                                <div class="rf-cell" style="white-space: nowrap;"><span class="rf-label"
                                        style="min-width: 30pt;">DV</span> <span
                                        class="rf-dots">{{ $odMpd ?: $osMpd }}</span></div>
                            </div>
                        </div>
                    </td>
                </tr>

                {{-- ===== LOOPING LENSA ===== --}}
                @if(count($listLensa) > 0)
                    @foreach($listLensa as $index => $namaLensa)
                        <tr>
                            <td>{{ $index === 0 ? 'Lensa' : '' }}</td>
                            <td class="colon">{{ $index === 0 ? ':' : '' }}</td>
                            <td class="value" colspan="2">{{ $namaLensa }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>Lensa</td>
                        <td class="colon">:</td>
                        <td class="value" colspan="2">-</td>
                    </tr>
                @endif

                {{-- ===== LOOPING FRAME ===== --}}
                @if(count($listFrame) > 0)
                    @foreach($listFrame as $index => $namaFrame)
                        <tr>
                            <td>{{ $index === 0 ? 'Frame' : '' }}</td>
                            <td class="colon">{{ $index === 0 ? ':' : '' }}</td>
                            <td class="value" colspan="2">{{ $namaFrame }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>Frame</td>
                        <td class="colon">:</td>
                        <td class="value" colspan="2">
                            {{ $transaction->kode_frame ? $transaction->kode_frame : '-' }}
                        </td>
                    </tr>
                @endif

                {{-- ===== LOOPING PRODUK LAINNYA ===== --}}
                @foreach($listProdukLain as $index => $namaProdukLain)
                    <tr>
                        <td>{{ $index === 0 ? 'Produk Lainnya' : '' }}</td>
                        <td class="colon">{{ $index === 0 ? ':' : '' }}</td>
                        <td class="value" colspan="2">{{ $namaProdukLain }}</td>
                    </tr>
                @endforeach

                {{-- ===== RINCIAN HARGA ===== --}}
                <tr>
                    <td>Harga Lensa</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($hargaLensa, 0, ',', '.') }}</td>
                    <td class="math-sign"></td>
                </tr>
                <tr>
                    <td>Harga Frame</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($hargaFrame, 0, ',', '.') }}</td>
                    <td class="math-sign">+</td>
                </tr>
                @if($hargaProdukLain > 0)
                    <tr>
                        <td>Harga Produk Lain</td>
                        <td class="colon">:</td>
                        <td class="value">Rp. {{ number_format($hargaProdukLain, 0, ',', '.') }}</td>
                        <td class="math-sign">+</td>
                    </tr>
                @endif
                <tr>
                    <td>Total Biaya</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    <td class="math-sign"></td>
                </tr>
                <tr>
                    <td>Yang diganti BPJS Kesehatan</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($potonganBpjs, 0, ',', '.') }}</td>
                    <td class="math-sign">-</td>
                </tr>
                <tr>
                    <td>Selisih yang dibayar</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($sisaBayar, 0, ',', '.') }}</td>
                    <td class="math-sign"></td>
                </tr>
            </tbody>
        </table>
        {{-- ===== BIODATA ===== --}}
        <table class="data-table" style="margin-top: 10pt;">
            <tbody>
                <tr>
                    <td>Nama Pemegang Kartu</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $namaPasien }}</td>
                </tr>
                <tr>
                    <td>Nomor Kartu BPJS Kesehatan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $noBpjs }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $alamat }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ===== DISCLAIMER ===== --}}
        <div class="disclaimer">
            Telah menerima kacamata sesuai dengan ukurannya.
        </div>

        {{-- ===== TTD ===== --}}
        <div class="ttd-section">
            <div class="ttd-block">
                <div class="kota-tanggal">&nbsp;</div>
                <div class="ttd-label">Yang membuat Kacamata</div>
                <div class="ttd-label">Perkasa Optikal</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $transaction->nama_dokter ?: 'Admin' }}</div>
            </div>

            <div class="ttd-block">
                <div class="kota-tanggal">Bekasi, {{ $tglFaktur }}</div>
                <div class="ttd-label">Tanda Tangan</div>
                <div class="ttd-label">Pemegang Kartu</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $namaPasien }}</div>
            </div>
        </div>
    </div>
</body>

</html>