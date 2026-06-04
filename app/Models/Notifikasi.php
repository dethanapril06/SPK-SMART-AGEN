<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'calon_agen_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calonAgen(): BelongsTo
    {
        return $this->belongsTo(CalonAgen::class);
    }
}