@extends('layouts.print')

@section('title', 'Nota Keterlambatan Pembayaran')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row mb-5">
            <div class="col-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th style="width: 40%">No. Pinjaman</th>
                        <td>: {{ $loan->kode_pinjaman }}</td>
                    </tr>
                    <tr>
                        <th>Nama Peminjam</th>
                        <td>: {{ $loan->member ? $loan->member->nama : ($loan->nasabah ? $loan->nasabah->nama : '-') }}</td>
                    </tr>
                    <tr>
                        <th>No. HP</th>
                        <td>: {{ $loan->member ? $loan->member->no_hp : ($loan->nasabah ? $loan->nasabah->no_hp : '-') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th style="width: 40%">Alamat</th>
                        <td>: {{ $loan->member ? $loan->member->alamat : ($loan->nasabah ? $loan->nasabah->alamat : '-') }}</td>
                    </tr>
                    <tr>
                        <th>Total Pinjaman</th>
                        <td>: Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="alert alert-warning text-center mb-4">
            <strong>PEMBERITAHUAN KETERLAMBATAN</strong><br>
            Berdasarkan data kami, terdapat tunggakan angsuran pinjaman dengan rincian sebagai berikut:
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="bg-light">
                        <th>Angsuran Ke</th>
                        <th>Jatuh Tempo</th>
                        <th>Keterlambatan</th>
                        <th class="text-right">Pokok</th>
                        <th class="text-right">Bunga</th>
                        <th class="text-right">Admin</th>
                        <th class="text-right">Denda</th>
                        <th class="text-right font-weight-bold">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPokok = 0;
                        $totalBunga = 0;
                        $totalAdmin = 0;
                        $totalDenda = 0;
                        $grandTotal = 0;
                    @endphp
                    @foreach($lateInstallments as $inst)
                        @php
                            $daysLate = now()->diffInDays($inst->tanggal_jatuh_tempo);
                            $totalPokok += $inst->pokok;
                            $totalBunga += $inst->bunga;
                            $totalAdmin += $inst->biaya_admin;
                            $totalDenda += $inst->denda;
                            $rowTotal = $inst->pokok + $inst->bunga + $inst->biaya_admin + $inst->denda;
                            $grandTotal += $rowTotal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $inst->angsuran_ke }}</td>
                            <td>{{ $inst->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                            <td>{{ $daysLate }} Hari</td>
                            <td class="text-right">{{ number_format($inst->pokok, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($inst->bunga, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($inst->biaya_admin, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($inst->denda, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($rowTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-weight-bold bg-light">
                        <td colspan="3" class="text-center">TOTAL TUNGGAKAN</td>
                        <td class="text-right">{{ number_format($totalPokok, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalBunga, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalAdmin, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalDenda, 0, ',', '.') }}</td>
                        <td class="text-right text-red h5 mb-0" style="color: #cd201f !important;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            <p>Mohon segera melakukan pembayaran tunggakan tersebut di kantor kami atau melalui petugas lapangan kami. Abaikan surat ini jika Anda sudah melakukan pembayaran.</p>

            <div class="row mt-5">
                <div class="col-8"></div>
                <div class="col-4 text-center">
                    Petugas Penagihan,<br><br><br><br>
                    ( ____________________ )
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
