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
        Schema::create('lakins', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->string('tipe', 10);
            $table->string('judul');
            $table->text('link_dokumen')->nullable();
            $table->string('file_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lakins');
    }
};
