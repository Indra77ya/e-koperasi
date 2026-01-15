<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $table = 'penarikan';

    /**
     * Get the member that owns the withdrawal.
     */
    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'anggota_id');
    }

    /**
     * Get the nasabah that owns the withdrawal.
     */
    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah', 'nasabah_id');
    }
}
