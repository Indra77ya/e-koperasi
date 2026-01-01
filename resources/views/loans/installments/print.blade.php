<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .receipt { max-width: 600px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; }
        .subtitle { font-size: 14px; }
        .row { display: flex; margin-bottom: 8px; }
        .label { width: 150px; font-weight: bold; }
        .value { flex: 1; border-bottom: 1px dotted #ccc; }
        .footer { margin-top: 30px; text-align: right; }
        .signature { margin-top: 50px; border-top: 1px solid #000; width: 200px; display: inline-block; text-align: center; }
        @media print {
            .no-print { display: none; }
            .receipt { border: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <div class="title">Kwitansi Pembayaran</div>
            <div class="subtitle">Angsuran Pinjaman</div>
        </div>

        <div class="row">
            <div class="label">Kode Pinjaman</div>
            <div class="value">{{ $installment->loan->kode_pinjaman }}</div>
        </div>
        <div class="row">
            <div class="label">Nama</div>
            <div class="value">
                @if($installment->loan->member)
                    {{ $installment->loan->member->nama }}
                @elseif($installment->loan->nasabah)
                    {{ $installment->loan->nasabah->nama }}
                @else
                    -
                @endif
            </div>
        </div>
        <div class="row">
            <div class="label">Angsuran Ke</div>
            <div class="value">{{ $installment->angsuran_ke }}</div>
        </div>
        <div class="row">
            <div class="label">Tanggal Bayar</div>
            <div class="value">{{ \Carbon\Carbon::parse($installment->tanggal_bayar)->format('d F Y') }}</div>
        </div>
         <div class="row">
            <div class="label">Metode</div>
            <div class="value">{{ ucfirst($installment->metode_pembayaran) }}</div>
        </div>
        <hr>
        <div class="row">
            <div class="label">Pokok + Bunga</div>
            <div class="value">Rp {{ number_format($installment->total_angsuran, 0, ',', '.') }}</div>
        </div>
        <div class="row">
            <div class="label">Denda</div>
            <div class="value">Rp {{ number_format($installment->denda, 0, ',', '.') }}</div>
        </div>
        <div class="row">
            <div class="label" style="font-size: 18px;">Total Bayar</div>
            <div class="value" style="font-size: 18px; font-weight: bold;">Rp {{ number_format($installment->total_angsuran + $installment->denda, 0, ',', '.') }}</div>
        </div>

        @if($installment->keterangan_pembayaran)
        <div class="row" style="margin-top: 10px;">
            <div class="label">Catatan</div>
            <div class="value">{{ $installment->keterangan_pembayaran }}</div>
        </div>
        @endif

        <div class="footer">
            <div style="margin-bottom: 10px;">{{ date('d F Y') }}</div>
            <div class="signature">
                Petugas
            </div>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()">Cetak</button>
            <button onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>
