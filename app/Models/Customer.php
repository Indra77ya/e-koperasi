<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'nasabah';

    protected $fillable = [
        'nik', 'nama', 'alamat', 'no_hp', 'pekerjaan',
        'info_bisnis', 'file_ktp', 'file_jaminan', 'status_risiko'
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'nasabah_id');
    }
}
