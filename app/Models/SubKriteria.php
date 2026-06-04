<?php

namespace App\Models;

use App\Models\Penilaian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKriteria extends Model
{
    use HasFactory;

    protected $table = 'sub_kriteria';

    protected $fillable = [
        'kriteria_id',
        'nama_sub',
        'nilai',
        'keterangan',
    ];

    // Relasi
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }
}