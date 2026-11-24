<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->dropForeign(['ikss_master_id']);
            $table->foreign('ikss_master_id')
                ->references('id')
                ->on('ikss_masters')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('capaian_kinerjas', function (Blueprint $table) {
            $table->dropForeign(['ikss_master_id']);
            $table->foreign('ikss_master_id')
                ->references('id')
                ->on('ikss_masters')
                ->cascadeOnDelete();
        });
    }
};
