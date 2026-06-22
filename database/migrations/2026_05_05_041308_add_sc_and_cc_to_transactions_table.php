<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('od_sc', 20)->nullable()->after('telp_pasien');
            $table->string('os_sc', 20)->nullable()->after('od_sc');
            $table->string('od_cc', 20)->nullable()->after('od_prism');
            $table->string('os_cc', 20)->nullable()->after('os_prism');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['od_sc', 'os_sc', 'od_cc', 'os_cc']);
        });
    }
};
