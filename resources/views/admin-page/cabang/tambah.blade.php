@extends('layouts.master')

@section('content')

    @php
        $edit = isset($cabang) && isset($cabang->IDCabang);
    @endphp

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">

                            <form id="form-default" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="d-flex justify-content-between flex-wrap">

                                    @if(Auth::user()->LevelID == 1)
                                        <div class="form-group col-md-12">
                                            <label>Travel <span class="text-danger">*</span></label>
                                            <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                                <option value="">Pilih Travel</option>
                                                @isset($travel)
                                                    @foreach($travel as $row)
                                                        <option value="{{ $row->IDTravel }}"
                                                            {{ ($cabang->IDTravel ?? '') == $row->IDTravel ? 'selected' : '' }}>
                                                            {{ $row->NamaTravel }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                    @endif

                                    <div class="form-group col-md-6">
                                        <label>Nama Cabang</label>
                                        <input type="text" name="NamaCabang" class="form-control"
                                            placeholder="Masukkan Nama Cabang" required
                                            value="{{ old('NamaCabang', $cabang->NamaCabang ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>
                                            Dokumen Pembukaan Cabang
                                            @if (!empty($cabang->DokPembukaanCabang))
                                                <a href="{{ asset('assets/dokumen-cabang/' . $cabang->DokPembukaanCabang) }}"
                                                    target="_blank">
                                                    | Lihat Dokumen
                                                </a>
                                            @endif
                                        </label>

                                        <input type="file" name="DokPembukaanCabang" id="DokPembukaanCabang"
                                            class="file-upload-default" accept=".pdf">

                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled
                                                placeholder="Upload Dokumen (PDF maks 2MB)">
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-primary" type="button">
                                                    Pilih File
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Nama Pimpinan Cabang</label>
                                        <input type="text" name="NamaPimpinanCabang" class="form-control"
                                            placeholder="Masukkan Nama Pimpinan Cabang"
                                            value="{{ old('NamaPimpinanCabang', $cabang->NamaPimpinanCabang ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>No Telepon Pimpinan Cabang</label>
                                        <input type="text" name="NoHPPimp" class="form-control"
                                            placeholder="Masukkan No Telp Pimpinan Cabang"
                                            value="{{ old('NoHPPimp', $cabang->NoHPPimp ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Nama CP</label>
                                        <input type="text" name="NamaCP" class="form-control"
                                            placeholder="Masukkan Nama CP"
                                            value="{{ old('NamaCP', $cabang->NamaCP ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>No Telepon CP</label>
                                        <input type="text" name="TelpCP" class="form-control"
                                            placeholder="Masukkan No Telepon CP"
                                            value="{{ old('TelpCP', $cabang->TelpCP ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Berdiri</label>
                                        <input type="text" name="TglAwalBerdiri" class="form-control datepicker"
                                            placeholder="Masukkan Tanggal Berdiri"
                                            value="{{ old('TglAwalBerdiri', $cabang->TglAwalBerdiri ?? '') }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Akhir Cabang</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ ($cabang->TglAkhirCabang ?? '') == '0000-00-00' ? '-' : $cabang->TglAkhirCabang ?? '-' }}">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Level Wilayah</label>
                                        <div class="row">

                                            {{-- Prov --}}
                                            <div class="col-lg-3">
                                                <select name="IDProv" id="IDProv" class="form-control select2" required>
                                                    <option value="">Pilih Provinsi</option>
                                                    @isset($prov)
                                                        @foreach ($prov as $row)
                                                            <option value="{{ $row->IDProv }}"
                                                                {{ ($cabang->IDProv ?? '') == $row->IDProv ? 'selected' : '' }}>
                                                                {{ $row->NamaProv }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>

                                            {{-- Kab --}}
                                            <div class="col-lg-3">
                                                <select name="IDKab" id="IDKab" class="form-control select2">
                                                    <option value="">Pilih Kabupaten</option>
                                                    @isset($kab)
                                                        @foreach ($kab as $row)
                                                            <option value="{{ $row->IDKab }}"
                                                                {{ ($cabang->IDKab ?? '') == $row->IDKab ? 'selected' : '' }}>
                                                                {{ $row->NamaKab }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>

                                            {{-- Kec --}}
                                            <div class="col-lg-3">
                                                <select name="IDKec" id="IDKec" class="form-control select2">
                                                    <option value="">Pilih Kecamatan</option>
                                                    @isset($kec)
                                                        @foreach ($kec as $row)
                                                            <option value="{{ $row->IDKec }}"
                                                                {{ ($cabang->IDKec ?? '') == $row->IDKec ? 'selected' : '' }}>
                                                                {{ $row->NamaKec }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>

                                            {{-- Desa --}}
                                            <div class="col-lg-3">
                                                <select name="IDDesa" id="IDDesa" class="form-control select2">
                                                    <option value="">Pilih Desa</option>
                                                    @isset($desa)
                                                        @foreach ($desa as $row)
                                                            <option value="{{ $row->IDDesa }}"
                                                                {{ ($cabang->IDDesa ?? '') == $row->IDDesa ? 'selected' : '' }}>
                                                                {{ $row->NamaDesa }}
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- ACTION --}}
                                <div class="text-right">
                                    <input type="hidden" name="id" value="{{ $cabang->IDCabang ?? '' }}">
                                    <input type="hidden" name="old_DokPembukaanCabang"
                                        value="{{ $cabang->DokPembukaanCabang ?? '' }}">

                                    <button type="button" onclick="history.back()" class="btn btn-outline-primary mr-2">
                                        Batal
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        Simpan
                                    </button>
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
            const isEdit = "{{ $edit }}"
            if (isEdit == false) {
                setprov()
            }

            $("#form-default").submit(function(e) {
                e.preventDefault()
                const formData = new FormData($("#form-default")[0]);
                $.ajax({
                    url: `${baseURL}admin/Cabang/simpan`,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(res) {

                        if (res.status) {
                            window.location.href = res.redirect
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

            function setprov() {
                $("#IDProv").empty()
                $("#IDProv").append(`<option value="">Pilih Provinsi</option>`)
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getprov`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option = `<option value="${el.IDProv}">${el.NamaProv}</option>`;
                            $("#IDProv").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            }
            $("#IDProv").on('change', function() {
                setkab($(this).val());
                $("#IDKec").empty()
                $("#IDDesa").empty()
                $("#IDKec").append(`<option value="">Pilih Kecamatan</option>`)
                $("#IDDesa").append(`<option value="">Pilih Desa</option>`)
            })
            $("#IDKab").on('change', function() {
                setkec($("#IDProv").val(), $(this).val());
                $("#IDDesa").empty()
                $("#IDDesa").append(`<option value="">Pilih Desa</option>`)
            })
            $("#IDKec").on('change', function() {
                setdesa($("#IDProv").val(), $("#IDKab").val(), $(this).val());
            })

            function setkab(prov) {
                $("#IDKab").empty()
                $("#IDKab").append(`<option value="">Pilih Kabupaten</option>`)
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkab/${prov}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option = `<option value="${el.IDKab}">${el.NamaKab}</option>`;
                            $("#IDKab").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });

            }

            function setkec(prov, kab) {
                $("#IDKec").empty()
                $("#IDKec").append(`<option value="">Pilih Kecamatan</option>`)
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkec/${prov}/${kab}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option = `<option value="${el.IDKec}">${el.NamaKec}</option>`;
                            $("#IDKec").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });

            }

            function setdesa(prov, kab, kec) {
                $("#IDDesa").empty()
                $("#IDDesa").append(`<option value="">Pilih Desa</option>`)
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getdesa/${prov}/${kab}/${kec}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option = `<option value="${el.IDDesa}">${el.NamaDesa}</option>`;
                            $("#IDDesa").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });

            }

        })
    </script>
@endpush
