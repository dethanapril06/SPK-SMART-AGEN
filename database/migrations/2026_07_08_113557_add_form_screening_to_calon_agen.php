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
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->string('form_screening_path')->nullable()->after('formulir_pendaftaran_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->dropColumn('form_screening_path');
        });
    }
};
