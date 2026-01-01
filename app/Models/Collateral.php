<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    protected $table = 'jaminan';

    protected $fillable = [
        'pinjaman_id',
        'jenis',
        'nomor',
        'pemilik',
        'nilai_taksasi',
        'foto',
        'dokumen',
        'status',
        'lokasi_penyimpanan',
        'tanggal_masuk',
        'tanggal_keluar',
        'diterima_oleh',
        'diserahkan_kepada',
        'keterangan',
    ];

    protected $dates = [
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    public function loan()
    {
        return $this->belongsTo('App\Models\Loan', 'pinjaman_id');
    }
}
