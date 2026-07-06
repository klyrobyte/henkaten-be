<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineProcess extends Model
{
    protected $fillable = [
        'sc_id',
        'factory',
        'machine_name',
        'process_name',
    ];
}
