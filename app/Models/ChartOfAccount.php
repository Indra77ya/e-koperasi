<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type', // ASSET, LIABILITY, EQUITY, REVENUE, EXPENSE
        'normal_balance', // DEBIT, CREDIT
        'description',
    ];

    public function journalItems()
    {
        return $this->hasMany(JournalItem::class);
    }
}
