<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingHistory extends Model
{
    protected $table = 'riwayat_tabungan';

    protected $fillable = [
        'anggota_id',
        'nasabah_id',
        'tanggal',
        'keterangan',
        'debet',
        'kredit',
        'saldo',
        'ref_type',
        'ref_id',
    ];

    /**
     * Get the member that owns the saving history.
     */
    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'anggota_id');
    }

    /**
     * Get the nasabah that owns the saving history.
     */
    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah', 'nasabah_id');
    }

    public function ref()
    {
        return $this->morphTo();
    }
}
