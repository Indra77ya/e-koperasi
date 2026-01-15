@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" id="member_detail_desc">{{ __('detail') }} {{ __('menu.member') }}</h3>
                <div class="card-options">
                    <a href="{{ route('members.index') }}" class="btn btn-sm btn-pill btn-secondary">{{ __('back') }}</a>
                    <a class="btn btn-sm btn-pill btn-primary ml-2" href="{{ route('members.edit', $member->id) }}">{{ __('edit') }}</a>
                </div>
            </div>
            <table class="table card-table" aria-describedby="member_detail_desc">
                <tbody>
                    <tr>
                        <td style="width: 25%;" class="text-muted">{{ __('nin') }}</td>
                        <td>{{ $member->nik }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('full_name') }}</td>
                        <td>{{ $member->nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $member->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('phone') }}</td>
                        <td>{{ $member->no_hp }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('gender') }}</td>
                        <td>{{ $member->jenkel }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('date_of_birth') }}</td>
                        <td>{{ $member->tempat_lahir }}, {{ $member->tanggal_lahir->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('religion') }}</td>
                        <td>{{ $member->agama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('address') }}</td>
                        <td>{{ $member->alamat }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">{{ __('employment') }}</td>
                        <td>{{ $member->pekerjaan }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Riwayat Pinjaman</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tgl Pengajuan</th>
                                <th>Jumlah</th>
                                <th>Bunga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member->loans as $loan)
                            <tr>
                                <td>{{ $loan->kode_pinjaman }}</td>
                                <td>{{ $loan->tanggal_pengajuan->format('Y-m-d') }}</td>
                                <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>{{ $loan->suku_bunga }}% ({{ ucfirst($loan->jenis_bunga) }})</td>
                                <td>
                                    @if($loan->status == 'lunas')
                                        <span class="tag tag-success">Lunas</span>
                                    @elseif($loan->status == 'berjalan')
                                        <span class="tag tag-primary">Berjalan</span>
                                    @elseif($loan->status == 'disetujui')
                                        <span class="tag tag-info">Disetujui</span>
                                    @elseif($loan->status == 'ditolak')
                                        <span class="tag tag-danger">Ditolak</span>
                                    @elseif($loan->status == 'macet')
                                        <span class="tag tag-danger">Macet / Bermasalah</span>
                                    @else
                                        <span class="tag tag-warning">Diajukan</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-secondary">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada riwayat pinjaman.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
