<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ScContext;

class SiteConfig extends Model
{
    use HasFactory;

    protected $fillable = ['sc_id', 'key', 'value'];

    /**
     * Get a config value by key.
     */
    public static function getVal(string $key, $default = null)
    {
        $scId = ScContext::id();
        return self::where('sc_id', $scId)->where('key', $key)->first()?->value ?? $default;
    }

    /**
     * Set a config value by key.
     */
    public static function setVal(string $key, $value)
    {
        $scId = auth()->check() ? (auth()->user()->sc_id ?? 1) : 1;
        return self::updateOrCreate(['sc_id' => $scId, 'key' => $key], ['value' => $value]);
    }
}
