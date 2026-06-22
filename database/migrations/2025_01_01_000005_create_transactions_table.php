<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Identitas Transaksi
            $table->string('no_transaksi', 25)->unique();
            $table->foreignId('patient_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_record_id')->nullable()->constrained()->onDelete('set null');

            // Tanggal Transaksi & Janji
            $table->date('tgl_order')->nullable();
            $table->date('tgl_faktur')->nullable();
            $table->date('tgl_selesai_janji')->nullable();

            // Status & Tipe
            $table->integer('typefaktur')->nullable();
            $table->tinyInteger('diambil')->default(0);
            $table->enum('status', ['pending', 'dp', 'lunas', 'batal', 'paid', 'unpaid'])->default('pending');

            // Finansial (Standardized with POS logic)
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('potongan_bpjs', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->decimal('dp', 15, 2)->default(0);
            $table->decimal('bayar', 15, 2)->default(0);
            $table->decimal('sisa', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris', 'debit', 'kredit'])->default('tunai');

            // Snapshot Pasien
            $table->string('no_bpjs')->nullable();
            $table->string('nama_pasien')->nullable();
            $table->text('alamat_pasien')->nullable();
            $table->string('telp_pasien')->nullable();
            $table->string('asal_resep')->nullable();

            // Refraksi OD (Kanan)
            $table->string('od_sph', 20)->nullable();
            $table->string('od_cyl', 20)->nullable();
            $table->string('od_axis', 20)->nullable();
            $table->string('od_add', 20)->nullable();
            $table->string('od_mpd', 20)->nullable();
            $table->string('od_prism', 20)->nullable();

            // Refraksi OS (Kiri)
            $table->string('os_sph', 20)->nullable();
            $table->string('os_cyl', 20)->nullable();
            $table->string('os_axis', 20)->nullable();
            $table->string('os_add', 20)->nullable();
            $table->string('os_mpd', 20)->nullable();
            $table->string('os_prism', 20)->nullable();

            // Detail Produk (Snapshot / Main Item)
            $table->string('lensa')->nullable();
            $table->string('kode_frame')->nullable();
            $table->string('nama_produk')->nullable();
            $table->text('keterangan_frame')->nullable();
            $table->string('seri')->nullable();
            $table->string('warna')->nullable();

            // Produksi & Lab
            $table->string('no_legalisasi')->nullable();
            $table->date('tgl_legalisasi')->nullable();
            $table->date('tgl_faset')->nullable();
            $table->string('lab')->nullable();
            $table->string('tempat_faset')->nullable();
            $table->date('tgl_datang_faset')->nullable();
            $table->date('tgl_selesai_faset')->nullable();

            // JSON fields (Legacy/Extra)
            $table->text('catatan')->nullable();
            $table->json('resep')->nullable();
            $table->json('jadwal')->nullable();
            $table->json('tambahan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nama_produk', 150);
            $table->string('seri', 100)->nullable();
            $table->string('warna', 100)->nullable();
            $table->integer('qty');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
    }
};
