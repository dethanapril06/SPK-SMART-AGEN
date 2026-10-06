<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update role enum in users table
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas_survey', 'calon_agen') NOT NULL DEFAULT 'calon_agen'");
        }

        // Mark existing users email verified so they are not blocked
        DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);

        // 2. Add kuota to periode_pendaftaran
        Schema::table('periode_pendaftaran', function (Blueprint $table) {
            $table->unsignedInteger('kuota')->default(0)->after('status');
        });

        // 3. Add fields to calon_agen
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->enum('sumber_pendaftaran', ['admin', 'mandiri'])->default('mandiri')->after('status');
            $table->foreignId('didaftarkan_oleh')->nullable()->after('sumber_pendaftaran')->constrained('users')->nullOnDelete();
            $table->enum('status_verifikasi', ['menunggu', 'valid', 'tidak_valid'])->default('menunggu')->after('didaftarkan_oleh');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan_verifikasi')->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_at')->nullable()->after('diverifikasi_oleh');
        });

        // Backfill existing data:
        // Set existing calon agen with 'disurvey', 'direkomendasi', 'belumdirekomendasi' to 'valid'
        DB::table('calon_agen')
            ->whereIn('status', ['disurvey', 'direkomendasi', 'belumdirekomendasi'])
            ->update(['status_verifikasi' => 'valid', 'diverifikasi_at' => now()]);

        // Backfill sumber_pendaftaran for calon agen with auto-generated emails
        $adminUser = DB::table('users')->where('role', 'admin')->first();
        $adminUserId = $adminUser ? $adminUser->id : null;

        $calonAgens = DB::table('calon_agen')
            ->join('users', 'calon_agen.user_id', '=', 'users.id')
            ->where('users.email', 'like', '%@calon-agen.local')
            ->select('calon_agen.id')
            ->get();

        foreach ($calonAgens as $item) {
            DB::table('calon_agen')->where('id', $item->id)->update([
                'sumber_pendaftaran' => 'admin',
                'didaftarkan_oleh' => $adminUserId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calon_agen', function (Blueprint $table) {
            $table->dropForeign(['didaftarkan_oleh']);
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn([
                'sumber_pendaftaran',
                'didaftarkan_oleh',
                'status_verifikasi',
                'catatan_verifikasi',
                'diverifikasi_oleh',
                'diverifikasi_at',
            ]);
        });

        Schema::table('periode_pendaftaran', function (Blueprint $table) {
            $table->dropColumn('kuota');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'calon_agen') NOT NULL DEFAULT 'calon_agen'");
    }
};
