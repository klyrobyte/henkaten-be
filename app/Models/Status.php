<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['sc_id', 'key', 'label', 'icon', 'color', 'order_index'];
}
