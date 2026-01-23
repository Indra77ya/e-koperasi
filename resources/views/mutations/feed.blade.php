@extends('layouts.app')

@section('page-title')
    Semua Mutasi Harian
@endsection

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Mutasi</h3>
                <div class="card-options">
                    <form action="{{ route('mutations.feed') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="date" name="date" class="form-control" value="{{ $date }}">
                            <span class="input-group-append">
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th scope="col" class="w-1">No.</th>
                            <th scope="col">{{ __('member') }}</th>
                            <th scope="col">{{ __('date') }}</th>
                            <th scope="col">{{ __('note') }}</th>
                            <th scope="col" class="text-right">{{ __('debit') }}</th>
                            <th scope="col" class="text-right">{{ __('credit') }}</th>
                            <th scope="col" class="text-right">{{ __('balance') }}</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paginatedItems as $index => $mutation)
                            <tr>
                                <td>{{ $paginatedItems->firstItem() + $index }}</td>
                                <td>
                                    @if($mutation->member)
                                        <div>{{ $mutation->member->nama }} (Anggota)</div>
                                        <div class="small text-muted">
                                            NIK: {{ $mutation->member->nik }}
                                        </div>
                                    @elseif($mutation->nasabah)
                                        <div>{{ $mutation->nasabah->nama }} (Nasabah)</div>
                                        <div class="small text-muted">
                                            NIK: {{ $mutation->nasabah->nik }}
                                        </div>
                                    @else
                                        <div>-</div>
                                    @endif
                                </td>
                                <td>{{ $mutation->date instanceof \Carbon\Carbon ? $mutation->date->format('Y-m-d') : $mutation->date }}</td>
                                <td>{{ $mutation->description }}</td>
                                <td class="text-right">{{ format_rupiah($mutation->debit) }}</td>
                                <td class="text-right">{{ format_rupiah($mutation->credit) }}</td>
                                <td class="text-right">
                                    <div>{{ format_rupiah($mutation->balance) }}</div>
                                    <small class="text-muted">
                                        @if($mutation->balance_type == 'Tabungan')
                                            <span class="text-success">(Tabungan)</span>
                                        @else
                                            <span class="text-warning">(Pinjaman)</span>
                                        @endif
                                    </small>
                                </td>
                                <td class="text-muted">
                                    @if($mutation->date instanceof \Carbon\Carbon)
                                        {{ $mutation->date->diffForHumans() }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('data_not_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">
                    Menampilkan <span>{{ $paginatedItems->firstItem() }}</span> sampai <span>{{ $paginatedItems->lastItem() }}</span> dari <span>{{ $paginatedItems->total() }}</span> entri
                </p>
                <ul class="pagination m-0 ml-auto">
                    {{ $paginatedItems->links() }}
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
