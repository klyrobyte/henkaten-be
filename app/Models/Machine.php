<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Machine extends Model
{
    protected $fillable = ['factory', 'name', 'photo'];

    /**
     * URL foto mesin — null jika belum ada foto
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
}