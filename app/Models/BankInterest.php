<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankInterest extends Model
{
    protected $table = 'bunga_tabungan';

    protected $fillable = [
        'anggota_id',
        'nasabah_id',
        'bulan',
        'tahun',
        'saldo_terendah',
        'suku_bunga',
        'nominal_bunga',
    ];

    /**
     * Get the member that owns the deposit.
     */
    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'anggota_id');
    }

    /**
     * Get the nasabah that owns the deposit.
     */
    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah', 'nasabah_id');
    }
}
