<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenagihanLapangan extends Model
{
    protected $table = 'penagihan_lapangan';

    protected $fillable = [
        'pinjaman_id',
        'petugas_id',
        'tanggal_rencana_kunjungan',
        'status',
        'catatan_tugas',
    ];

    protected $dates = [
        'tanggal_rencana_kunjungan',
    ];

    public function loan()
    {
        return $this->belongsTo('App\Models\Loan', 'pinjaman_id');
    }

    public function petugas()
    {
        return $this->belongsTo('App\User', 'petugas_id');
    }
}
