<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('os_mpd')->nullable()->after('os_add');
        });

        Schema::table('old_glasses', function (Blueprint $table) {
            $table->string('os_mpd')->nullable()->after('os_add');
        });

        Schema::table('refractions', function (Blueprint $table) {
            $table->string('od_mpd')->nullable()->after('od_add');
            $table->string('os_mpd')->nullable()->after('os_add');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('os_mpd');
        });

        Schema::table('old_glasses', function (Blueprint $table) {
            $table->dropColumn('os_mpd');
        });

        Schema::table('refractions', function (Blueprint $table) {
            $table->dropColumn(['od_mpd', 'os_mpd']);
        });
    }
};
