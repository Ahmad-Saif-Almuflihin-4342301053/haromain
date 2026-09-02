@extends('layouts.master')

@section('content')

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="row w-100 mb-2">
                                @if (Auth::user()->LevelID == 1)
                                    <div class="col-md-4 mb-2">
                                        <select name="IDTravel" id="filter-travel" class="form-control select2">
                                            <option value="">Semua Banner</option>
                                            <option value="haromain">Haromain (Pusat)</option>
                                            @if(isset($travel))
                                                @foreach ($travel as $row)
                                                    <option value="{{ $row->IDTravel }}">{{ $row->NamaTravel }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                @endif
                                <div class="{{ Auth::user()->LevelID == 1 ? 'col-md-8' : 'col-md-12' }} mb-2">
                                    <form id="form-search" method="get" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" class="input-text" id="search-keyword" placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <a href="{{ url('admin/Banner/tambah') }}" class="btn btn-primary float-right">Tambah</a>

                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>Foto</th>
                                            @if(Auth::user()->LevelID == 1)
                                                <th>Travel</th>
                                            @endif
                                            <th>Keterangan 1</th>
                                            <th>Keterangan 2</th>
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
    $(document).ready(function () {

        const isSuperadmin = {{ Auth::user()->LevelID == 1 ? 'true' : 'false' }};
        const baseURL = "{{ url('/') }}/";

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('admin.banner.get') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: function (d) {
                    if (isSuperadmin) {
                        d.IDTravel = $('#filter-travel').val();
                    }
                }
            },
            columns: [
                {
                    data: 'No',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let foto = ''
                        if (!data.Gambar) {
                            foto = `${baseURL}assets/admin-page/icon/no-image.svg`
                        } else {
                            foto = `${baseURL}assets/banner/${data.Gambar}`
                        }
                        return `<button class="show-image btn p-0" data-foto="${foto}">
                                    <img src="${foto}">
                                </button>`;
                    }
                },
                @if(Auth::user()->LevelID == 1)
                {
                    data: 'NamaTravel',
                    name: 'msttravel.NamaTravel',
                    render: function (data, type, row) {
                        if (!row.IDTravel) {
                            return '<span class="badge badge-primary font-weight-bold">Haromain</span>';
                        }
                        return data ? `<span class="badge badge-info">${data}</span>` : '<span class="text-muted">-</span>';
                    }
                },
                @endif
                { data: 'Keterangan1' },
                { data: 'Keterangan2' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let checked = data.IsAktif == 1 ? 'checked' : ''
                        return `<input type="checkbox" ${checked}
                                value="${data.IsAktif}"
                                data-id="${data.KodeBanner}"
                                class="bs-toggle"
                                data-toggle="toggle"
                                data-onstyle="success"
                                data-size="sm"
                                data-on="Aktif"
                                data-off="Nonaktif">`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let edit = `<a class="btn btn-inverse-info btn-edit p-2 mr-2"
                                      href="${baseURL}admin/Banner/edit?id=${btoa(data.KodeBanner)}">
                                      <i class="fa fa-edit text-behance"></i>
                                    </a>`;
                        let del = `<button class="btn btn-inverse-danger btn-del p-2"
                                      data-id="${data.KodeBanner}"
                                      data-foto="${data.Gambar}">
                                      <i class="fa fa-trash"></i>
                                   </button>`;
                        return edit + del;
                    }
                }
            ],
            language: {
                url: `${baseURL}assets/admin-page/datatable/id.json`
            },
            fnDrawCallback: function () {
                $('.bs-toggle').bootstrapToggle();
            }
        });

        if (isSuperadmin) {
            $('#filter-travel').on('change', function () {
                table.ajax.reload();
            });
        }

        $('#form-search').on('submit', function (e) {
            e.preventDefault()
            table.search($('#search-keyword').val()).draw()
        });

        $('#datatable').on('change', '.bs-toggle', function () {
            let isaktif = $(this).val()
            let id = $(this).data('id')

            $.get(`${baseURL}admin/Banner/setaktif`, {
                isaktif: isaktif,
                id: id
            }).done(function (res) {

                if (res.status) {
                    table.ajax.reload()
                    toast('success', res.message)
                } else {
                    toast('error', res.message)
                }
            }).fail(function () {
                toast('error', 'Sistem Error')
            })
        });

        $('#datatable').on('click', '.btn-del', function () {
            let id = $(this).data('id')
            let foto = $(this).data('foto')

            Swal.fire({
                title: 'Anda yakin ingin menghapus data?',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get(`${baseURL}admin/Banner/hapus`, {
                        id: id,
                        foto: foto
                    }).done(function (res) {

                        if (res.status) {
                            table.ajax.reload()
                            toast('success', res.message)
                        } else {
                            toast('error', res.message)
                        }
                    }).fail(function () {
                        toast('error', 'Data tidak dapat dihapus karena sudah digunakan transaksi')
                    })
                }
            })
        });

        $('#datatable').on('click', '.show-image', function () {
            Swal.fire({
                imageUrl: $(this).data('foto'),
                width: 700
            })
        });

    });
</script>

@endpush
