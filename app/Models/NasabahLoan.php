<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NasabahLoan extends Model
{
    protected $table = 'nasabah_loans';

    protected $fillable = [
        'nasabah_id', 'amount', 'loan_date', 'due_date', 'status', 'notes'
    ];

    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah');
    }
}
