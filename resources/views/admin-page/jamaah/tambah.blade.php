@extends('layouts.master')

@section('content')

    @php
        $edit = isset($jamaah?->IDPerson);
    @endphp

    @push('css')
        <style>
            select[readonly].select2-hidden-accessible+.select2-container {
                pointer-events: none;
                touch-action: none;
            }

            select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
                background: #eee;
                box-shadow: none;
            }

            select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
            select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
                display: none;
            }
        </style>
    @endpush

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">

                            <form id="form-default" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="d-flex justify-content-between flex-wrap">
                                    <div class="form-group col-md-6">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="NamaJamaah" class="form-control" required
                                            value="{{ old('NamaJamaah', $jamaah->NamaJamaah ?? '') }}"
                                            placeholder="Masukkan Nama Lengkap">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>NIK</label>
                                        <input type="text" name="NIK" class="form-control"
                                            value="{{ old('NIK', $jamaah->NIK ?? '') }}" placeholder="Masukkan NIK">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>No Passport</label>
                                        <input type="text" name="NoPasspor" class="form-control"
                                            value="{{ old('NoPasspor', $jamaah->NoPasspor ?? '') }}"
                                            placeholder="Masukkan No Passport">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Jenis Kelamin</label>
                                        <select name="JenisKelamin" id="JenisKelamin" class="form-control select2">
                                            <option value="">Pilih</option>
                                            <option value="L" @selected(old('JenisKelamin', $jamaah->JenisKelamin ?? '') === 'L')>Laki-laki</option>
                                            <option value="P" @selected(old('JenisKelamin', $jamaah->JenisKelamin ?? '') === 'P')>Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>No HP (Username)</label>
                                        <input type="text" name="NoHP" class="form-control" required
                                            value="{{ old('NoHP', $jamaah->NoHP ?? '') }}" placeholder="Masukkan No HP">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="TempatLahir" class="form-control"
                                            value="{{ old('TempatLahir', $jamaah->TempatLahir ?? '') }}"
                                            placeholder="Masukkan Tempat Lahir">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Lahir</label>
                                        <input type="text" name="TanggalLahir" class="form-control datepicker"
                                            value="{{ old('TanggalLahir', isset($jamaah->TanggalLahir) ? datetime_to_date($jamaah->TanggalLahir) : '') }}"
                                            placeholder="Masukkan Tanggal Lahir">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Alamat</label>
                                        <div class="row">
                                            <div class="col-lg-3" id="select-prov">
                                                <select name="IDProv" id="IDProv" class="form-control select2" required>
                                                    <option value="">Pilih Provinsi</option>
                                                    @if (isset($prov))
                                                        @foreach ($prov as $row)
                                                            <option value="{{ $row->IDProv }}"
                                                                {{ old('IDProv', $jamaah->IDProv ?? '') == $row->IDProv ? 'selected' : '' }}>
                                                                {{ $row->NamaProv }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select name="IDKab" id="IDKab" class="form-control select2" required>
                                                    <option value="">Pilih Kabupaten</option>
                                                    @if (isset($kab))
                                                        @foreach ($kab as $row)
                                                            <option value="{{ $row->IDKab }}"
                                                                {{ old('IDKab', $jamaah->IDKab ?? '') == $row->IDKab ? 'selected' : '' }}>
                                                                {{ $row->NamaKab }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select name="IDKec" id="IDKec" class="form-control select2" required>
                                                    <option value="">Pilih Kecamatan</option>
                                                    @if (isset($kec))
                                                        @foreach ($kec as $row)
                                                            <option value="{{ $row->IDKec }}"
                                                                {{ old('IDKec', $jamaah->IDKec ?? '') == $row->IDKec ? 'selected' : '' }}>
                                                                {{ $row->NamaKec }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <select name="IDDesa" id="IDDesa" class="form-control select2" required>
                                                    <option value="">Pilih Desa</option>
                                                    @if (isset($desa))
                                                        @foreach ($desa as $row)
                                                            <option value="{{ $row->IDDesa }}"
                                                                {{ old('IDDesa', $jamaah->IDDesa ?? '') == $row->IDDesa ? 'selected' : '' }}>
                                                                {{ $row->NamaDesa }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" name="Alamat" placeholder="Masukkan Alamat"
                                            class="form-control mt-2" value="{{ old('Alamat', $jamaah->Alamat ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Paket</label>
                                        <select name="IDPaket" class="form-control select2" required>
                                            <option value="">Pilih</option>
                                            @foreach ($paket as $row)
                                                <option value="{{ $row->IDPaket }}"
                                                    {{ old('IDPaket', $jamaah->IDPaket ?? '') == $row->IDPaket ? 'selected' : '' }}>
                                                    {{ $row->NamaPaket }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Cabang</label>
                                        <select name="IDCabang" class="form-control select2" required>
                                            <option value="">Pilih</option>
                                            @foreach ($cabang as $row)
                                                <option value="{{ $row->IDCabang }}"
                                                    {{ old('IDCabang', $jamaah->IDCabang ?? '') == $row->IDCabang ? 'selected' : '' }}>
                                                    {{ $row->NamaCabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tipe Kamar</label>
                                        <select name="JumlahTrip" class="form-control select2" required>
                                            <option value="">Pilih</option>
                                            <option value="2" @selected(old('JumlahTrip', $jamaah->JumlahTrip ?? '') == 2)>Double</option>
                                            <option value="3" @selected(old('JumlahTrip', $jamaah->JumlahTrip ?? '') == 3)>Triple</option>
                                            <option value="4" @selected(old('JumlahTrip', $jamaah->JumlahTrip ?? '') == 4)>Quad</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Agen</label>
                                        <select name="IDPersonAgen" id="IDPersonAgen" class="form-control select2"
                                            required>
                                            <option value="">Pilih</option>
                                            @if (isset($agentravel))
                                                @foreach ($agentravel as $row)
                                                    <option value="{{ $row->IDPerson }}"
                                                        {{ old('IDPersonAgen', $jamaah->IDPersonAgen ?? '') == $row->IDPerson ? 'selected' : '' }}>
                                                        {{ $row->NamaJamaah }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Email</label>
                                        <input type="text" id="email-jamaah" name="Email" class="form-control"
                                            required placeholder="Masukkan Email"
                                            value="{{ old('Email', $jamaah->Email ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>{{ $edit ? 'Ubah' : '' }} Password</label>
                                        <input type="password" name="Password" autocomplete="new-password"
                                            placeholder="Masukkan Password" class="form-control"
                                            {{ $edit ? '' : 'required' }}>
                                    </div>

                                </div>

                                <div class="text-right">
                                    <input type="hidden" name="id" value="{{ $jamaah->IDPerson ?? '' }}">
                                    <input type="hidden" name="old_FotoProfil" value="{{ $jamaah->FotoProfil ?? '' }}">
                                    <input type="hidden" name="old_IDPaket" value="{{ $jamaah->IDPaket ?? '' }}">
                                    <button type="button" id="btn-batal"
                                        class="btn btn-outline-primary mr-2">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>

                            </form>

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
            let doc_index = 0;
            const isEdit = "{{ $edit }}";
            if (isEdit == false) setprov();

            $('#btn-batal').on('click', function() {
                history.back();
            });

            $('#form-default').submit(function(e) {
                e.preventDefault();
                const formData = new FormData($('#form-default')[0]);
                $.ajax({
                    url: `${baseURL}admin/Jamaah/simpan`,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res.status) {
                            window.location.href = res.redirect;
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

            function setprov() {
                $('#IDProv').empty().append(`<option value="">Pilih Provinsi</option>`);
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getprov`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                $('#IDProv').append(
                                    `<option value="${el.IDProv}">${el.NamaProv}</option>`);
                            });
                            $('#IDProv').trigger('change.select2');
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setagen() {
                let prov = $('#IDProv').val(),
                    kab = $('#IDKab').val(),
                    kec = $('#IDKec').val(),
                    desa = $('#IDDesa').val();
                $('#IDPersonAgen').empty().append(`<option value="">Pilih Agen</option>`);
                $.ajax({
                    url: `${baseURL}admin/Jamaah/getAgenByLevel?prov=${prov}&kab=${kab}&kec=${kec}&desa=${desa}`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                $('#IDPersonAgen').append(
                                    `<option value="${el.IDPerson}">${el.NamaJamaah}</option>`);
                            });
                            $('#IDPersonAgen').trigger('change.select2');
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            $('#IDProv').on('change', function() {
                setagen();
                setkab($(this).val());
                $('#IDKec').empty().append(`<option value="">Pilih Kecamatan</option>`).trigger('change.select2');
                $('#IDDesa').empty().append(`<option value="">Pilih Desa</option>`).trigger('change.select2');
            });
            $('#IDKab').on('change', function() {
                setagen();
                setkec($('#IDProv').val(), $(this).val());
                $('#IDDesa').empty().append(`<option value="">Pilih Desa</option>`).trigger('change.select2');
            });
            $('#IDKec').on('change', function() {
                setagen();
                setdesa($('#IDProv').val(), $('#IDKab').val(), $(this).val());
            });
            $('#IDDesa').on('change', function() {
                setagen();
            });

            function setkab(prov) {
                $('#IDKab').empty().append(`<option value="">Pilih Kabupaten</option>`);
                if (!prov) {
                    $('#IDKab').trigger('change.select2');
                    return;
                }
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkab/${prov}`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                $('#IDKab').append(
                                    `<option value="${el.IDKab}">${el.NamaKab}</option>`);
                            });
                            $('#IDKab').trigger('change.select2');
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setkec(prov, kab) {
                $('#IDKec').empty().append(`<option value="">Pilih Kecamatan</option>`);
                if (!prov || !kab) {
                    $('#IDKec').trigger('change.select2');
                    return;
                }
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkec/${prov}/${kab}`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                $('#IDKec').append(
                                    `<option value="${el.IDKec}">${el.NamaKec}</option>`);
                            });
                            $('#IDKec').trigger('change.select2');
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

            function setdesa(prov, kab, kec) {
                $('#IDDesa').empty().append(`<option value="">Pilih Desa</option>`);
                if (!prov || !kab || !kec) {
                    $('#IDDesa').trigger('change.select2');
                    return;
                }
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getdesa/${prov}/${kab}/${kec}`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                $('#IDDesa').append(
                                    `<option value="${el.IDDesa}">${el.NamaDesa}</option>`);
                            });
                            $('#IDDesa').trigger('change.select2');
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            }

        });
    </script>
@endpush
