<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // old_glasses & prescriptions hanya punya os_pd, sehingga insert dari POS
        // gagal karena MedicalRecordService memetakan od_mpd -> od_pd.
        Schema::table('old_glasses', function (Blueprint $table) {
            $table->string('od_pd')->nullable()->after('od_mpd');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('od_pd')->nullable()->after('od_mpd');
        });
    }

    public function down(): void
    {
        Schema::table('old_glasses', function (Blueprint $table) {
            $table->dropColumn('od_pd');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('od_pd');
        });
    }
};
