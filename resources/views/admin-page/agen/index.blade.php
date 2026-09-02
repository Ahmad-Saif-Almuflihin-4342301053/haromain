@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">
                                <form id="form-search" method="get" class="w-100">
                                    <div class="row align-items-center">
                                        @if (Auth::user()->LevelID == 1)
                                            <div class="col-md-4 mb-2">
                                                <select id="filter-travel" class="form-control select2">
                                                    <option value="">Semua Perusahaan Travel</option>
                                                    @foreach ($travel as $t)
                                                        <option value="{{ $t->IDTravel }}">{{ $t->NamaTravel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="{{ Auth::user()->LevelID == 1 ? 'col-md-8' : 'col-md-12' }} mb-2">
                                            <div class="input-search d-flex align-items-center px-3">
                                                <i class="fa fa-search text-gray mr-2"></i>
                                                <input type="text" name="" class="input-text" id="search-keyword"
                                                    placeholder="Cari Data">
                                                <button type="submit" class="btn btn-primary">Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="table-responsive mt-4">
                                    <a href="{{ url('admin/Agen/tambah') }}" class="btn btn-primary float-right">Tambah</a>
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                @if (Auth::user()->LevelID == 1)
                                                    <th>Travel</th>
                                                @endif
                                                <th>Nama Agen</th>
                                                <th>Kode Referral</th>
                                                <th>No HP</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Level Wilayah</th>
                                                <th>Foto</th>
                                                <th>Status</th>
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
            const isSuperadmin = {{ Auth::user()->LevelID == 1 ? 'true' : 'false' }};
            const baseURL = "{{ url('/') }}/";

            let columns = [
                {
                    data: 'No',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                }
            ];

            if (isSuperadmin) {
                columns.push({
                    data: 'NamaTravel',
                    name: 'msttravel.NamaTravel',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">-</span>';
                    }
                });
            }

            columns.push(
                {
                    data: 'NamaJamaah'
                },
                {
                    data: 'KodeReferral',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'NoHP'
                },
                {
                    data: 'TglMulaiAgen'
                },
                {
                    data: 'TglAkhirAgen',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'LevelWilayah'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let foto = ''
                        if (data.FotoProfil == null || data.FotoProfil == '') {
                            foto = `${baseURL}assets/admin-page/icon/no-image.svg`
                        } else {
                            foto = `${baseURL}assets/foto-profil/${data.FotoProfil}`
                        }
                        return `<button class="show-image btn p-0" data-foto="${foto}"><img src="${foto}"></button>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let checked = data.IsAktif == 1 ? 'checked' : ''

                        return `<input type="checkbox" ${checked} value="${data.IsAktif}" data-id="${data.IDPerson}" class="bs-toggle" data-toggle="toggle" data-onstyle="success" data-size="sm" data-on="Aktif" data-off="Nonaktif">`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let edit =
                            `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Agen/edit?id=${btoa(data.IDPerson)}"> <i class="fa fa-edit text-behance"></i> </a>`
                        let del =
                            `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDPerson}" data-foto="${data.FotoProfil}" data-dok="${data.DokMoU}"> <i class="fa fa-trash"></i> </button>`
                        return edit + del;
                    }
                }
            );

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "{{ url('admin/Agen/get') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: function(d) {
                        if (isSuperadmin) {
                            d.IDTravel = $('#filter-travel').val();
                        }
                    }
                },
                columns: columns,
                language: {
                    url: `${baseURL}assets/admin-page/datatable/id.json`,
                },
                "fnDrawCallback": function() {
                    $('.bs-toggle').bootstrapToggle();
                },
            });

            if (isSuperadmin) {
                $('#filter-travel').on('change', function() {
                    table.ajax.reload();
                });
            }
            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });
            $("#datatable").on('change', '.bs-toggle', function(e) {
                let isaktif = $(this).val()
                let id = $(this).data('id')
                $.ajax({
                    url: `${baseURL}admin/Agen/setaktif?isaktif=${isaktif}&id=${id}`,
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
                let foto = $(this).data('foto')
                Swal.fire({
                    title: `Anda yakin ingin menghapus data?`,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Agen/hapus?id=${id}&dok=${dok}&foto=${foto}`,
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
