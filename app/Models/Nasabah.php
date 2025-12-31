<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    protected $table = 'nasabah';

    protected $fillable = [
        'nik',
        'nama',
        'alamat',
        'no_hp',
        'pekerjaan',
        'detail_usaha',
        'tempat_lahir',
        'tanggal_lahir',
        'file_ktp',
        'file_jaminan',
        'status',
    ];

    protected $dates = [
        'tanggal_lahir',
    ];
}
