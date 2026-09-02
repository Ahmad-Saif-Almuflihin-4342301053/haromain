@extends('layouts.master')

@section('content')
    @php $edit = isset($jamaah->IDPerson) ? true : false; @endphp
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-import">
                                    <i class="mdi mdi-file-excel"></i> Import Excel
                                </button>
                            </div>
                            <form id="form-SelectJamaah" class="mb-5" action="" method="get">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="no_hp" placeholder="Cari No HP Jamaah" value="{{ request('no_hp') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Load</button>
                                    </div>
                                </div>
                            </form>

                            @if(request('no_hp') && !$jamaah)
                                <div class="alert alert-warning">
                                    Data Jamaah dengan No HP {{ request('no_hp') }} tidak ditemukan.
                                </div>
                            @endif

                            <form id="form-default" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex justify-content-between flex-wrap">
                                    <div class="form-group col-md-6">
                                        <label for="">Nama Lengkap</label>
                                        <input type="text" name="NamaJamaah" placeholder="Masukkan Nama Lengkap" disabled
                                            class="form-control" required value="{{ $jamaah->NamaJamaah ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">NIK</label>
                                        <input type="text" name="NIK" placeholder="Masukkan NIK" class="form-control"
                                            disabled required value="{{ $jamaah->NIK ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">No Passport</label>
                                        <input type="text" name="NoPasspor" placeholder="Masukkan No Passport" disabled
                                            class="form-control" value="{{ $jamaah->NoPasspor ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Jenis Kelamin</label>
                                        <input type="text" name="JenisKelamin" placeholder="Masukkan Jenis Kelamin"
                                            disabled class="form-control" value="{{ $jamaah->JenisKelamin ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">No HP</label>
                                        <input type="text" name="NoHP" placeholder="Masukkan No HP" disabled
                                            class="form-control" required value="{{ $jamaah->NoHP ?? '' }}">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Tempat Lahir</label>
                                        <input type="text" name="TempatLahir" disabled
                                            value="{{ $jamaah->TempatLahir ?? '' }}" placeholder="Masukkan Tempat Lahir"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Tanggal Lahir</label>
                                        <input type="text" name="TanggalLahir" disabled
                                            value="{{ isset($jamaah->TanggalLahir) ? datetime_to_date($jamaah->TanggalLahir) : '' }}"
                                            placeholder="Masukkan Tanggal Lahir" class="form-control datepicker">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="">Alamat</label>
                                        <div class="row">
                                            <div class="col-lg-3" id="select-prov">
                                                <select name="IDProv" id="IDProv" class="form-control select2" disabled>
                                                    <option value="">Pilih Provinsi</option>
                                                    @if (isset($prov))
                                                        @foreach ($prov as $row)
                                                            <option value="{{ $row->IDProv }}"
                                                                {{ isset($jamaah) && $row->IDProv == ($jamaah->IDProv ?? null) ? 'selected' : '' }}>
                                                                {{ $row->NamaProv }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select name="IDKab" id="IDKab" class="form-control select2" disabled>
                                                    <option value="">Pilih Kabupaten</option>
                                                    @if (isset($kab))
                                                        @foreach ($kab as $row)
                                                            <option value="{{ $row->IDKab }}"
                                                                {{ isset($jamaah) && $row->IDKab == ($jamaah->IDKab ?? null) ? 'selected' : '' }}>
                                                                {{ $row->NamaKab }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select name="IDKec" id="IDKec" class="form-control select2" disabled>
                                                    <option value="">Pilih Kecamatan</option>
                                                    @if (isset($kec))
                                                        @foreach ($kec as $row)
                                                            <option value="{{ $row->IDKec }}"
                                                                {{ isset($jamaah) && $row->IDKec == ($jamaah->IDKec ?? null) ? 'selected' : '' }}>
                                                                {{ $row->NamaKec }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select name="IDDesa" id="IDDesa" class="form-control select2" disabled>
                                                    <option value="">Pilih Desa</option>
                                                    @if (isset($desa))
                                                        @foreach ($desa as $row)
                                                            <option value="{{ $row->IDDesa }}"
                                                                {{ isset($jamaah) && $row->IDDesa == ($jamaah->IDDesa ?? null) ? 'selected' : '' }}>
                                                                {{ $row->NamaDesa }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" name="Alamat" disabled placeholder="Masukkan Alamat"
                                            class="form-control mt-2" value="{{ $jamaah->Alamat ?? '' }}">
                                    </div>

                                    @if (isset($jamaah))
                                        <div class="form-group col-md-6">
                                            <label for="">Paket</label>
                                            <select name="IDPaket" id="" class="form-control select2" required>
                                                <option value="">Pilih</option>
                                                @foreach ($paket as $row)
                                                    <option value="{{ $row->IDPaket }}"
                                                        {{ isset($jamaah) && $jamaah->IDPaket == $row->IDPaket ? 'selected' : '' }}>
                                                        {{ $row->NamaPaket }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="">Cabang</label>
                                            <select name="IDCabang" id="" class="form-control select2" required>
                                                <option value="">Pilih</option>
                                                @foreach ($cabang as $row)
                                                    <option value="{{ $row->IDCabang }}"
                                                        {{ isset($jamaah) && $jamaah->IDCabang == $row->IDCabang ? 'selected' : '' }}>
                                                        {{ $row->NamaCabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="">Tipe Kamar</label>
                                            <select name="JumlahTrip" id="" class="form-control select2"
                                                required>
                                                <option value="">Pilih</option>
                                                <option value="2"
                                                    {{ isset($jamaah) && $jamaah->JumlahTrip == '2' ? 'selected' : '' }}>
                                                    Double</option>
                                                <option value="3"
                                                    {{ isset($jamaah) && $jamaah->JumlahTrip == '3' ? 'selected' : '' }}>
                                                    Triple</option>
                                                <option value="4"
                                                    {{ isset($jamaah) && $jamaah->JumlahTrip == '4' ? 'selected' : '' }}>
                                                    Quad</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="">Agen</label>
                                            <select name="IDPersonAgen" id="IDPersonAgen" required
                                                class="form-control select2">
                                                <option value="">Pilih</option>
                                                @if (isset($agentravel))
                                                    @foreach ($agentravel as $row)
                                                        <option value="{{ $row->IDPerson }}"
                                                            {{ isset($jamaah) && $jamaah->IDPersonAgen == $row->IDPerson ? 'selected' : '' }}>
                                                            {{ $row->NamaJamaah }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @endif

                                </div>

                                @if (isset($jamaah))
                                    <div class="text-right">
                                        <input type="hidden" name="id" value="{{ $jamaah->IDPerson }}">
                                        <a href="#" id="btn-cetak" target="_blank" class="btn btn-warning mr-2 d-none">
                                            <i class="mdi mdi-printer"></i> Cetak Nota
                                        </a>
                                        <button type="button" onclick="history.back()"
                                            class="btn btn-outline-primary mr-2">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                @endif

                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="modal-import-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-import-label">Import Jamaah Paket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-import" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0">File Excel</label>
                                <a href="{{ route('admin.entryjamaahpaket.download_template') }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="mdi mdi-download"></i> Download Template
                                </a>
                            </div>
                            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls,.csv,.txt">
                            <small class="text-muted">Format: No, No HP, Nama Jamaah</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Paket</label>
                            <select name="IDPaket" class="form-control select2" required style="width:100%">
                                <option value="">Pilih Paket</option>
                                @foreach ($paket as $row)
                                    <option value="{{ $row->IDPaket }}">{{ $row->NamaPaket }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cabang</label>
                            <select name="IDCabang" class="form-control select2" required style="width:100%">
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabang as $row)
                                    <option value="{{ $row->IDCabang }}">{{ $row->NamaCabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tipe Kamar</label>
                            <select name="JumlahTrip" class="form-control select2" required style="width:100%">
                                <option value="">Pilih Tipe Kamar</option>
                                <option value="2">Double</option>
                                <option value="3">Triple</option>
                                <option value="4">Quad</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Agen</label>
                            <select name="IDPersonAgen" class="form-control select2" required style="width:100%">
                                <option value="">Pilih Agen</option>
                                @if(isset($agentravel))
                                    @foreach ($agentravel as $row)
                                        <option value="{{ $row->IDPerson }}">{{ $row->NamaJamaah }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div id="import-result" class="d-none">
                            <hr>
                            <h6>Hasil Import</h6>
                            <div class="alert alert-success">Berhasil: <span id="success-count">0</span></div>
                            <div id="failed-list-container" class="d-none">
                                <div class="alert alert-danger">Gagal:</div>
                                <ul id="failed-list" class="list-group"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
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
            let doc_index = 0;
            const isEdit = {{ isset($jamaah->IDPerson) ? 'true' : 'false' }};
            if (!isEdit) setprov();
            setagen();

            $('#form-default').submit(function(e) {
                e.preventDefault();
                $('#btn-cetak').addClass('d-none');
                const formData = new FormData($('#form-default')[0]);
                $.ajax({
                    url: `${baseURL}admin/Entryjamaahpaket/simpan`,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res.status) {
                            if (res.print_url) {
                                $('#btn-cetak').attr('href', res.print_url).removeClass('d-none');
                            }
                            toast('success', res.message)
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            });

            // Handle Import
            $('#form-import').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = $(this).find('button[type="submit"]');
                const originalText = btn.text();
                
                btn.prop('disabled', true).text('Mengimport...');
                $('#import-result').addClass('d-none');
                $('#failed-list').empty();
                $('#failed-list-container').addClass('d-none');

                $.ajax({
                    url: `${baseURL}admin/Entryjamaahpaket/import`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        btn.prop('disabled', false).text(originalText);
                        
                        if (res.status) {
                            $('#import-result').removeClass('d-none');
                            $('#success-count').text(res.success_count);
                            
                            if (res.failed.length > 0) {
                                $('#failed-list-container').removeClass('d-none');
                                res.failed.forEach(item => {
                                    $('#failed-list').append(`<li class="list-group-item text-danger">${item.no_hp} - ${item.nama} (${item.reason})</li>`);
                                });
                            } else {
                                toast('success', 'Semua data berhasil diimport');
                                setTimeout(() => location.reload(), 2000);
                            }
                        } else {
                            toast('error', res.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text(originalText);
                        toast('error', xhr.responseJSON?.message || 'Terjadi kesalahan saat import');
                    }
                });
            });

            function setprov() {
                $('#IDProv').empty().append(`<option value="">Pilih Provinsi</option>`);
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getprov`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $('#IDProv').append(
                                `<option value="${el.IDProv}">${el.NamaProv}</option>`);
                        });
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setagen() {
                let prov = $('#IDProv').val();
                let kab = $('#IDKab').val();
                let kec = $('#IDKec').val();
                let desa = $('#IDDesa').val();
                $('#IDPersonAgen').empty().append(`<option value="">Pilih Agen</option>`);
                $.ajax({
                    url: `${baseURL}admin/Jamaah/getAgenByLevel?prov=${prov}&kab=${kab}&kec=${kec}&desa=${desa}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $('#IDPersonAgen').append(
                                `<option value="${el.IDPerson}">${el.NamaJamaah}</option>`);
                        });
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            $('#IDProv').on('change', function() {
                setagen();
                setkab($(this).val());
                $('#IDKec').empty().append(`<option value="">Pilih Kecamatan</option>`);
                $('#IDDesa').empty().append(`<option value="">Pilih Desa</option>`);
            });
            $('#IDKab').on('change', function() {
                setagen();
                setkec($('#IDProv').val(), $(this).val());
                $('#IDDesa').empty().append(`<option value="">Pilih Desa</option>`);
            });
            $('#IDKec').on('change', function() {
                setagen();
                setdesa($('#IDProv').val(), $('#IDKab').val(), $(this).val());
            });
            $('#IDDesa').on('change', function() {
                setagen();
            });
            $('#SelectJamaah').on('change', function() {
                $('#form-SelectJamaah').submit();
            });

            function setkab(prov) {
                $('#IDKab').empty().append(`<option value="">Pilih Kabupaten</option>`);
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkab/${prov}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $('#IDKab').append(
                                `<option value="${el.IDKab}">${el.NamaKab}</option>`);
                        });
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setkec(prov, kab) {
                $('#IDKec').empty().append(`<option value="">Pilih Kecamatan</option>`);
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkec/${prov}/${kab}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $('#IDKec').append(
                                `<option value="${el.IDKec}">${el.NamaKec}</option>`);
                        });
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setdesa(prov, kab, kec) {
                $('#IDDesa').empty().append(`<option value="">Pilih Desa</option>`);
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getdesa/${prov}/${kab}/${kec}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            $('#IDDesa').append(
                                `<option value="${el.IDDesa}">${el.NamaDesa}</option>`);
                        });
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }
        });
    </script>

@endpush
