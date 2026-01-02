<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenagihanLog extends Model
{
    protected $table = 'penagihan_log';

    protected $fillable = [
        'pinjaman_id',
        'user_id',
        'metode_penagihan',
        'hasil_penagihan',
        'tanggal_janji_bayar',
        'catatan',
        'bukti_foto',
    ];

    protected $dates = [
        'tanggal_janji_bayar',
    ];

    public function loan()
    {
        return $this->belongsTo('App\Models\Loan', 'pinjaman_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
