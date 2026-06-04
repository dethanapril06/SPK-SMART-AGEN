<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilSmart extends Model
{
    use HasFactory;

    protected $table = 'hasil_smart';

    protected $fillable = [
        'calon_agen_id',
        'periode_id',
        'skor_akhir',
        'peringkat',
        'keputusan',
        'dihitung_at',
    ];

    protected function casts(): array
    {
        return [
            'dihitung_at' => 'datetime',
            'skor_akhir'  => 'float',
        ];
    }

    public function isDirekomendasi(): bool
    {
        return $this->keputusan === 'direkomendasi';
    }

    // Relasi
    public function calonAgen(): BelongsTo
    {
        return $this->belongsTo(CalonAgen::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }
}