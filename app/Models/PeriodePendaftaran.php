<?php

namespace App\Models;

use App\Models\CalonAgen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodePendaftaran extends Model
{
    use HasFactory;

    protected $table = 'periode_pendaftaran';

    protected $fillable = [
        'nama_periode',
        'tanggal_buka',
        'tanggal_tutup',
        'status',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'tanggal_buka'  => 'date',
        'tanggal_tutup' => 'date',
    ];

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    // Relasi
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function calonAgen(): HasMany
    {
        return $this->hasMany(CalonAgen::class, 'periode_id');
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'periode_id');
    }

    public function hasilSmart(): HasMany
    {
        return $this->hasMany(HasilSmart::class, 'periode_id');
    }
}