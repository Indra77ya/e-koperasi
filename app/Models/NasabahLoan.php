<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NasabahLoan extends Model
{
    protected $table = 'nasabah_loans';

    protected $fillable = [
        'nasabah_id', 'amount', 'loan_date', 'due_date', 'status', 'notes',
        'loan_type', 'interest_type', 'interest_rate', 'tenor', 'admin_fee', 'disbursement_method',
        'approved_by', 'approved_at', 'disbursed_at', 'rejected_at', 'rejection_reason'
    ];

    protected $dates = [
        'loan_date', 'due_date', 'approved_at', 'disbursed_at', 'rejected_at'
    ];

    public function nasabah()
    {
        return $this->belongsTo('App\Models\Nasabah');
    }

    public function approver()
    {
        return $this->belongsTo('App\Models\User', 'approved_by');
    }

    public function installments()
    {
        return $this->hasMany('App\Models\NasabahLoanInstallment');
    }
}
