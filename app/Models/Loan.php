<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'pinjaman';

    protected $fillable = [
        'nasabah_id', 'jumlah_pinjaman', 'tanggal_pinjaman',
        'tanggal_jatuh_tempo', 'status', 'keterangan'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'nasabah_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class, 'pinjaman_id');
    }
}
