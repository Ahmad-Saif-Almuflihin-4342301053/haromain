@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">

                                {{-- FORM SEARCH --}}
                                <form id="form-search" method="get" class="w-100">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="keyword" class="input-text" id="search-keyword"
                                            placeholder="Cari Data" value="{{ request('keyword') }}">
                                        <button type="submit" class="btn btn-primary ml-2">
                                            Cari
                                        </button>
                                    </div>
                                </form>

                                {{-- TABLE --}}
                                <div class="table-responsive mt-4 w-100">
                                    <a href="{{ route('admin.cabang.tambah') }}" class="btn btn-primary float-right">
                                        Tambah
                                    </a>

                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                @if(Auth::user()->LevelID == 1)
                                                    <th>Travel</th>
                                                @endif
                                                <th>Nama Cabang</th>
                                                <th>Nama Pimpinan</th>
                                                <th>No Telp Pimpinan</th>
                                                <th>Level Wilayah</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- DataTables akan mengisi --}}
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: `${baseURL}admin/Cabang/get`,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    @if(Auth::user()->LevelID == 1)
                    {
                        data: 'NamaTravel',
                        defaultContent: '-'
                    },
                    @endif
                    {
                        data: 'NamaCabang'
                    },
                    {
                        data: 'NamaPimpinanCabang'
                    },
                    {
                        data: 'NoHPPimp'
                    },
                    {
                        data: 'LevelWilayah'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let checked = data.IsAktif == 1 ? 'checked' : ''
                            return `<input type="checkbox" ${checked} value="${data.IsAktif}" data-id="${data.IDCabang}" class="bs-toggle" data-toggle="toggle" data-onstyle="success" data-size="sm" data-on="Aktif" data-off="Nonaktif">`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let edit =
                                `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Cabang/edit?id=${btoa(data.IDCabang)}"> <i class="fa fa-edit text-behance"></i> </a>`
                            let del =
                                `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDCabang}" data-dok="${data.DokPembukaanCabang}"> <i class="fa fa-trash"></i> </button>`
                            return edit + del;
                        }
                    }
                ],
                language: {
                    url: `${baseURL}assets/admin-page/datatable/id.json`,
                },
                "fnDrawCallback": function() {
                    $('.bs-toggle').bootstrapToggle();
                },
            });
            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });
            $("#datatable").on('change', '.bs-toggle', function(e) {
                let isaktif = $(this).val()
                let id = $(this).data('id')
                $.ajax({
                    url: `${baseURL}admin/Cabang/setaktif?isaktif=${isaktif}&id=${id}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {

                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message)
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            });
            $("#datatable").on('click', '.btn-del', function(e) {
                let id = $(this).data('id')
                let dok = $(this).data('dok')
                Swal.fire({
                    title: `Anda yakin ingin menghapus data?`,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Cabang/hapus?id=${id}&dok=${dok}`,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(res) {

                                if (res.status) {
                                    table.ajax.reload();
                                    toast('success', res.message)
                                } else {
                                    toast('error', res.message)
                                }
                            },
                            error: function(xhr, status, error) {
                                toast('error',
                                    'Data tidak dapat dihapus karena sudah digunakan transaksi'
                                )
                            }
                        });
                    }
                })

            });

        });
    </script>
@endpush
