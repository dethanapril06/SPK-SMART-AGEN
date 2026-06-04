<?php

namespace App\Models;

use App\Models\SubKriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'nama_kriteria',
        'kode_kriteria',
        'bobot',
        'tipe',
        'created_by',
    ];

    public function isBenefit(): bool
    {
        return $this->tipe === 'benefit';
    }

    public function isCost(): bool
    {
        return $this->tipe === 'cost';
    }

    // Relasi
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subKriteria(): HasMany
    {
        return $this->hasMany(SubKriteria::class);
    }
}