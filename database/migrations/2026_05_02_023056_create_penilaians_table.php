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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_id')->constrained('periode_pendaftaran')->restrictOnDelete();
            $table->foreignId('calon_agen_id')->constrained('calon_agen')->restrictOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria')->restrictOnDelete();
            $table->foreignId('sub_kriteria_id')->constrained('sub_kriteria')->restrictOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->float('nilai_input');
            $table->text('catatan')->nullable();
            $table->timestamps();
 
            // Satu calon agen hanya boleh dinilai satu kali per kriteria dalam satu periode
            $table->unique(['periode_id', 'calon_agen_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
