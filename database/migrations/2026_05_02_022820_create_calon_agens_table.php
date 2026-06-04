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
        Schema::create('calon_agen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('periode_id')->constrained('periode_pendaftaran')->restrictOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->enum('status', ['diproses', 'disurvey', 'direkomendasi', 'belumdirekomendasi'])->default('diproses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calon_agen');
    }
};
