<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'pinjaman';

    protected $fillable = [
        'kode_pinjaman',
        'anggota_id',
        'jenis_pinjaman',
        'jumlah_pinjaman',
        'tenor',
        'suku_bunga',
        'jenis_bunga',
        'biaya_admin',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'status',
        'keterangan',
    ];

    protected $dates = [
        'tanggal_pengajuan',
        'tanggal_persetujuan',
    ];

    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'anggota_id');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\LoanInstallment', 'pinjaman_id');
    }
}
