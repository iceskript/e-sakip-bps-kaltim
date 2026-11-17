<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('tahun');
            $table->string('triwulan', 4); // 'TW1', 'TW2', 'TW3', 'TW4'
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tahun', 'triwulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};