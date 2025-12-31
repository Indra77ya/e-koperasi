<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    protected $table = 'nasabahs';

    protected $fillable = [
        'nik', 'nama', 'alamat', 'no_hp', 'pekerjaan', 'usaha',
        'file_ktp', 'file_jaminan', 'status'
    ];

    public function loans()
    {
        return $this->hasMany('App\Models\NasabahLoan');
    }
}
