@php
    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai / 10) . " Puluh" . penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai / 100) . " Ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai / 1000) . " Ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai / 1000000) . " Juta" . penyebut($nilai % 1000000);
        }
        return $temp;
    }

    function terbilang($nilai)
    {
        if ($nilai == 0)
            return "Nol Rupiah";
        $hasil = trim(penyebut($nilai));
        return $hasil . " Rupiah";
    }

    $showDetail = request()->query('show_detail', '1') === '1';

    $harga = $transaction->harga_jual ?? $transaction->total_harga ?? 0;
    $potongan = $transaction->potongan ?? $transaction->diskon_nominal ?? 0;
    $potonganBpjs = $transaction->potongan_bpjs ?? 0;
    $tipeBpjs = $transaction->patient->tipe_bpjs ?? '';

    // Final amount the customer pays
    $totalHarusDibayar = $transaction->total_bayar ?? ($harga - $potongan - $potonganBpjs);

    // In "Pesanan Besar" context, "Total yang dibayarkan" often refers to the net payable amount 
    // or the amount they just paid (DP). Looking at user images, it seems to be the Net Payable.
    $displayTotal = $totalHarusDibayar;

    $nama_kasir = $transaction->kasir->name ?? 'Kasir';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Pesanan Besar - {{ $transaction->no_transaksi }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            position: relative;
            margin: 0;
            padding: 5mm;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header {
            margin-bottom: 2mm;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
        }

        .header h1 {
            font-size: 12pt;
            margin: 0 0 1mm 0;
            font-weight: bold;
        }

        .header p {
            margin: 0;
            line-height: 1.3;
            font-size: 9pt;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 4mm 0 6mm 0;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -40%);
            opacity: 0.08;
            z-index: -1;
            width: 250px;
        }

        .content {
            position: relative;
            z-index: 10;
        }

        .row-info {
            display: flex;
            margin-bottom: 1.5mm;
        }

        .col-label {
            width: 35mm;
        }

        .col-sep {
            width: 3mm;
            text-align: center;
        }

        .col-val {
            flex: 1;
        }

        .items-area {
            display: flex;
            margin-bottom: 4mm;
        }

        .items-label {
            width: 35mm;
        }

        .items-sep {
            width: 3mm;
            text-align: center;
        }

        .items-table {
            flex: 1;
            width: 100%;
            border-collapse: collapse;
        }

        .items-table td {
            vertical-align: top;
            padding-bottom: 1.5mm;
        }

        .item-name {
            width: 70%;
        }

        .item-price {
            width: 30%;
            text-align: right;
        }

        .summary-container {
            display: flex;
            margin-top: 10mm;
            margin-bottom: 5mm;
            justify-content: space-between;
            align-items: flex-end;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 1.5mm 0;
        }

        .summary-table .lbl {
            width: 35mm;
        }

        .summary-table .sep {
            width: 3mm;
            text-align: center;
        }

        .summary-table .mid-space {
            width: auto;
        }

        .summary-table .amt {
            width: 35mm;
            text-align: right;
        }

        .terbilang-box {
            border: 1px solid #666;
            padding: 3mm 4mm;
            font-style: italic;
            font-weight: bold;
            font-size: 11pt;
            width: 70%;
            margin-top: 5mm;
            transform: skewX(-15deg);
            margin-left: 20px;
            background: #fdfdfd;
        }

        .terbilang-content {
            transform: skewX(15deg);
        }

        .footer {
            display: flex;
            justify-content: flex-end;
            margin-top: -10mm;
            /* pull up next to terbilang */
        }

        .signature {
            text-align: center;
            width: 70mm;
        }

        .signature-date {
            margin-bottom: 4mm;
            font-size: 10pt;
        }

        .stamp-box {
            color: #0044cc;
            margin: 4mm 0 15mm 0;
            text-align: center;
        }

        .stamp-box h2 {
            font-size: 14pt;
            margin: 0;
        }

        .stamp-box p {
            font-size: 8pt;
            margin: 1mm 0 0 0;
            line-height: 1.2;
        }

        .stamp-box .phone {
            font-weight: bold;
            font-size: 9pt;
        }

        .signature-name {
            font-size: 10pt;
        }

        .d-flex {
            display: flex;
        }
    </style>
</head>

<body onload="window.print()">
    <!-- Watermark behind content -->
    <img src="/icon.png" class="watermark" alt="Watermark">

    <div class="header">
        <h1>OPTIK PERKASA</h1>
        <p>Jl. KH. Agus Salim No. 42, Bekasi</p>
        <p>Telp/Wa : 0218800590</p>
        <p>Website : www.optikperkasa.com</p>
    </div>

    <div class="title">BUKTI PEMBAYARAN</div>

    <div class="content">
        <div class="row-info">
            <div class="col-label">No Pesanan</div>
            <div class="col-sep">:</div>
            <div class="col-val">{{ $transaction->no_transaksi }}</div>
        </div>
        <div class="row-info">
            <div class="col-label">Sudah terima dari</div>
            <div class="col-sep">:</div>
            <div class="col-val">{{ $transaction->patient->nama ?? ($transaction->nama_pasien ?? 'UMUM') }}</div>
        </div>
        <div class="items-area">
            <div class="items-label">Untuk pembayaran</div>
            <div class="items-sep">:</div>
            <div class="col-val">
                @if($showDetail)
                    <table class="items-table">
                        @foreach($transaction->items as $item)
                            <tr>
                                <td class="item-name">{{ $item->qty }}({{ strtolower(trim(penyebut($item->qty))) }})
                                    {{ $item->nama_produk }}
                                </td>
                                <td class="item-price">Rp. {{ number_format($item->harga_satuan * $item->qty, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div style="font-weight: normal; line-height: 1.6;">
                        @foreach($transaction->items as $item)
                            <tr>
                                <td class="item-name">{{ $item->qty }}({{ strtolower(trim(penyebut($item->qty))) }})
                                    {{ $item->nama_produk }}
                                </td><br>
                            </tr>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <table class="summary-table">
            <tr>
                <td class="lbl">Total</td>
                <td class="sep">:</td>
                <td class="mid-space"></td>
                <td class="amt">Rp. {{ number_format($harga, 0, ',', '.') }}</td>
            </tr>
            @if($potonganBpjs > 0)
                <tr>
                    <td class="lbl">Potongan BPJS {{ $tipeBpjs }}</td>
                    <td class="sep">:</td>
                    <td class="mid-space"></td>
                    <td class="amt">Rp. {{ number_format($potonganBpjs, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if($potongan > 0)
                <tr>
                    <td class="lbl">Diskon/Potongan</td>
                    <td class="sep">:</td>
                    <td class="mid-space"></td>
                    <td class="amt">Rp. {{ number_format($potongan, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td class="lbl">Total yang dibayarkan</td>
                <td class="sep">:</td>
                <td class="mid-space"></td>
                <td class="amt">Rp. {{ number_format($displayTotal, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="d-flex" style="justify-content: space-between; align-items: flex-end;">
            <div class="terbilang-box">
                <div class="terbilang-content">Terbilang : {{ terbilang($displayTotal) }}</div>
            </div>

            <div class="signature">
                <div class="signature-date">Bekasi, {{ $transaction->created_at->format('d/m/Y') }}</div>
                <div class="stamp-box">

                </div>
                <div class="signature-name">{{ $nama_kasir }}</div>
            </div>
        </div>
    </div>
</body>

</html>