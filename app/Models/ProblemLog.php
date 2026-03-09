<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemLog extends Model
{
    protected $fillable = [
        'tanggal','factory','shift','jenis','lokasi',
        'waktu_mulai','waktu_selesai','status','durasi',
        'deskripsi','cause','countermeasure','pic','created_by',
    ];

    // FIX: gunakan 'date:Y-m-d' bukan 'date' agar serialisasi JSON
    // mengembalikan string 'YYYY-MM-DD', bukan Carbon object yang bisa gagal encode
    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' || !$this->waktu_selesai;
    }

    public function getJenisColorAttribute(): string
    {
        return match ($this->jenis) {
            'Man'      => '#e74c3c',
            'Material' => '#f39c12',
            'Machine'  => '#1F3C88',
            'Method'   => '#729E3F',
            default    => '#888',
        };
    }
}