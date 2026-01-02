<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'transaction_date',
        'reference_number',
        'description',
        'ref_type',
        'ref_id',
    ];

    protected $dates = ['transaction_date'];

    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    public function ref()
    {
        return $this->morphTo();
    }
}
