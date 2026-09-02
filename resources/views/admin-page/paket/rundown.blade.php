@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <!-- first row starts here -->
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">

                        <div class="card-header">
                            <h5>Nama Paket: {{ $paket->NamaPaket }}</h5>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">

                                <form id="form-search" method="get" class="w-100">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" class="input-text" id="search-keyword"
                                            placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">
                                            Cari
                                        </button>
                                    </div>
                                </form>

                                <div class="table-responsive mt-4">
                                    <a href="{{ route('admin.paket.tambah_itenerary', [
                                        'pkt' => base64_encode($idpaket),
                                        'trv' => base64_encode($idtravel),
                                    ]) }}"
                                        class="btn btn-primary float-right">
                                        Tambah
                                    </a>

                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Tanggal</th>
                                                <th>Jam Awal</th>
                                                <th>Jam Akhir</th>
                                                <th>Agenda</th>
                                                <th>Lokasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>

                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.paket.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
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
            //datatables
            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: `${baseURL}admin/Paket/get_itenerary`,
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: function(e) {
                        e.idpaket = "{{ $idpaket }}";
                        e.idtravel = "{{ $idtravel }}";
                    }
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        data: 'TglMulai',
                        render: function(data, type, row) {
                            return data ? format_date_dmy(data) : '';
                        },
                        orderable: false,
                    },
                    {
                        data: 'TglMulai',
                        render: function(data, type, row) {
                            return data ? format_date_hm(data) : '';
                        },
                        orderable: false,
                    },
                    {
                        data: 'TglSelesai',
                        render: function(data, type, row) {
                            return data ? format_date_hm(data) : '';
                        },
                        orderable: false,
                    },
                    {
                        data: 'Agenda',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'Lokasi',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let edit =
                                `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Paket/edit_itenerary?pkt=${btoa(data.IDPaket)}&trv=${btoa(data.IDTravel)}&nrt=${btoa(data.NoUrut)}"> <i class="fa fa-edit text-behance"></i> </a>`
                            let del =
                                `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDPaket}" data-id2="${data.IDTravel}" data-id3="${data.NoUrut}"> <i class="fa fa-trash"></i> </button>`
                            return `<div class="d-flex">${edit + del}</div>`
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
            $("#datatable").on('click', '.btn-del', function(e) {
                let idpaket = $(this).data('id')
                let idtravel = $(this).data('id2')
                let nourut = $(this).data('id3')
                Swal.fire({
                    title: `Anda yakin ingin menghapus data?`,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Paket/hapus_itenerary?idpaket=${idpaket}&idtravel=${idtravel}&nourut=${nourut}`,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                res = JSON.parse(res)
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

            let select = $('#select-leader')
            $(".btn-submit-leader").on('click', function(e) {
                let id = $("#id-leader").val()
                let prevleader = $("#prev-leader").val()

                let leader = select.val()
                $.ajax({
                    url: `${baseURL}admin/Paket/leader?id=${id}&leader=${leader}&prevleader=${prevleader}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = JSON.parse(res)
                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message)
                            $('#tourModal').modal('hide');
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem error')
                    }
                });
            });

            $("#datatable").on('click', '.btn-leader', function(e) {
                let id = $(this).data('id')
                let prevleader = $(this).data('prevleader')

                $.ajax({
                    url: `${baseURL}admin/Paket/find?id=${id}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = JSON.parse(res)

                        $("#id-leader").val(id)
                        $("#prev-leader").val(prevleader)

                        select.empty()
                        let option = `<option value="">Pilih Tour Leader</option>`;
                        select.append(option)

                        if (res.status) {
                            res.data.forEach(el => {
                                option =
                                    `<option value="${el.IDPerson}">${el.NamaJamaah}</option>`;
                                select.append(option)
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', '')
                    }
                });

            });

        });
    </script>
@endpush
