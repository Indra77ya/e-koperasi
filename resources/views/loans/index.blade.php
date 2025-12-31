@extends('layouts.app')

@section('page-title')
    Data Pinjaman
@endsection

@section('content-app')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Pinjaman</h4>
                    <div class="card-tools">
                        <a href="{{ route('loans.create') }}" class="btn btn-primary btn-sm">Ajukan Pinjaman</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="loans-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Anggota</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Tenor</th>
                                    <th>Bunga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#loans-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('loans.index') }}',
                columns: [
                    { data: 'kode_pinjaman', name: 'kode_pinjaman' },
                    { data: 'member_name', name: 'member.nama' },
                    { data: 'jenis_pinjaman', name: 'jenis_pinjaman' },
                    { data: 'jumlah_pinjaman', name: 'jumlah_pinjaman' },
                    { data: 'tenor', name: 'tenor' },
                    { data: 'suku_bunga', name: 'suku_bunga' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
