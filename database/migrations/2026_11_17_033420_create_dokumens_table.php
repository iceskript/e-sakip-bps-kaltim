<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('menu_key', 30); // renstra, perjanjian_kinerja, materi_panduan
            $table->string('judul');
            $table->smallInteger('tahun')->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('jenis', 20)->default('Link'); // File / Link
            $table->text('url_link')->nullable();
            $table->string('file_path')->nullable();
            $table->string('filesize', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};