<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    use HasFactory;

    protected $casts = [
        'id_kelas' => 'int',
        'user_id' => 'int',
        'created_at' => 'datetime'
    ];
}
