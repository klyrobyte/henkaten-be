<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factory extends Model
{
    protected $fillable = ['sc_id', 'name', 'slug', 'short_label', 'gradient', 'order_index', 'detail_departemen'];

    public function sc()
    {
        return $this->belongsTo(Sc::class, 'sc_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order_index');
    }

    /**
     * Generate slug from name automatically if not provided
     */
    public static function makeSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    }

    /**
     * Generate short label from name (e.g. "Factory 2" → "F2")
     */
    public static function makeShortLabel(string $name): string
    {
        // e.g. "Factory 2" → "F2", "Factory 3 & 4" → "F3&4", "Painting" → "PT"
        $name = trim($name);
        if (preg_match('/factory\s+(.+)/i', $name, $m)) {
            return 'F' . preg_replace('/[^a-z0-9&]/i', '', $m[1]);
        }
        // For names like "Painting", take first 2 uppercase letters
        preg_match_all('/[A-Z]/', $name, $caps);
        if (count($caps[0]) >= 2) {
            return implode('', array_slice($caps[0], 0, 2));
        }
        return strtoupper(substr($name, 0, 2));
    }
}
