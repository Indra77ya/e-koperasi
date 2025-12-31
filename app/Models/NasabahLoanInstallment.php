<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NasabahLoanInstallment extends Model
{
    protected $table = 'nasabah_loan_installments';

    protected $fillable = [
        'nasabah_loan_id', 'installment_number', 'due_date',
        'principal_amount', 'interest_amount', 'total_amount', 'remaining_balance',
        'status', 'paid_at', 'amount_paid', 'penalty_amount', 'notes'
    ];

    protected $dates = [
        'due_date', 'paid_at'
    ];

    public function loan()
    {
        return $this->belongsTo('App\Models\NasabahLoan', 'nasabah_loan_id');
    }
}
