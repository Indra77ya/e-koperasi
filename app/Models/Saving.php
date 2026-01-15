<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    protected $table = 'tabungan';

    protected $fillable = ['anggota_id', 'nasabah_id', 'saldo'];

    /**
     * Get the member that owns the saving.
     */
    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'anggota_id');
    }

    /**
     * Get the nasabah that owns the saving.
     */
    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah', 'nasabah_id');
    }
}
