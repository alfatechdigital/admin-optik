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
        $tables = ['refractions', 'prescriptions', 'old_glasses', 'transactions'];
        $columns = [
            'od_sc', 'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_pd', 'od_mpd', 'od_prism', 'od_cc',
            'os_sc', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_pd', 'os_mpd', 'os_prism', 'os_cc'
        ];

        foreach ($tables as $tableName) {
            $existingColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    $existingColumns[] = $column;
                }
            }

            if (!empty($existingColumns)) {
                Schema::table($tableName, function (Blueprint $table) use ($existingColumns) {
                    foreach ($existingColumns as $column) {
                        $table->string($column)->nullable()->change();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        // No down needed
    }
};
