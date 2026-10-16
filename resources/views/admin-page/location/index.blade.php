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
                                    <select name="IDKota" id="IDKota" class="form-control select2 filter">
                                        <option value="">Pilih Kota</option>
                                        @foreach ($kota as $row)
                                            <option value="{{ $row->IDKota }}">
                                                {{ $row->NamaKota }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" class="input-text" id="search-keyword" placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between flex-wrap">
                            <div class="table-responsive mt-4">
                                <a href="{{ route('admin.location.tambah') }}"
                                   class="btn btn-primary float-right">
                                    Tambah
                                </a>

                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>Lokasi</th>
                                            <th>Gambar</th>
                                            <th>Kategori</th>
                                            <th>Kota</th>
                                            <th>Rekomendasi</th>
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
                url: "{{ route('admin.location.get') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (d) {
                    d.IDKatKonten = $('#IDKatKonten').val();
                    d.IDKota = $('#IDKota').val();
                    d.searchKeyword = $('#search-keyword').val();
                },
                error: function () {
                    toast('error', 'Terjadi kesalahan saat memuat data. Silakan coba lagi.');
                }
            },
            columns: [
                {
                    data: 'No',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                { data: 'JudulKonten' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `<img src="{{ asset('assets/konten') }}/${data.GambarKonten}"
                                    class="show-image"
                                    data-foto="{{ asset('assets/konten') }}/${data.GambarKonten}"
                                    style="width:50px;height:50px;object-fit:cover;cursor:pointer;">`;
                    }
                },
                { data: 'NamaKategori' },
                { data: 'NamaKota' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let checked = data.IsRekomendasi == 1 ? 'checked' : '';
                        return `<input type="checkbox"
                                    ${checked}
                                    value="${data.IsRekomendasi}"
                                    data-id="${data.IDKonten}"
                                    class="bs-toggle"
                                    data-toggle="toggle"
                                    data-onstyle="success"
                                    data-size="sm">`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        let edit = `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="{{ url('admin/location/edit') }}?id=${btoa(data.IDKonten)}"> <i class="fa fa-edit text-behance"></i></a>`;
                        let del = `<button class="btn btn-inverse-danger btn-del p-2" data-id="${data.IDKonten}" data-gambar="${data.GambarKonten}"> <i class="fa fa-trash"></i> </button>`;
                        return edit + del;
                    }
                }
            ],
            language: {
                url: "{{ asset('assets/admin-page/datatable/id.json') }}"
            },
            fnDrawCallback: function () {
                $('.bs-toggle').bootstrapToggle();
            }
        });

        $('#form-search').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });

        $('#datatable').on('change', '.bs-toggle', function () {
            let isrekomendasi = $(this).val();
            let id = $(this).data('id');

            $.get("{{ url('admin/location/setrekomendasi') }}", {
                isrekomendasi: isrekomendasi,
                id: id
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
            let id = $(this).data('id');
            let gambarkonten = $(this).data('gambar');

            Swal.fire({
                title: 'Anda yakin ingin menghapus data?',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get("{{ url('admin/location/hapus') }}", {
                        id: id,
                        logo: gambarkonten
                    }, function (res) {
                        if (res.status) {
                            table.ajax.reload();
                            toast('success', res.message);
                        } else {
                            toast('error', res.message);
                        }
                    }).fail(function () {
                        toast('error', 'Data tidak dapat dihapus karena berhubungan dengan data lain');
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
