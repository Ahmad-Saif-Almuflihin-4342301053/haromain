@extends('layouts.master')

@section('content')

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Manajemen Galeri</h4>

                            <div>
                                {{-- <button class="btn btn-info mr-2"
                                        data-toggle="modal"
                                        data-target="#modalSettingHalaman">
                                    Setting Halaman
                                </button> --}}

                                <a href="{{ route('admin.galeri.tambah') }}"
                                   class="btn btn-primary">
                                   Tambah Galeri
                                </a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <select id="filter-jenis" class="form-control">
                                    <option value="">Semua Jenis</option>
                                    <option value="gambar">Gambar</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="datatable" class="table w-100">
                                <thead class="bg-lightgray">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Jenis</th>
                                        <th>Kategori</th>
                                        <th>Konten</th>
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
{{-- 
<div class="modal fade" id="modalSettingHalaman" tabindex="-1">
    <div class="modal-dialog modal-md">
        <form action="{{ route('admin.galeri.setting.update') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setting Halaman</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <h5 class="mb-3">Header</h5>

                    <div class="form-group">
                        <label>Judul Header</label>
                        <input type="text"
                               name="judul_header"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Header</label>
                        <textarea name="deskripsi_header"
                                  class="form-control"
                                  rows="3"
                                  required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Gambar Header</label>
                        <input type="file"
                               name="gambar_header"
                               class="form-control"
                               accept="image/*">
                    </div>

                    <hr>

                    <h5 class="mb-3">Footer</h5>

                    <div class="form-group">
                        <label>Judul Footer</label>
                        <input type="text"
                               name="judul_footer"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Footer</label>
                        <textarea name="deskripsi_footer"
                                  class="form-control"
                                  rows="3"
                                  required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div> --}}

@endsection


@push('js')
<script>
$(document).ready(function () {

    const table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: "{{ route('admin.galeri.get') }}",
            type: "POST",
            data: function (d) {
                d._token = "{{ csrf_token() }}";
                d.JenisGaleri = $('#filter-jenis').val();
            }
        },
        columns: [
            { data: 'No', orderable: false, searchable: false, className:"text-center" },
            { data: 'NamaGaleri', name: 'NamaGaleri' },
            { data: 'JenisGaleri', name: 'JenisGaleri' },
            { data: 'KategoriGaleri', name: 'KategoriGaleri' },
            {
                data: 'KontenGaleri',
                name: 'KontenGaleri',
                render: function(data, type, row) {

                    if (row.JenisGaleri === 'gambar') {

                        let img = "{{ asset('assets/galeri') }}/" + data;

                        if (data.startsWith('http')) {
                            img = data;
                        }

                        return `
                            <img src="${img}"
                                 style="width:50px;height:50px;object-fit:cover;cursor:pointer"
                                 onclick="window.open('${img}','_blank')">
                        `;
                    } else {

                        if (data.startsWith('http') || data.startsWith('www')) {
                            return `<a href="${data}" target="_blank">Link Video</a>`;
                        }

                        return 'Video File';
                    }
                }
            },
            {
                data: 'IDGaleri',
                orderable: false,
                searchable: false,
                className:"text-center d-flex",
                render: function (data) {
                    return `
                        <a href="{{ url('admin/galeri/edit') }}?id=${data}"
                           class="btn btn-sm btn-inverse-info mr-2 d-flex">
                           <i class="fa fa-edit"></i>
                        </a>

                        <button class="btn btn-sm btn-inverse-danger btn-hapus d-flex"
                                data-id="${data}">
                            <i class="fa fa-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#filter-jenis').change(function () {
        table.ajax.reload();
    });

    $(document).on('click', '.btn-hapus', function () {

        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin ingin menghapus data ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.galeri.hapus') }}",
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