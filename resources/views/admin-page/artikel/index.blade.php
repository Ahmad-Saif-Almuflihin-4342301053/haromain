@extends('layouts.master')

@section('content')

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form id="form-search" method="get" class="w-100">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="col-md-4">
                                    <select name="IDKatKonten" id="IDKatKonten" class="form-control select2 filter">
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($kategori_konten as $row)
                                            <option value="{{ $row->IDKatKonten }}">
                                                {{ $row->NamaKategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="" class="input-text" id="search-keyword"
                                            placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap mx-4">
                        <div class="table-responsive mt-4">
                            <a href="{{ url('admin/artikel/tambah') }}"
                                class="btn btn-primary float-right">Tambah</a>
                            <table id="datatable" class="table w-100">
                                <thead class="bg-lightgray">
                                    <tr>
                                        <th>No.</th>
                                        <th>Gambar</th>
                                        <th>Tanggal</th>
                                        <th>Judul</th>
                                        <th>Author</th>
                                        <th>No. Urut</th>
                                        <th>Kategori</th>
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
@endsection

@push('js')
<script>
     $(document).ready(function () {

        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ route('admin.artikel.get') }}",
                type: "POST",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.IDKatKonten = $('#IDKatKonten').val();
                    d.searchKeyword = $('#search-keyword').val();
                },
                error: function (xhr, error) {
                    console.error('DataTables Error:', error);
                    alert('Terjadi kesalahan saat memuat data.');
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
                        if (data.GambarKonten) {
                            let img = "{{ asset('assets/konten') }}/" + data.GambarKonten;
                            return `
                                <img src="${img}"
                                     style="width:50px;height:50px;object-fit:cover;cursor:pointer"
                                     onclick="window.open('${img}','_blank')">
                            `;
                        }
                        return '-';
                    }
                },
                { data: 'TanggalKonten' },
                { data: 'JudulKonten' },
                { data: 'Author' },
                { data: 'NoUrut', defaultContent: '-' },
                { data: 'NamaKategori' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let editUrl = "{{ route('admin.artikel.edit', ':id') }}"
                            .replace(':id', btoa(data.IDKonten));

                        let edit = `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/artikel/edit?id=${btoa(data.IDKonten)}"> <i class="fa fa-edit text-behance"></i> </a>`
                        let del = `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDKonten}" data-gambar="${data.GambarKonten}"> <i class="fa fa-trash"></i> </button>`

                        return edit + del;
                    }
                }
            ],
            language: {
                url: "{{ asset('assets/admin-page/datatable/id.json') }}"
            },
            drawCallback: function () {
                $('.bs-toggle').bootstrapToggle();
            }
        });

        // reload search
        $('#form-search').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

        // toggle rekomendasi
        $('#datatable').on('change', '.bs-toggle', function () {
            let isrekomendasi = $(this).val();
            let id = $(this).data('id');

            $.get("{{ route('admin.artikel.setrekomendasi') }}", {
                isrekomendasi: isrekomendasi,
                id: id
            }, function (res) {
                if (res.status) {
                    table.ajax.reload();
                    toast('success', res.message);
                } else {
                    toast('error', res.message);
                }
            }).fail(function () {
                toast('error', 'Sistem Error');
            });
        });

        // hapus
        $('#datatable').on('click', '.btn-del', function () {
            let id = $(this).data('id');
            let gambar = $(this).data('gambar');

            Swal.fire({
                title: 'Anda yakin ingin menghapus data?',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get("{{ route('admin.artikel.hapus') }}", {
                        id: id,
                        logo: gambar
                    }, function (res) {
                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message);
                        } else {
                            toast('error', res.message);
                        }
                    }).fail(function () {
                        toast('error', 'Data tidak dapat dihapus');
                    });
                }
            });
        });

    });
</script>
@endpush
