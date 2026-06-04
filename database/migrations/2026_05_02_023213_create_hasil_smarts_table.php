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
        Schema::create('hasil_smart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_agen_id')->constrained('calon_agen')->restrictOnDelete();
            $table->foreignId('periode_id')->constrained('periode_pendaftaran')->restrictOnDelete();
            $table->float('skor_akhir');    // hasil akhir perhitungan SMART
            $table->integer('peringkat');   // urutan dari yang tertinggi
            $table->enum('keputusan', ['direkomendasi', 'belumdirekomendasi']);
            $table->timestamp('dihitung_at')->useCurrent();
            $table->timestamps();
 
            $table->unique(['calon_agen_id', 'periode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_smarts');
    }
};
