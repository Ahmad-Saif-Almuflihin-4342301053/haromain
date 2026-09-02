@extends('layouts.master')

@section('content')
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <!-- first row starts here -->
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">

                                {{-- Form Pencarian --}}
                                <form id="form-search" method="get" class="w-100">
                                    @csrf
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="keyword" class="input-text" id="search-keyword"
                                            placeholder="Cari Data" value="{{ request('keyword') }}">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </form>

                                {{-- Tabel dan tombol Tambah --}}
                                <div class="table-responsive mt-4">
                                    <a href="{{ url('admin/Broadcast/tambah') }}"
                                        class="btn btn-primary float-right">Tambah</a>
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Narasi Broadcast</th>
                                                <th>Provinsi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Data akan diisi via AJAX / Controller --}}
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
                    url: "{{ url('admin/Broadcast/get') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        width: '5%',
                        className: "text-center"
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '50%',
                        render: function(data, type, row) {
                            return `<div style="width:450px">${limitStringLength(data.Isi, 100)}</div>`;
                        }
                    },
                    {
                        width: '30%',
                        data: 'NamaProv'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '15%',
                        render: function(data, type, row) {
                            let edit =
                                `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Broadcast/edit?id=${btoa(data.IDPengumuman)}"> <i class="fa fa-edit text-behance"></i> </a>`
                            let del =
                                `<button class="btn btn-inverse-danger btn-del p-2" data-id="${btoa(data.IDPengumuman)}"> <i class="fa fa-trash"></i> </button>`
                            return edit + del;
                        }
                    }
                ],
                language: {
                    url: "{{ url('assets/admin-page/datatable/id.json') }}"
                },
                fnDrawCallback: function() {
                    $('.bs-toggle').bootstrapToggle();
                }
            });

            $('#form-search').on('submit', function(e) {
                e.preventDefault();
                table.search($('#search-keyword').val()).draw();
            });

            $("#datatable").on('click', '.btn-del', function() {
                let id = $(this).data('id');
                Swal.fire({
                    icon: "question",
                    title: "Konfirmasi",
                    text: "Anda yakin ingin menghapus data ini? data yang sudah dihapus tidak dapat dikembalikan",
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/Broadcast/hapus') }}?id=" + id,
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
                                    'Data tidak dapat dihapus karena sudah digunakan transaksi'
                                );
                            }
                        });
                    }
                });
            });

            $("#datatable").on('click', '.show-image', function() {
                let foto = $(this).data('foto');
                Swal.fire({
                    imageUrl: foto,
                    width: 700
                });
            });

            function limitStringLength(str, maxLength) {
                if (str.length > maxLength) {
                    return str.slice(0, maxLength) + '...';
                } else {
                    return str;
                }
            }
        });
    </script>
@endpush
