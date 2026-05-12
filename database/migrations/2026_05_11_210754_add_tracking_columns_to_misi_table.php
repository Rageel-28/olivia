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
        Schema::table('misi', function (Blueprint $table) {
            $table->foreignId('tester_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('aplikasi_id')->nullable()->constrained('aplikasi')->cascadeOnDelete();
            $table->integer('total_durasi_detik')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('misi', function (Blueprint $table) {
            $table->dropForeign(['tester_id']);
            $table->dropForeign(['aplikasi_id']);
            $table->dropColumn(['tester_id', 'aplikasi_id', 'total_durasi_detik']);
        });
    }
};
