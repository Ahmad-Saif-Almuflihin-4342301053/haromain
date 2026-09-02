@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">
                                <form id="form-search" method="get" class="w-100">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="" class="input-text" id="search-keyword"
                                            placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </form>
                                <div class="table-responsive mt-4">
                                    <a href="{{ url('admin/kota/tambah') }}" class="btn btn-primary float-right">Tambah</a>
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                @if(Auth::user()->LevelID == 1)
                                                    <th>Travel</th>
                                                @endif
                                                <th>Nama Kota</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
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
                    url: "{{ url('admin/kota/get') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables Error:', error);
                        alert('Terjadi kesalahan saat memuat data.');
                    }
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
                        name: 'msttravel.NamaTravel',
                        defaultContent: '-'
                    },
                    @endif
                    {
                        data: 'NamaKota'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let edit =
                                `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="{{ url('admin/kota/edit') }}?id=${btoa(data.IDKota)}"> <i class="fa fa-edit text-behance"></i> </a>`
                            let del =
                                `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDKota}"> <i class="fa fa-trash"></i> </button>`
                            // let edit = `
                        //     <a class="btn btn-inverse-info btn-edit p-2 mr-2"
                        //        href="{{ url('admin/kota/edit') }}?id=${btoa(data.IDKota)}">
                        //        <i class="fa fa-edit text-behance"></i>
                        //     </a>`;

                            // let del = `
                        //     <button class="btn btn-inverse-danger btn-del p-2"
                        //             data-id="${data.IDKota}">
                        //         <i class="fa fa-trash"></i>
                        //     </button>`;

                            return edit + del;
                        }
                    }
                ],
                language: {
                    url: "{{ asset('assets/admin-page/datatable/id.json') }}"
                }
            });

            $('#form-search').on('submit', function(e) {
                e.preventDefault();
                table.search($('#search-keyword').val()).draw();
            });

            $("#datatable").on('click', '.btn-del', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Anda yakin ingin menghapus data?',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/kota/hapus') }}?id=${id}`,
                            type: 'GET',
                            success: function(res) {
                                if (res.status) {
                                    table.ajax.reload();
                                    toast('success', res.message);
                                } else {
                                    toast('error', res.message);
                                }
                            },
                            error: function() {
                                toast('error',
                                    'Data tidak dapat dihapus karena berhubungan dengan data lain'
                                );
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
