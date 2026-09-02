@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-wrap">
                                
                                <form id="form-filter" method="get" class="w-100 mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Filter Tanggal Registrasi</label>
                                            <input type="text" id="daterange" class="form-control" placeholder="Pilih rentang tanggal">
                                            <input type="hidden" id="start_date" name="start_date">
                                            <input type="hidden" id="end_date" name="end_date">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Cari Nama / No HP</label>
                                            <input type="text" id="filter_search" name="filter_search" class="form-control" placeholder="Cari nama jamaah atau no HP">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="btn-group w-100">
                                                <button type="button" id="btn-filter" class="btn btn-primary">
                                                    <i class="fa fa-search"></i> Filter
                                                </button>
                                                <button type="button" id="btn-reset" class="btn btn-secondary">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" id="btn-daftarkan" class="btn btn-success btn-block" disabled>
                                                <i class="fa fa-check"></i> DAFTARKAN PAKET
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive mt-4">
                                    <table id="datatable" class="table w-100">
                                        <thead class="bg-lightgray">
                                            <tr>
                                                <th style="width: 40px;">
                                                    <input type="checkbox" id="select-all">
                                                </th>
                                                <th>No.</th>
                                                <th>Nama Jamaah</th>
                                                <th>NIK</th>
                                                <th>No HP</th>
                                                <th>Tanggal Register</th>
                                                <th>Alamat</th>
                                                <th>Status</th>
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

    <!-- Modal Daftarkan Paket -->
    <div class="modal fade" id="modalDaftarkan" tabindex="-1" aria-labelledby="modalDaftarkanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDaftarkanLabel">Daftarkan Paket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-daftarkan">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Jumlah Jamaah Terpilih: <span id="selected-count">0</span></strong>
                        </div>

                        <div class="form-group">
                            <label>Pilih Travel <span class="text-danger">*</span></label>
                            <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                <option value="">Pilih Travel</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Paket <span class="text-danger">*</span></label>
                            <select name="IDPaket" id="IDPaket" class="form-control select2" required>
                                <option value="">Pilih Paket</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Cabang <span class="text-danger">*</span></label>
                            <select name="IDCabang" id="IDCabang" class="form-control select2" required>
                                <option value="">Pilih Cabang</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Pilih Agen</label>
                            <select name="IDPersonAgen" id="IDPersonAgen" class="form-control select2">
                                <option value="">Pilih Agen (Opsional)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Trip <span class="text-danger">*</span></label>
                            <select name="JumlahTrip" id="JumlahTrip" class="form-control select2" required>
                                <option value="">Pilih Jumlah Trip</option>
                                <option value="2">Double (2 orang/kamar)</option>
                                <option value="3">Triple (3 orang/kamar)</option>
                                <option value="4">Quad (4 orang/kamar)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            <i class="fa fa-save"></i> Daftarkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const baseURL = "{{ url('/') }}/";
            let selectedJamaah = [];

            // Initialize DateRangePicker
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
            });

            // Initialize DataTable
            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [[5, 'desc']],
                ajax: {
                    url: "{{ route('admin.paket-admin.get') }}",
                    type: "POST",
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.filter_search = $('#filter_search').val();
                    }
                },
                columns: [
                    { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            const isChecked = selectedJamaah.includes(data.IDPerson) ? 'checked' : '';
                            return `<input type="checkbox" class="jamaah-checkbox" value="${data.IDPerson}" ${isChecked}>`;
                        }
                    },
                    { data: 'No', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'NamaJamaah' },
                    { data: 'NIK' },
                    { data: 'NoHP' },
                    { 
                        data: null, 
                        render: function(data) {
                            return format_date_dmy(data.Tgl_Register);
                        }
                    },
                    { 
                        data: null,
                        render: function(data) {
                            let alamat = data.Alamat || '-';
                            if (data.LokasiLengkap) {
                                alamat += '<br><small class="text-muted">' + data.LokasiLengkap + '</small>';
                            }
                            return alamat;
                        }
                    },
                    { 
                        data: null, 
                        orderable: false, 
                        searchable: false,
                        render: function(data) {
                            let checked = data.IsAktif == 1 ? 'checked' : '';
                            return `<input type="checkbox" ${checked} disabled class="bs-toggle" data-toggle="toggle" data-onstyle="success" data-size="sm" data-on="Aktif" data-off="Nonaktif">`;
                        }
                    }
                ],
                language: { url: `${baseURL}assets/admin-page/datatable/id.json` },
                fnDrawCallback: function() { 
                    $('.bs-toggle').bootstrapToggle();
                    updateSelectAllCheckbox();
                }
            });

            // Filter button
            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            // Reset button
            $('#btn-reset').on('click', function() {
                $('#daterange').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                $('#filter_search').val('');
                table.ajax.reload();
            });

            // Select All checkbox
            $('#select-all').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.jamaah-checkbox:visible').prop('checked', isChecked).trigger('change');
            });

            // Individual checkbox change
            $('#datatable').on('change', '.jamaah-checkbox', function() {
                const value = $(this).val();
                const isChecked = $(this).prop('checked');

                if (isChecked) {
                    if (!selectedJamaah.includes(value)) {
                        selectedJamaah.push(value);
                    }
                } else {
                    selectedJamaah = selectedJamaah.filter(id => id !== value);
                }

                updateDaftarkanButton();
                updateSelectAllCheckbox();
            });

            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.jamaah-checkbox:visible').length;
                const checkedCheckboxes = $('.jamaah-checkbox:visible:checked').length;
                $('#select-all').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            }

            function updateDaftarkanButton() {
                $('#btn-daftarkan').prop('disabled', selectedJamaah.length === 0);
            }

            // Open modal
            $('#btn-daftarkan').on('click', function() {
                if (selectedJamaah.length === 0) {
                    toast('error', 'Pilih minimal 1 jamaah');
                    return;
                }

                $('#selected-count').text(selectedJamaah.length);
                loadTravel();
                $('#modalDaftarkan').modal('show');
            });

            // Load Travel
            function loadTravel() {
                $.ajax({
                    url: "{{ route('admin.paket-admin.getTravel') }}",
                    type: 'GET',
                    success: function(res) {
                        $('#IDTravel').empty().append('<option value="">Pilih Travel</option>');
                        if (res.status && res.data) {
                            res.data.forEach(function(item) {
                                $('#IDTravel').append(`<option value="${item.IDTravel}">${item.NamaTravel}</option>`);
                            });
                        }
                    },
                    error: function() {
                        toast('error', 'Gagal memuat data travel');
                    }
                });
            }

            // Travel change event - reload dependent dropdowns
            $('#IDTravel').on('change', function() {
                const IDTravel = $(this).val();
                
                // Reset dependent dropdowns
                $('#IDPaket').empty().append('<option value="">Pilih Paket</option>');
                $('#IDCabang').empty().append('<option value="">Pilih Cabang</option>');
                $('#IDPersonAgen').empty().append('<option value="">Pilih Agen (Opsional)</option>');
                
                if (IDTravel) {
                    loadPaket(IDTravel);
                    loadCabang(IDTravel);
                    loadAgen(IDTravel);
                }
            });

            // Load Paket
            function loadPaket(IDTravel) {
                $.ajax({
                    url: "{{ route('admin.paket-admin.getPaket') }}",
                    type: 'GET',
                    data: { IDTravel: IDTravel },
                    success: function(res) {
                        $('#IDPaket').empty().append('<option value="">Pilih Paket</option>');
                        if (res.status && res.data) {
                            res.data.forEach(function(item) {
                                const tglBerangkat = moment(item.TglBerangkat).format('DD/MM/YYYY');
                                $('#IDPaket').append(`<option value="${item.IDPaket}">${item.NamaPaket} - ${tglBerangkat}</option>`);
                            });
                        }
                    },
                    error: function() {
                        toast('error', 'Gagal memuat data paket');
                    }
                });
            }

            // Load Cabang
            function loadCabang(IDTravel) {
                $.ajax({
                    url: "{{ route('admin.paket-admin.getCabang') }}",
                    type: 'GET',
                    data: { IDTravel: IDTravel },
                    success: function(res) {
                        $('#IDCabang').empty().append('<option value="">Pilih Cabang</option>');
                        if (res.status && res.data) {
                            res.data.forEach(function(item) {
                                $('#IDCabang').append(`<option value="${item.IDCabang}">${item.NamaCabang}</option>`);
                            });
                        }
                    },
                    error: function() {
                        toast('error', 'Gagal memuat data cabang');
                    }
                });
            }

            // Load Agen
            function loadAgen(IDTravel) {
                $.ajax({
                    url: "{{ route('admin.paket-admin.getAgen') }}",
                    type: 'GET',
                    data: { IDTravel: IDTravel },
                    success: function(res) {
                        $('#IDPersonAgen').empty().append('<option value="">Pilih Agen (Opsional)</option>');
                        if (res.status && res.data) {
                            res.data.forEach(function(item) {
                                $('#IDPersonAgen').append(`<option value="${item.IDPerson}">${item.NamaJamaah}</option>`);
                            });
                        }
                    },
                    error: function() {
                        toast('error', 'Gagal memuat data agen');
                    }
                });
            }

            // Submit form
            $('#form-daftarkan').on('submit', function(e) {
                e.preventDefault();

                if (selectedJamaah.length === 0) {
                    toast('error', 'Tidak ada jamaah yang dipilih');
                    return;
                }

                const formData = new FormData(this);
                selectedJamaah.forEach(function(id) {
                    formData.append('jamaah_ids[]', id);
                });

                $('#btn-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
                $.ajax({
                    url: "{{ route('admin.paket-admin.simpan') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            toast('success', res.message);
                            $('#modalDaftarkan').modal('hide');
                            $('#form-daftarkan')[0].reset();
                            selectedJamaah = [];
                            updateDaftarkanButton();
                            $('#select-all').prop('checked', false);
                            table.ajax.reload();
                        } else {
                            toast('error', res.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Gagal mendaftarkan jamaah';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toast('error', message);
                    },
                    complete: function() {
                        $('#btn-submit').prop('disabled', false).html('<i class="fa fa-save"></i> Daftarkan');
                    }
                });
            });

            // Reset modal on close
            $('#modalDaftarkan').on('hidden.bs.modal', function() {
                $('#form-daftarkan')[0].reset();
                $('#IDPaket, #IDCabang, #IDPersonAgen, #JumlahTrip').val('').trigger('change');
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap',
                width: '100%'
            });
        });
    </script>
@endpush
