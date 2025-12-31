<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $table = 'pinjaman';

    protected $fillable = [
        'kode_pinjaman',
        'anggota_id',
        'nasabah_id',
        'jenis_pinjaman',
        'jumlah_pinjaman',
        'tenor',
        'suku_bunga',
        'satuan_bunga',
        'jenis_bunga',
        'biaya_admin',
        'denda_keterlambatan',
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

    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah', 'nasabah_id');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\LoanInstallment', 'pinjaman_id');
    }
}
