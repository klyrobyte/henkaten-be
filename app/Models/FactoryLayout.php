<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryLayout extends Model
{
    protected $fillable = [
        'sc_id',
        'factory',
        'layout_image',
        'layout_width',
        'layout_height',
    ];

    protected $casts = [
        'layout_width'  => 'integer',
        'layout_height' => 'integer',
    ];

    public function sc(): BelongsTo
    {
        return $this->belongsTo(Sc::class, 'sc_id');
    }

    /**
     * URL publik gambar layout.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->layout_image) {
            return null;
        }
        return asset('storage/layouts/' . basename($this->layout_image));
    }
}
