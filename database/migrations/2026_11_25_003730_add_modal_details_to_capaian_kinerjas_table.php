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
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->json('capaian_iku')->nullable();
            $table->json('analisis_capaian')->nullable();
            $table->json('capaian_ro')->nullable();
            $table->json('tindak_lanjut')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->dropColumn([
                'capaian_iku',
                'analisis_capaian',
                'capaian_ro',
                'tindak_lanjut',
            ]);
        });
    }
};
