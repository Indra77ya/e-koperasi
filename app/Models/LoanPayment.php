<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    protected $table = 'pembayaran_pinjaman';

    protected $fillable = [
        'pinjaman_id', 'jumlah_bayar', 'tanggal_bayar', 'keterangan'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'pinjaman_id');
    }
}
