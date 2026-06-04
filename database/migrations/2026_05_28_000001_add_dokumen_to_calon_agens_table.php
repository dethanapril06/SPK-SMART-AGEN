<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->string('ktp_path')->nullable()->after('alamat');
            $table->string('nib_path')->nullable()->after('ktp_path');
            $table->string('npwp_path')->nullable()->after('nib_path');
            $table->string('formulir_pendaftaran_path')->nullable()->after('npwp_path');
        });
    }

    public function down(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->dropColumn([
                'ktp_path',
                'nib_path',
                'npwp_path',
                'formulir_pendaftaran_path',
            ]);
        });
    }
};
