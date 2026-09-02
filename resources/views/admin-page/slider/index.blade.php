@extends('layouts.master')

@section('content')
<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="table-responsive mt-4">
                                <a href="{{ url('admin/Slider/tambah') }}" class="btn btn-primary float-right">Tambah</a>
                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Slider</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- content-wrapper ends -->
</div><!-- main-panel ends -->

@endsection
@push('js')
<script>
$(document).ready(function() {
    const table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: "{{ url('admin/Slider/get') }}",
            type: "POST",
            data: {_token: '{{ csrf_token() }}'}
        },
        columns: [
            { data: 'No', orderable: false, searchable: false, className: "text-center" },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    let foto = data.GambarKonten ? `{{ asset('assets/konten') }}/${data.GambarKonten}`
                                                  : `{{ asset('assets/admin-page/icon/no-image.svg') }}`;
                    return `<button class="show-image btn p-0" data-foto="${foto}">
                                <img src="${foto}" style="max-height:60px;">
                            </button>`;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {

                    let edit = `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="{{ url('admin/Slider/edit') }}?id=${btoa(data.IDKonten)}"> <i class="fa fa-edit text-behance"></i> </a>`
                    let del = `<button class="btn btn-inverse-danger btn-del p-2" data-id="${btoa(data.IDKonten)}" data-foto="${btoa(data.GambarKonten)}"> <i class="fa fa-trash"></i> </button>`

                    return edit + del;
                }
            }
        ],
        language: {
            url: "{{ asset('assets/admin-page/datatable/id.json') }}",
        },
        fnDrawCallback: function() {
            $('.bs-toggle').bootstrapToggle();
        }
    });

    // Hapus slider
    $("#datatable").on('click', '.btn-del', function() {
        let id = $(this).data('id');
        let foto = $(this).data('foto');
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
                    url: "{{ url('admin/Slider/hapus') }}?id=" + id + "&foto=" + foto,
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
                        toast('error', 'Data tidak dapat dihapus karena sudah digunakan transaksi');
                    }
                });
            }
        });
    });

    // Tampilkan gambar
    $("#datatable").on('click', '.show-image', function() {
        let foto = $(this).data('foto');
        Swal.fire({
            imageUrl: foto,
            width: 700,
        });
    });

});
</script>


@endpush
