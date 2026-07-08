<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->decimal('lat_domisili', 10, 7)->nullable()->after('alamat_domisili');
            $table->decimal('lng_domisili', 10, 7)->nullable()->after('lat_domisili');
            $table->decimal('lat_usaha', 10, 7)->nullable()->after('alamat_usaha');
            $table->decimal('lng_usaha', 10, 7)->nullable()->after('lat_usaha');
        });
    }

    public function down(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->dropColumn(['lat_domisili', 'lng_domisili', 'lat_usaha', 'lng_usaha']);
        });
    }
};
