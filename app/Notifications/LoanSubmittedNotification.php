<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Loan;

class LoanSubmittedNotification extends Notification
{
    use Queueable;

    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $borrower = $this->loan->nasabah ? $this->loan->nasabah->nama : ($this->loan->member ? $this->loan->member->nama : 'Unknown');
        return [
            'loan_id' => $this->loan->id,
            'message' => 'Pinjaman baru diajukan oleh ' . $borrower,
            'amount' => $this->loan->jumlah_pinjaman,
        ];
    }
}
