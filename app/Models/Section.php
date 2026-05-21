<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = ['factory_id', 'name', 'code', 'type', 'is_key_persons', 'is_key_robot', 'order_index'];

    protected $casts = [
        'is_key_persons' => 'boolean',
        'is_key_robot'   => 'boolean',
    ];

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }
}
