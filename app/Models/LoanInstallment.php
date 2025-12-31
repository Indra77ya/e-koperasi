<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInstallment extends Model
{
    protected $table = 'pinjaman_angsuran';

    protected $fillable = [
        'pinjaman_id',
        'angsuran_ke',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'total_angsuran',
        'pokok',
        'bunga',
        'sisa_pinjaman',
        'status',
    ];

    protected $dates = [
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
    ];

    public function loan()
    {
        return $this->belongsTo('App\Models\Loan', 'pinjaman_id');
    }
}
