<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    public function Guru()
    {
        return $this->hasOne('App\Models\User', 'id', 'id_guru');
    }
    protected $casts = [
        'id_guru' => 'int',
        'jumlah_siswa' => 'int',
        'status' => 'int',

    ];
}
