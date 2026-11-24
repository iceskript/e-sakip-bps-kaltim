<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->text('link_bukti')->nullable();
            $table->text('kendala')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->dropColumn(['link_bukti', 'kendala']);
        });
    }
};
