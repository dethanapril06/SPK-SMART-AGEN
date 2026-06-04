<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'periode_id',
        'calon_agen_id',
        'kriteria_id',
        'sub_kriteria_id',
        'admin_id',
        'nilai_input',
        'catatan',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePendaftaran::class, 'periode_id');
    }

    public function calonAgen(): BelongsTo
    {
        return $this->belongsTo(CalonAgen::class);
    }

    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function subKriteria(): BelongsTo
    {
        return $this->belongsTo(SubKriteria::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}