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
                                    <input type="text" name="keyword" class="input-text" id="search-keyword" placeholder="Cari Data">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </form>
                            <div class="table-responsive mt-4">
                                <a href="{{ url('admin/Travel/tambah') }}" class="btn btn-primary float-right">Tambah</a>
                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>Tanggal Register</th>
                                            <th>Nama Travel</th>
                                            <th>Nama Pemilik</th>
                                            <th>Alamat Kantor</th>
                                            <th>Telepon Pemilik</th>
                                            <th>No. PPIU</th>
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
        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('admin.travel.get') }}",
                type: "POST",
                data: {_token: '{{ csrf_token() }}'}
            },
            columns: [
                {data: 'No', orderable: false, searchable: false, className: "text-center"},
                {data: 'TglRegister'},
                {data: 'NamaTravel'},
                {data: 'NamaPemilik'},
                {data: 'Alamat'},
                {data: 'NoTelpPemilik', orderable: false},
                {data: 'NoPPIU', orderable: false},
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let checked = data.IsShow == 1 ? 'checked' : '';
                        return `<input type="checkbox" ${checked} value="${data.IsShow}" data-id="${data.IDTravel}" class="bs-toggle" data-toggle="toggle" data-onstyle="success" data-size="sm" data-on="Aktif" data-off="Nonaktif">`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let edit = `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="{{ url('admin/Travel/edit') }}?id=${btoa(data.IDTravel)}"> <i class="fa fa-edit text-behance"></i> </a>`;
                        let del = `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDTravel}" data-logo="${data.LogoKantor}"> <i class="fa fa-trash"></i> </button>`;
                        return edit + del;
                    }
                }
            ],
            language: {url: "{{ asset('assets/admin-page/datatable/id.json') }}"},
            fnDrawCallback: function() {
                $('.bs-toggle').bootstrapToggle();
            },
        });

        $('#form-search').on('submit', function(e) {
            e.preventDefault();
            table.search($('#search-keyword').val()).draw();
        });

        $("#datatable").on('change', '.bs-toggle', function() {
            let isaktif = $(this).val();
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/Travel/setaktif') }}?isaktif=" + isaktif + "&id=" + id,
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
                    toast('error', 'Sistem Error');
                }
            });
        });

        $("#datatable").on('click', '.btn-del', function() {
            let id = $(this).data('id');
            let logokantor = $(this).data('logo');
            Swal.fire({
                title: `Anda yakin ingin menghapus data?`,
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/Travel/hapus') }}?id=" + id + "&logo=" + logokantor,
                        type: 'GET',
                        success: function(res) {
                            if (res.status) {
                                table.ajax.reload();
                                toast('success', res.message);
                            } else {
                                console.log(res);
                                toast('error', res.message);
                            }
                        },
                        error: function() {
                            toast('error', 'Data tidak dapat dihapus karena berhubungan dengan data lain');
                        }
                    });
                }
            });
        });

        $("#datatable").on('click', '.show-image', function() {
            let foto = $(this).data('foto');
            Swal.fire({ imageUrl: foto, width: 700 });
        });
    });
</script>
@endpush
