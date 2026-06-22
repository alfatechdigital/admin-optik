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
        Schema::create('refractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();

            $table->string('doctor_name')->nullable();
            $table->string('diagnosis')->nullable();
            $table->date('exam_date')->nullable();
            $table->text('notes')->nullable();

            // OD
            $table->string('od_sc')->nullable();
            $table->string('od_sph')->nullable();
            $table->string('od_cyl')->nullable();
            $table->string('od_axis')->nullable();
            $table->string('od_add')->nullable();
            $table->string('od_pd')->nullable();
            $table->string('od_prism')->nullable();
            $table->string('od_cc')->nullable();

            // OS
            $table->string('os_sc')->nullable();
            $table->string('os_sph')->nullable();
            $table->string('os_cyl')->nullable();
            $table->string('os_axis')->nullable();
            $table->string('os_add')->nullable();
            $table->string('os_pd')->nullable();
            $table->string('os_prism')->nullable();
            $table->string('os_cc')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refractions');
    }
};
