<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capaian_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ikss_master_id')->constrained('ikss_masters')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periodes')->cascadeOnDelete();

            // Menggunakan decimal (NUMERIC) karena PostgreSQL sangat presisi untuk angka
            $table->decimal('target_pk', 10, 2)->nullable();
            $table->decimal('target_tw', 10, 2)->nullable();
            $table->decimal('realisasi', 10, 2)->nullable();
            $table->decimal('capaian_persen', 10, 2)->nullable();
            $table->decimal('capaian_tw_sebelumnya', 10, 2)->nullable();

            $table->string('status', 20)->default('Kosong'); // Kosong, Sebagian, Sudah Diisi
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Satu indikator hanya boleh punya 1 data per periode (tahun+tw)
            $table->unique(['ikss_master_id', 'periode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capaian_kinerjas');
    }
};