<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ikss_masters', function (Blueprint $table) {
            $table->id();
            $table->string('kode_indikator', 20)->unique(); // 1.1.1.1
            $table->text('sasaran_kegiatan');
            $table->text('indikator_kinerja');
            $table->string('target', 100)->nullable(); // "100 Persen", "74,45 Poin"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ikss_masters');
    }
};