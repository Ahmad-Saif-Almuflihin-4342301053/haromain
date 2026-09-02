
@extends('layouts.master')

@section('content')

@push('css')
<style>
.badge {
    font-size: 12px!important;
    padding: .3rem .3rem!important;
}
</style>
@endpush

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">

                                {{-- FORM SEARCH --}}
                                <form id="form-search" method="get" class="w-100">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" class="input-text" id="search-keyword"
                                            placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </form>

                                {{-- DATATABLE --}}
                                <div class="table-responsive mt-4">
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th width="5%">No.</th>
                                                <th>Nama Setting</th>
                                                <th>Value</th>
                                                <th width="8%">Aksi</th>
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

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Data</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="form-data" method="POST" enctype="multipart/form-data" class="main-form">
                    @csrf

                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label id="titleModel"></label>

                                    <input type="hidden" id="nosetting" name="nosetting">

                                    <input type="text" class="form-control" id="value1" name="value1"
                                        placeholder="Masukkan data" autocomplete="off" oninput="only_number('value1')"
                                        required>

                                    <div id="onlyVoucher" class="checkbox mt-2 hidden">
                                        <input type="checkbox" value="1" id="value3" name="value3">
                                        <span style="font-size: 14px;">Aktifkan Voucher</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" id="btnsave" class="btn btn-primary">Simpan</button>
                    </div>

                </form>

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
                    url: "{{ route('admin.setting.get') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 'No',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let status = '';
                            if (data.Value1 === 'AKTIF') {
                                status = ' <span class="badge badge-success">Aktif</span>';
                            } else if (data.Value1 === 'NON AKTIF') {
                                status = ' <span class="badge badge-danger">Tidak Aktif</span>';
                            }
                            return data.Jenis + status;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return format_nominal(data.Value);
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                        <button
                            class="btn btn-inverse-info btn-edit p-2 mr-2"
                            data-id="${data.KodeSetting}"
                            data-model='${JSON.stringify(data)}'>
                            <i class="fa fa-edit"></i>
                        </button>
                    `;
                        }
                    }
                ],
                language: {
                    url: "{{ asset('assets/admin-page/datatable/id.json') }}"
                }
            });

            // SEARCH
            $('#form-search').on('submit', function(e) {
                e.preventDefault();
                table.search($('#search-keyword').val()).draw();
            });

            // EDIT BUTTON
            $('#datatable').on('click', '.btn-edit', function() {
                const model = $(this).data('model');

                $('#form-data')[0].reset();
                $('#nosetting').val(model.KodeSetting);
                $('#value1').val(format_nominal(model.Value));
                $('#titleModel').html(model.Jenis);

                if (model.KodeSetting == 1 || model.KodeSetting == 2 || model.KodeSetting == 3) {
                    $('#onlyVoucher').addClass('hidden');
                    $('#value3').prop('checked', false);
                } else {
                    $('#onlyVoucher').removeClass('hidden');
                    $('#value3').prop('checked', model.Value1 === 'AKTIF');
                }

                $('#editModal').modal('show');
            });

            // SUBMIT MODAL FORM
            $('#form-data').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.setting.simpan') }}",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(res) {
                        if (res.status) {
                            $('#editModal').modal('hide');
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

        });

        // FORMAT INPUT ANGKA
        function only_number(id) {
            const el = document.getElementById(id);
            el.addEventListener('keyup', function() {
                el.value = formatRupiah(this.value);
            });
        }
    </script>
@endpush
