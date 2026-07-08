<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->renameColumn('alamat', 'alamat_domisili');
        });

        Schema::table('calon_agen', function (Blueprint $table) {
            $table->string('nama_usaha')->nullable()->after('nama_lengkap');
            $table->text('alamat_usaha')->nullable()->after('alamat_domisili');
        });
    }

    public function down(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->dropColumn(['nama_usaha', 'alamat_usaha']);
        });

        Schema::table('calon_agen', function (Blueprint $table) {
            $table->renameColumn('alamat_domisili', 'alamat');
        });
    }
};
