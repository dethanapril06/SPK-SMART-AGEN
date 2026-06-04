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
        Schema::create('periode_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->enum('status', ['draft', 'aktif', 'ditutup'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_pendaftaran');
    }
};
