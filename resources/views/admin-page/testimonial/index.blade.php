@extends('layouts.master')

@section('content')

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap mx-4">
                            <h4 class="card-title">Manajemen Testimonial</h4>
                                <a href="{{ route('admin.testimonial.tambah') }}"
                                    class="btn btn-primary float-right mb-3">Tambah Testimonial</a>
                            <div class="table-responsive mt-4">
                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama</th>
                                            <th>Testimoni</th>
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
        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('admin.testimonial.get') }}",
                type: "POST",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                },
                error: function (xhr, error) {
                    console.error('DataTables Error:', error);
                    toast('error', 'Terjadi kesalahan saat memuat data.');
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
                    data: 'JudulKonten',
                    name: 'JudulKonten'
                },
                {
                    data: 'IsiKonten',
                    name: 'IsiKonten',
                    render: function(data) {
                        return data.length > 100 ? data.substr(0, 100) + '...' : data;
                    }
                },
                {
                    data: 'IDKonten',
                    orderable: false,
                    searchable: false,
                    className: "text-center d-flex ",
                    render: function (data) {
                        return `
                            <a href="{{ url('admin/testimonial/edit') }}?id=${btoa(data)}" class="btn btn-sm btn-inverse-info mr-1"><i class="fa fa-edit text-behance"></i></a>
                            <button class="btn btn-sm btn-inverse-danger btn-hapus" data-id="${data}"><i class="fa fa-trash"></i></button>
                        `;
                    }
                }
            ]
        });

        $(document).on('click', '.btn-hapus', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Apakah Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.testimonial.hapus') }}",
                        type: "GET",
                        data: { id: id },
                        success: function (response) {
                            if (response.status) {
                                toast('success', response.message);
                                table.ajax.reload();
                            } else {
                                toast('error', response.message);
                            }
                        },
                        error: function () {
                            toast('error', 'Terjadi kesalahan saat menghapus data.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
