<?php

namespace App\Models;

use App\Models\Notifikasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CalonAgen extends Model
{
    use HasFactory;

    protected $table = 'calon_agen';

    protected $fillable = [
        'user_id',
        'periode_id',
        'nik',
        'nama_lengkap',
        'nama_usaha',
        'no_hp',
        'alamat_domisili',
        'lat_domisili',
        'lng_domisili',
        'alamat_usaha',
        'lat_usaha',
        'lng_usaha',
        'ktp_path',
        'nib_path',
        'npwp_path',
        'formulir_pendaftaran_path',
        'form_screening_path',
        'status',
    ];

    protected $casts = [
        'id'            => 'integer',
        'periode_id'    => 'integer',
        'lat_domisili'  => 'float',
        'lng_domisili'  => 'float',
        'lat_usaha'     => 'float',
        'lng_usaha'     => 'float',
    ];


    public function isDirekomendasi(): bool
    {
        return $this->status === 'direkomendasi';
    }

    public function isBelumDirekomendasi(): bool
    {
        return $this->status === 'belumdirekomendasi';
    }

    public function sudahDinilai(): bool
    {
        return $this->status === 'disurvey';
    }

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function hasilSmart(): HasOne
    {
        return $this->hasOne(HasilSmart::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }
}
