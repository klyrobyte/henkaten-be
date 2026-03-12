<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = ['factory', 'name', 'photo'];

    /**
     * URL foto mesin — null jika belum ada foto
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}