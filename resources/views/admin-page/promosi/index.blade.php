@extends('layouts.master')

@section('content')
    <!-- resources/views/admin-page/promosi/index.blade.php -->

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <!-- first row starts here -->
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="col-md-5">
                                    <select name="" id="IDPaket" class="form-control select2">
                                        <option value="">Pilih Paket</option>
                                        @foreach ($paket as $row)
                                            <option value="{{ $row->IDPaket }}">{{ $row->NamaPaket }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <form id="form-search" method="get" class="col-md-7">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="" class="input-text" id="search-keyword"
                                            placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </form>

                                <div class="table-responsive mt-4">
                                    <a href="{{ url('admin/Promosi/tambah') }}"
                                        class="btn btn-primary float-right">Tambah</a>
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Travel</th>
                                                <th>Nama Paket</th>
                                                <th>Biaya Paket Promo</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- DataTables akan di-load via AJAX --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- main-panel ends -->

    <!-- Modal Verifikasi -->
    <div class="modal fade" id="verifModal" tabindex="-1" aria-labelledby="verifModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Nama Travel</label>
                            <input type="text" id="show-NamaTravel" class="form-control" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Paket</label>
                            <input type="text" id="show-NamaPaket" class="form-control" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tanggal Mulai</label>
                            <input type="text" name="TglMulai" required class="form-control datepicker"
                                id="show-TglMulai" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tanggal Selesai</label>
                            <input type="text" name="TglBerakhir" readonly class="form-control" id="show-TglBerakhir"
                                disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Harga</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                </div>
                                <input type="text" name="Tarif" id="show-HargaPromo" placeholder="Tarif" readonly
                                    class="form-control nominal">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-verif">Verifikasi</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        $(document).ready(function() {
            let active = ''
            let level = "{{ auth()->user()->LevelID }}"
            window.rowCache = {};

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "{{ url('admin/Promosi/get') }}",
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
                    {
                        data: 'NamaTravel'
                    },
                    {
                        data: 'NamaPaket'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return "Rp " + format_nominal(data.HargaPromo)
                        }
                    },
                    {
                        data: 'TglMulai'
                    },
                    {
                        data: 'TglBerakhir'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let verified =
                                ` <span class="badge bg-inverse-primary text-white rounded-pill">Sudah Verifikasi</span>`
                            let unverified =
                                `<span class="badge bg-inverse-primary bg-inverse-warning rounded-pill">Belum Verifikasi</span>`

                            return data.IsVerified == 1 ? verified : unverified;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            rowCache[data.IDTransaksi] = data;
                            let edit =
                                `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Promosi/edit?id=${btoa(data.IDTransaksi)}"> <i class="fa fa-edit text-behance"></i> </a>`
                            let del =
                                `<button class="btn btn-inverse-danger btn-del p-2 mr-2" data-id="${data.IDTransaksi}"> <i class="fa fa-trash"></i> </button>`
                            let view = ''

                            if (level == "1") {
                                view =
                                    `<button class="btn btn-inverse-primary btn-verif p-2"  data-toggle="modal" data-target="#verifModal" data-id="${data.IDTransaksi}"> <i class="fa fa-eye"></i> </button>`
                            } else {
                                view = ''
                            }

                            return edit + del + view;
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
            $('#IDPaket').on('change', function(e) {
                filter()
            });

            function filter() {
                let paket = $("#IDPaket").val()
                let url = `${baseURL}admin/Promosi/get?paket=${paket}`
                table.ajax.url(url).load();
            }
            $("#datatable").on('click', '.btn-verif', function(e) {
                let id = $(this).data('id');
                let data = rowCache[id]

                let arrShow = ['NamaTravel', 'NamaPaket', 'TglMulai', 'TglBerakhir', 'HargaPromo']
                arrShow.forEach(el => {
                    $("#show-" + el).val(data[el])
                });
                active = $(this).data('id')
                if (data.IsVerified == 1) {
                    $("#btn-verif").hide()
                } else {
                    $("#btn-verif").show()
                }
            })
            $("#btn-verif").on('click', function(e) {
                let isaktif = $(this).val()
                let id = $(this).data('id')
                $.ajax({
                    url: `${baseURL}admin/Promosi/verifikasi?id=${active}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#verifModal').modal('hide')

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
                Swal.fire({
                    title: `Anda yakin ingin menghapus data?`,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Promosi/hapus?id=${id}`,
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
            $("#datatable").on('click', '.show-image', function(e) {
                let foto = $(this).data('foto')
                Swal.fire({
                    imageUrl: foto,
                    width: 700,
                })

            });
        });
    </script>
@endpush
