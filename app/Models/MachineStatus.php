<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineStatus extends Model
{
    protected $fillable = ['tanggal','factory','shift','machine_name','status'];
    protected $casts    = ['tanggal' => 'date'];
}
