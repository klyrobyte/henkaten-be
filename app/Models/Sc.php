<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sc extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_label',
        'gradient',
        'order_index',
        'detail_departemen',
        'require_pin',  // ponytail: SC PIN protection
        'pin_hash',
    ];

    protected $casts = [
        'require_pin' => 'boolean',
    ];

    public function factories()
    {
        return $this->hasMany(Factory::class, 'sc_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'sc_id');
    }
}
