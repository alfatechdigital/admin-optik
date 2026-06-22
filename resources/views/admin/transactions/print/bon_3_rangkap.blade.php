<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Nota Transaksi</title>
    <style>
        /* Pengaturan ukuran kertas thermal 80mm standar */
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
            font-family: 'Courier New', Courier, Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000000;
            background: #ffffff;
            width: 74mm; /* Lebar printable area aman printer 80mm agar tidak terpotong samping */
            max-width: 74mm;
            padding: 2mm 1mm 4mm 1mm;
        }

        /* KUNCI UTAMA: Memaksa printer melakukan cut/potong di setiap akhir section */
        .print-section {
            page-break-after: always;
            break-after: page;
            display: block;
            width: 100%;
        }

        /* Menghilangkan margin bawah pada section terakhir agar tidak memicu kertas kosong menjulur terlalu panjang */
        .print-section:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }

        /* Menghilangkan elemen yang tidak perlu saat mode cetak aktif */
        @media print {
            html, body {
                width: 74mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-section">
        @include('admin.transactions.print.bon_1_rangkap', ['copies' => 2])
    </div>

    <div class="print-section">
        @include('admin.transactions.print.fasetan')
    </div>

    <script>
        window.onload = function() {
            window.print();
            // Opsional: window.close(); // Aktifkan jika ini dibuka di tab baru dan ingin otomatis tertutup setelah cetak
        }
    </script>
</body>
</html>