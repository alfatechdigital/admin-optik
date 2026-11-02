<style>
    /* Mengatur ukuran kertas label 30mm x 15mm */
    @page {
        size: 30mm 15mm;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 2px;
        /* Sedikit padding agar tidak mepet */
        width: 30mm;
        height: 15mm;
        font-family: 'Arial', sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        overflow: hidden;
    }

    .faktur {
        font-size: 10pt;
        /* Sesuaikan ukuran font */
        font-weight: bold;
        line-height: 1;
        margin-bottom: 2px;
    }

    .nama {
        font-size: 8pt;
        line-height: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 28mm;
    }
</style>

<div class="label-container">
    <div class="faktur">{{ $transaction->no_transaksi }}</div>
    <div class="nama">{{ $transaction->patient->nama }}</div>
</div>

<script>
    // Langsung buka dialog print saat halaman dimuat
    window.onload = function () {
        window.print();
    }
</script>