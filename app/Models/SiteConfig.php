<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Get a config value by key.
     */
    public static function getVal(string $key, $default = null)
    {
        return self::where('key', $key)->first()?->value ?? $default;
    }

    /**
     * Set a config value by key.
     */
    public static function setVal(string $key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
