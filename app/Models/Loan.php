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
        'tempo_angsuran',
        'jenis_bunga',
        'biaya_admin',
        'denda_keterlambatan',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'status',
        'kolektabilitas',
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

    public function collaterals()
    {
        return $this->hasMany('App\Models\Collateral', 'pinjaman_id');
    }

    public function penagihanLogs()
    {
        return $this->hasMany('App\Models\PenagihanLog', 'pinjaman_id');
    }

    public function penagihanLapangan()
    {
        return $this->hasMany('App\Models\PenagihanLapangan', 'pinjaman_id');
    }

    // Helper to get days past due based on oldest unpaid installment
    public function getDaysPastDueAttribute()
    {
        $oldestUnpaid = $this->installments()
            ->where('status', 'belum_lunas')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->first();

        if ($oldestUnpaid) {
            return now()->diffInDays($oldestUnpaid->tanggal_jatuh_tempo);
        }

        return 0;
    }

    /**
     * Get remaining principal balance.
     */
    public function getRemainingPrincipalAttribute()
    {
        if ($this->tenor == 0) {
            // Indefinite Loan: Check latest unpaid installment's sisa_pinjaman
            // If no unpaid installment (should not happen if active), return 0 or check last paid.
            $latest = $this->installments()
                ->where('status', 'belum_lunas')
                ->latest('angsuran_ke')
                ->first();

            if ($latest) {
                return $latest->sisa_pinjaman;
            }

            // If all paid or none yet (e.g. just disbursed but query runs before installment created - unlikely)
             return 0;

        } else {
            // Fixed Loan
            $paidPrincipal = $this->installments()
                ->where('status', 'lunas')
                ->sum('pokok');

            return max(0, $this->jumlah_pinjaman - $paidPrincipal);
        }
    }
}
