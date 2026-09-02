@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">

                                @if (Auth::user()->LevelID == 1)
                                <div class="col-md-4">
                                    <select name="IDTravel" id="IDTravel" class="form-control select2 filter">
                                        <option value="">Semua Travel</option>
                                        @foreach ($travel as $row)
                                            <option value="{{ $row->IDTravel }}"
                                                {{ $row->IDTravel == $IDTravel ? 'selected' : '' }}>
                                                {{ $row->NamaTravel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <form id="form-search" method="get" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-3"></div>
                                @else
                                <div class="col-md-8">
                                    <form id="form-search" method="get" class="w-100">
                                        <div class="input-search d-flex align-items-center px-3">
                                            <i class="fa fa-search text-gray mr-2"></i>
                                            <input type="text" class="input-text" id="search-keyword"
                                                placeholder="Cari Data">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4"></div>
                                @endif

                                <div class="mt-4 w-100">
                                    <a href="{{ url('admin/Person/tambah') }}" class="btn btn-primary float-right">
                                        Tambah
                                    </a>

                                    <table id="datatable" class="table w-100 text-nowrap">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Lengkap</th>
                                                <th>Tanggal Register</th>
                                                <th>No HP</th>
                                                <th>Foto</th>
                                                @if (Auth::user()->LevelID == 1)
                                                    <th>Travel</th>
                                                @endif
                                                @if (Auth::user()->LevelID == 1 || empty(Auth::user()->IDCabang))
                                                    <th>Cabang</th>
                                                @endif
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
    <!-- Modal Edit Nama Jamaah -->
    <div class="modal fade" id="modalEditNama" tabindex="-1" role="dialog" aria-labelledby="modalEditNamaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditNamaLabel">Edit Nama Jamaah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id-person">
                    <div class="form-group">
                        <label for="edit-nama-jamaah">Nama Lengkap</label>
                        <input type="text" id="edit-nama-jamaah" class="form-control" placeholder="Masukkan nama lengkap">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btn-simpan-nama">Simpan</button>
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
                scrollX: true,
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: "{{ url('admin/Person/get') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}"
                        d.IDTravel = $('#IDTravel').val()
                        d.searchKeyword = $('#search-keyword').val()
                    }
                },
                columns: [{
                        data: 'No',
                        className: "text-center"
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `<a href="javascript:void(0)" 
                                    class="btn-edit-nama text-primary fw-semibold text-decoration-none" 
                                    data-id="${data.IDPerson}" 
                                    data-nama="${data.NamaJamaah}"
                                    style="cursor:pointer;">
                                        ${data.NamaJamaah}
                                    </a>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return format_date_dmy(data.Tgl_Register)
                        }
                    },
                    {
                        data: 'NoHP'
                    },
                    {
                        data: null,
                        render: function(data) {
                            let foto = ''
                            if (!data.FotoProfil) {
                                foto = `${baseURL}assets/admin-page/icon/no-image.svg`
                            } else {
                                foto = `${baseURL}assets/foto-profil/${data.FotoProfil}`
                            }
                            return `
                        <button class="show-image btn p-0" data-foto="${foto}">
                            <img src="${foto}">
                        </button>
                    `
                        }
                    },
                    @if (Auth::user()->LevelID == 1)
                    {
                        data: null,
                        render: function(data) {
                            return removeDuplicates(data.NamaTravel) || '-'
                        }
                    },
                    @endif
                    @if (Auth::user()->LevelID == 1 || empty(Auth::user()->IDCabang))
                    {
                        data: 'NamaCabang',
                        defaultContent: 'Pusat'
                    },
                    @endif
                    {
                        data: null,
                        render: function(data) {
                            let checked = data.IsAktif == 1 ? 'checked' : ''
                            return `
                        <input type="checkbox"
                               ${checked}
                               value="${data.IsAktif}"
                               data-id="${data.IDPerson}"
                               class="bs-toggle"
                               data-toggle="toggle"
                               data-onstyle="success"
                               data-size="sm"
                               data-on="Aktif"
                               data-off="Nonaktif">
                    `
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            let edit = `<a href="${baseURL}admin/Person/edit?id=${btoa(data.IDPerson)}" class="btn btn-warning btn-sm mr-1 d-flex align-items-center" title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>`;
                            let detail = `
                            <a href="${baseURL}admin/Person/detail/${data.IDPerson}" class="btn btn-info btn-sm mr-1 d-flex align-items-center"
                                     title="Detail">
                                <i class="fa fa-info"></i>
                            </a>`;
                            let reset = `<button class="btn btn-inverse-warning btn-reset btn-sm mr-1" data-id="${data.IDPerson}" title="Reset Password"> <i class="fa fa-key"></i> </button>`;
                            let logout = `<button class="btn btn-danger btn-sm btn-force-logout d-flex align-items-center"
                                    data-id="${data.IDPerson}"
                                    title="Paksa Logout">
                                <i class="fa fa-sign-out"></i>
                            </button>`;

                            return `<div class="d-flex">${edit}${detail}${reset}${logout}</div>`;
                        }
                    }
                ],
                language: {
                    url: `${baseURL}assets/admin-page/datatable/id.json`,
                },
                columnDefs: [{
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
                }],
                fnDrawCallback: function() {
                    $('.bs-toggle').bootstrapToggle()
                }
            })

            $('#datatable').on('click', '.btn-reset', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin ingin mereset password jamaah ini?',
                    showCancelButton: true,
                    confirmButtonText: 'Reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Person/reset-password`,
                            type: 'POST',
                            data: { id: id },
                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            success: function(res) {
                                if (res.status) {
                                    toast('success', res.message);
                                } else {
                                    toast('error', res.message);
                                }
                            },
                            error: function() {
                                toast('error', 'Sistem Error');
                            }
                        });
                    }
                });
            });

            $('#form-search').on('submit', function(e) {
                e.preventDefault()
                table.search($('#search-keyword').val()).draw();
            });

            $("#datatable").on('change', '.bs-toggle', function(e) {
                let isaktif = $(this).val()
                let id = $(this).data('id')
                $.ajax({
                    url: `${baseURL}admin/Person/setaktif?isaktif=${isaktif}&id=${id}`,
                    type: 'GET',
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
                let foto = $(this).data('foto')
                let paket = $(this).data('paket')
                Swal.fire({
                    title: `Anda yakin ingin menghapus data?`,
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Jamaah/hapus?id=${id}&foto=${foto}&paket=${paket}`,
                            type: 'GET',
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
                                    'Data tidak dapat dihapus karena berhubungan dengan data lain'
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

            function removeDuplicates(str) {
                if (str == null || str == '') {
                    return ''
                }
                let uniqueValues = [...new Set(str.split(', '))];
                return uniqueValues.join(', ');
            }
            $(".filter").on('change', function() {
                filter()
            })

            function filter() {
                trv = $("#IDTravel").val()
                let url = `${baseURL}admin/Person/get?trv=${trv}`
                table.ajax.url(url).load();
            }

            // Force logout handler
            $("#datatable").on('click', '.btn-force-logout', function(e) {
                e.preventDefault();
                let idPerson = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin memaksa logout user ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Paksa Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `${baseURL}admin/Person/forceLogout`,
                            type: 'GET',
                            data: {
                                id: idPerson
                            },
                            success: function(res) {
                                if (res.status) {
                                    toast('success', res.message);
                                    table.ajax.reload();
                                } else {
                                    toast('error', res.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                toast('error',
                                    'Terjadi kesalahan saat memaksa logout user');
                            }
                        });
                    }
                });
            });

            // Buka modal edit nama
            $("#datatable").on('click', '.btn-edit-nama', function() {
                let id   = $(this).data('id');
                let nama = $(this).data('nama');
                $('#edit-id-person').val(id);
                $('#edit-nama-jamaah').val(nama);
                $('#modalEditNama').modal('show');
            });

            // Simpan nama jamaah via AJAX — reload tanpa pindah halaman
            $('#btn-simpan-nama').on('click', function() {
                let id   = $('#edit-id-person').val();
                let nama = $('#edit-nama-jamaah').val().trim();

                if (!nama) {
                    toast('error', 'Nama jamaah tidak boleh kosong');
                    return;
                }

                $.ajax({
                    url: `${baseURL}admin/Person/updateNama`,
                    type: 'POST',
                    data: {
                        id: id,
                        NamaJamaah: nama,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#modalEditNama').modal('hide');
                            toast('success', res.message);
                            table.ajax.reload(null, false); // false = jangan kembali ke halaman 1
                        } else {
                            toast('error', res.message);
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error');
                    }
                });
            });

        });
    </script>
@endpush
