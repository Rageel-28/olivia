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
        Schema::table('misi_anggota', function (Blueprint $table) {
            $table->integer('total_durasi_detik')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('misi_anggota', function (Blueprint $table) {
            $table->dropColumn('total_durasi_detik');
        });
    }
};
