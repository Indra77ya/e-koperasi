@extends('layouts.app')

@section('page-title')
    Data Nasabah
@endsection

@section('content-app')
    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List Nasabah</h3>
                    <div class="card-options">
                        <a href="{{ route('nasabah.create') }}" class="btn btn-primary btn-sm"><i class="fe fe-plus"></i> Tambah Nasabah</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable" id="tabel-nasabah">
                        <thead>
                            <tr>
                                <th class="w-1">No.</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Pekerjaan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        require(['datatables', 'jquery'], function(datatable, $) {
            $('#tabel-nasabah').DataTable({
                lengthChange: false,
                serverSide: true,
                searching: true,
                processing: true,
                scrollX: true,
                ajax: '{{ url('nasabah/get-json') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'id', searchable: false },
                    { data: 'nik', name: 'nik' },
                    { data: 'nama', name: 'nama' },
                    { data: 'no_hp', name: 'no_hp' },
                    { data: 'pekerjaan', name: 'pekerjaan' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                language: {
                    "url": 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json'
                }
            });
        });
    </script>
@endsection
