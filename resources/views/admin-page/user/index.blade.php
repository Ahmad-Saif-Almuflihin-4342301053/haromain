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
                                <div class="input-search d-flex align-items-center px-3">
                                    <i class="fa fa-search text-gray mr-2"></i>
                                    <input type="text" class="input-text" id="search-keyword" placeholder="Cari Data">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </form>

                            <div class="table-responsive mt-4">
                                <a href="{{ route('admin.user.tambah') }}" class="btn btn-primary float-right">Tambah</a>

                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Lengkap</th>
                                            <th>Email</th>
                                            <th>Username</th>
                                            <th>Nama Travel</th>
                                            <th>Cabang</th>
                                            <th>No HP</th>
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
    // const baseURL = "{{ url('/') }}/";

    $(document).ready(function () {

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('admin.user.get') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                }
            },
            columns: [
                { data: 'No', orderable: false, searchable: false, className: "text-center" },
                { data: 'NamaLengkap' },
                { data: 'Email' },
                { data: 'UserName' },
                { data: 'NamaTravel', defaultContent: '-' },
                {
                    data: 'NamaCabang',
                    defaultContent: '-',
                    render: function (data, type, row) {
                        if (row.IDCabang && data) {
                            return data;
                        } else if (row.IDTravel) {
                            return 'Pusat';
                        }
                        return '-';
                    }
                },
                { data: 'HPNo', orderable: false },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let foto = data.Foto
                            ? `${baseURL}assets/foto-profil/${data.Foto}`
                            : `${baseURL}assets/admin-page/icon/no-image.svg`;

                        return `<button class="show-image btn p-0" data-foto="${foto}">
                                    <img src="${foto}">
                                </button>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let checked = data.IsAktif == 1 ? 'checked' : '';
                        return `<input type="checkbox" ${checked}
                                value="${data.IsAktif}"
                                data-user="${data.UserName}"
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
                        let edit = `<a class="btn btn-inverse-info p-2 mr-2"
                            href="{{ route('admin.user.edit') }}?u=${btoa(data.UserName)}">
                            <i class="fa fa-edit text-behance"></i></a>`;

                        let del = `<button class="btn btn-inverse-danger p-2 btn-del"
                            data-username="${data.UserName}"
                            data-foto="${data.Foto}">
                            <i class="fa fa-trash"></i></button>`;

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

        $('#form-search').on('submit', function (e) {
            e.preventDefault();
            table.search($('#search-keyword').val()).draw();
        });

        $('#datatable').on('change', '.bs-toggle', function () {
            $.get("{{ route('admin.user.setaktif') }}", {
                isaktif: $(this).val(),
                username: $(this).data('user')
            }, function (res) {
                if (res.status) {
                    table.ajax.reload();
                    toast('success', res.message);
                } else {
                    toast('error', res.message);
                }
            });
        });

        $('#datatable').on('click', '.btn-del', function () {
            let username = $(this).data('username');
            let foto = $(this).data('foto');

            Swal.fire({
                title: `Anda yakin ingin menghapus data ${username}?`,
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get("{{ route('admin.user.hapus') }}", {
                        username: username,
                        foto: foto
                    }, function (res) {
                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message);
                        } else {
                            toast('error', res.message);
                        }
                    });
                }
            });
        });

        $('#datatable').on('click', '.show-image', function () {
            Swal.fire({
                imageUrl: $(this).data('foto'),
                width: 700
            });
        });

    });
</script>
@endpush
