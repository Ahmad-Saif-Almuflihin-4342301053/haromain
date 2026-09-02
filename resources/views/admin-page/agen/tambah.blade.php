@extends('layouts.master')

@section('content')

    @php
        $mutasi = isset($mutasi) && $mutasi;
        $edit = isset($agen?->IDPerson) && !$mutasi;
        $isSuperadmin = Auth::user()->LevelID == 1;
    @endphp

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">{{ $edit ? 'Edit Agen Travel' : ($mutasi ? 'Angkat Jamaah Menjadi Agen' : 'Tambah Agen Travel') }}</h4>

                            @if ($mutasi)
                                <div class="alert alert-info py-2 px-3 mb-4">
                                    <i class="fa fa-info-circle mr-1"></i> Mengangkat jamaah <strong>{{ $agen->NamaJamaah }}</strong> menjadi Agen Travel. Lengkapi data penugasan agen di bawah ini.
                                    <a href="{{ url('admin/Agen/tambah') }}" class="btn btn-sm btn-outline-secondary ml-3">Batal / Tambah Baru</a>
                                </div>
                            @endif

                            {{-- PILIH JAMAAH (UNTUK MUTASI) --}}
                            @if (!$edit && !$mutasi)
                                <div class="card mb-4 bg-light p-3 border">
                                    <h5 class="mb-2">Angkat dari Jamaah yang Sudah Terdaftar</h5>
                                    <p class="text-muted small mb-2">Pilih jamaah dari daftar jika ingin mengangkat jamaah yang sudah ada menjadi agen:</p>
                                    <form id="form-SelectJamaah" action="{{ url('admin/Agen/addfromjamaah') }}" method="get">
                                        <select class="form-control select2" name="id" id="SelectJamaah">
                                            <option value="">-- Pilih Jamaah untuk Diangkat Jadi Agen --</option>
                                            @foreach ($jamaah as $row)
                                                <option value="{{ base64_encode($row->IDPerson) }}">
                                                    {{ $row->NamaJamaah }} ({{ $row->NoHP ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            @endif

                            <form id="form-default" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    {{-- PILIH TRAVEL UNTUK SUPERADMIN --}}
                                    @if ($isSuperadmin)
                                        <div class="form-group col-md-12">
                                            <label>Perusahaan Travel <span class="text-danger">*</span></label>
                                            <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                                <option value="">Pilih Perusahaan Travel</option>
                                                @foreach ($travel as $t)
                                                    <option value="{{ $t->IDTravel }}" @selected(($agen->IDTravel ?? '') == $t->IDTravel)>
                                                        {{ $t->NamaTravel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="form-group col-md-6">
                                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="NamaJamaah" class="form-control" required
                                            value="{{ old('NamaJamaah', $agen->NamaJamaah ?? '') }}"
                                            placeholder="Masukkan Nama Lengkap">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>NIK</label>
                                        <input type="text" name="NIK" class="form-control"
                                            value="{{ old('NIK', $agen->NIK ?? '') }}" placeholder="Masukkan NIK">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>No Passport</label>
                                        <input type="text" name="NoPasspor" class="form-control"
                                            value="{{ old('NoPasspor', $agen->NoPasspor ?? '') }}"
                                            placeholder="Masukkan No Passport">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Jenis Kelamin</label>
                                        <select name="JenisKelamin" class="form-control select2">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" @selected(($agen->JenisKelamin ?? '') === 'L')>Laki-laki</option>
                                            <option value="P" @selected(($agen->JenisKelamin ?? '') === 'P')>Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>No HP (WhatsApp) <span class="text-danger">*</span></label>
                                        <input type="text" name="NoHP" class="form-control" required
                                            value="{{ old('NoHP', $agen->NoHP ?? '') }}" placeholder="Masukkan No HP">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" name="Email" class="form-control" required
                                            value="{{ old('Email', $agen->Email ?? '') }}" placeholder="Masukkan Email">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tempat Lahir</label>
                                        <input type="text" name="TempatLahir" class="form-control"
                                            value="{{ old('TempatLahir', $agen->TempatLahir ?? '') }}" placeholder="Masukkan Tempat Lahir">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Lahir</label>
                                        <input type="text" name="TanggalLahir" class="form-control datepicker"
                                            value="{{ old('TanggalLahir', isset($agen->TanggalLahir) ? datetime_to_date($agen->TanggalLahir) : '') }}" placeholder="YYYY-MM-DD">
                                    </div>

                                    {{-- LEVEL WILAYAH KEAGENAN --}}
                                    <div class="form-group col-md-6">
                                        <label>Level Wilayah Penugasan Agen <span class="text-danger">*</span></label>
                                        <select name="LevelWilayah" id="LevelWilayah" class="form-control select2" required>
                                            <option value="">Pilih Level Wilayah</option>
                                            <option value="NASIONAL" @selected(($agen->LevelWilayah ?? '') === 'NASIONAL')>Nasional</option>
                                            <option value="Provinsi" @selected(($agen->LevelWilayah ?? '') === 'Provinsi')>Provinsi</option>
                                            <option value="Kabupaten" @selected(($agen->LevelWilayah ?? '') === 'Kabupaten')>Kabupaten</option>
                                            <option value="Kecamatan" @selected(($agen->LevelWilayah ?? '') === 'Kecamatan')>Kecamatan</option>
                                            <option value="Desa" @selected(($agen->LevelWilayah ?? '') === 'Desa')>Desa</option>
                                        </select>
                                    </div>

                                    {{-- ALAMAT / WILAYAH --}}
                                    <div class="form-group col-md-6" id="select-prov">
                                        <label>Provinsi</label>
                                        <select name="IDProv" class="form-control select2" id="IDProv">
                                            <option value="">Pilih Provinsi</option>
                                            @if (isset($prov))
                                                @foreach ($prov as $row)
                                                    @php
                                                        $val = is_object($row) ? ($row->IDProv ?? '') : ($row['IDProv'] ?? '');
                                                        $text = is_object($row) ? ($row->NamaProv ?? '') : ($row['NamaProv'] ?? '');
                                                    @endphp
                                                    <option value="{{ $val }}" @selected(($agen->IDProv ?? '') == $val)>
                                                        {{ $text }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6" id="select-kab">
                                        <label>Kabupaten / Kota</label>
                                        <select name="IDKab" class="form-control select2" id="IDKab">
                                            <option value="">Pilih Kabupaten</option>
                                            @if (isset($kab))
                                                @foreach ($kab as $row)
                                                    @php
                                                        $val = is_object($row) ? ($row->IDKab ?? '') : ($row['IDKab'] ?? '');
                                                        $text = is_object($row) ? ($row->NamaKab ?? '') : ($row['NamaKab'] ?? '');
                                                    @endphp
                                                    <option value="{{ $val }}" @selected(($agen->IDKab ?? '') == $val)>
                                                        {{ $text }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6" id="select-kec">
                                        <label>Kecamatan</label>
                                        <select name="IDKec" class="form-control select2" id="IDKec">
                                            <option value="">Pilih Kecamatan</option>
                                            @if (isset($kec))
                                                @foreach ($kec as $row)
                                                    @php
                                                        $val = is_object($row) ? ($row->IDKec ?? '') : ($row['IDKec'] ?? '');
                                                        $text = is_object($row) ? ($row->NamaKec ?? '') : ($row['NamaKec'] ?? '');
                                                    @endphp
                                                    <option value="{{ $val }}" @selected(($agen->IDKec ?? '') == $val)>
                                                        {{ $text }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6" id="select-des">
                                        <label>Desa / Kelurahan</label>
                                        <select name="IDDesa" class="form-control select2" id="IDDesa">
                                            <option value="">Pilih Desa</option>
                                            @if (isset($desa))
                                                @foreach ($desa as $row)
                                                    @php
                                                        $val = is_object($row) ? ($row->IDDesa ?? '') : ($row['IDDesa'] ?? '');
                                                        $text = is_object($row) ? ($row->NamaDesa ?? '') : ($row['NamaDesa'] ?? '');
                                                    @endphp
                                                    <option value="{{ $val }}" @selected(($agen->IDDesa ?? '') == $val)>
                                                        {{ $text }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Alamat Lengkap</label>
                                        <input type="text" name="Alamat" class="form-control"
                                            value="{{ old('Alamat', $agen->Alamat ?? '') }}" placeholder="Masukkan Alamat Lengkap">
                                    </div>

                                    {{-- FOTO PROFIL --}}
                                    <div class="form-group col-md-6">
                                        <label>
                                            Foto Profil
                                            @if (!empty($agen->FotoProfil))
                                                <a href="{{ asset('assets/foto-profil/' . $agen->FotoProfil) }}"
                                                    target="_blank">
                                                    | Lihat Foto
                                                </a>
                                            @endif
                                        </label>

                                        <input type="file" name="FotoProfil" class="file-upload-default"
                                            accept=".jpg,.jpeg,.png">

                                        <div class="input-group">
                                            <input type="text" class="form-control file-upload-info" disabled
                                                placeholder="Upload Foto (jpg/png maks 2MB)">
                                            <span class="input-group-append">
                                                <button type="button" class="file-upload-browse btn btn-primary">
                                                    Pilih File
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- DOKUMEN MOU --}}
                                    <div class="form-group col-md-6">
                                        <label>
                                            Dokumen MoU (Perjanjian Agen)
                                            @if (!empty($agen->DokMoU))
                                                <a href="{{ asset('assets/dokumen-mou/' . $agen->DokMoU) }}"
                                                    target="_blank">
                                                    | Lihat Dokumen
                                                </a>
                                            @endif
                                        </label>

                                        <input type="file" name="DokMoU" class="file-upload-default" accept=".pdf">

                                        <div class="input-group">
                                            <input type="text" class="form-control file-upload-info" disabled
                                                placeholder="Upload MoU (PDF maks 2MB)">
                                            <span class="input-group-append">
                                                <button type="button" class="file-upload-browse btn btn-primary">
                                                    Pilih File
                                                </button>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- PASSWORD --}}
                                    @if (!$mutasi)
                                        <div class="form-group col-md-6">
                                            <label>{{ $edit ? 'Ubah' : '' }} Password {!! $edit ? '(Kosongkan jika tidak diubah)' : '<span class="text-danger">*</span>' !!}</label>
                                            <input type="password" name="Password" class="form-control"
                                                autocomplete="new-password" {{ $edit ? '' : 'required' }}>
                                        </div>
                                    @endif

                                </div>

                                <div class="text-right mt-3">
                                    <input type="hidden" name="id" value="{{ $agen->IDPerson ?? '' }}">
                                    <input type="hidden" name="old_FotoProfil" value="{{ $agen->FotoProfil ?? '' }}">
                                    <input type="hidden" name="old_DokMoU" value="{{ $agen->DokMoU ?? '' }}">
                                    <input type="hidden" name="mutasi" value="{{ $mutasi ?? '' }}">

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
            const baseURL = "{{ url('/') }}/";
            const isEdit = {{ $edit ? 'true' : 'false' }};
            const isSuperadmin = {{ $isSuperadmin ? 'true' : 'false' }};

            if (!isEdit && !$('#IDProv option:selected').val()) {
                setprov();
            }

            // Handle level wilayah toggle
            function updateLevelWilayahUI() {
                let val = $('#LevelWilayah').val();
                if (val === 'NASIONAL' || val === '') {
                    $('#select-prov, #select-kab, #select-kec, #select-des').hide();
                } else if (val === 'Provinsi') {
                    $('#select-prov').show();
                    $('#select-kab, #select-kec, #select-des').hide();
                } else if (val === 'Kabupaten') {
                    $('#select-prov, #select-kab').show();
                    $('#select-kec, #select-des').hide();
                } else if (val === 'Kecamatan') {
                    $('#select-prov, #select-kab, #select-kec').show();
                    $('#select-des').hide();
                } else if (val === 'Desa') {
                    $('#select-prov, #select-kab, #select-kec, #select-des').show();
                }
            }

            $('#LevelWilayah').on('change', function() {
                updateLevelWilayahUI();
            });

            // Initial UI check on page load
            if ($('#LevelWilayah').val()) {
                updateLevelWilayahUI();
            }

            $("#form-default").submit(function(e) {
                e.preventDefault();
                const formData = new FormData($("#form-default")[0]);
                $.ajax({
                    url: `${baseURL}admin/Agen/simpan`,
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
                            toast('success', res.message);
                            setTimeout(() => {
                                window.location.href = res.redirect;
                            }, 500);
                        } else {
                            toast('error', res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            });

            function setprov() {
                $("#IDProv").empty().append(`<option value="">Pilih Provinsi</option>`);
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getprov`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                let option = `<option value="${el.IDProv}">${el.NamaProv}</option>`;
                                $("#IDProv").append(option);
                            });
                            $("#IDProv").trigger('change.select2');
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }

            $("#IDProv").on('change', function() {
                setkab($(this).val());
                $("#IDKec").empty().append(`<option value="">Pilih Kecamatan</option>`).trigger('change.select2');
                $("#IDDesa").empty().append(`<option value="">Pilih Desa</option>`).trigger('change.select2');
            });

            $("#IDKab").on('change', function() {
                setkec($("#IDProv").val(), $(this).val());
                $("#IDDesa").empty().append(`<option value="">Pilih Desa</option>`).trigger('change.select2');
            });

            $("#IDKec").on('change', function() {
                setdesa($("#IDProv").val(), $("#IDKab").val(), $(this).val());
            });

            function setkab(prov) {
                $("#IDKab").empty().append(`<option value="">Pilih Kabupaten</option>`);
                if (!prov) {
                    $("#IDKab").trigger('change.select2');
                    return;
                }
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkab/${prov}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                let option = `<option value="${el.IDKab}">${el.NamaKab}</option>`;
                                $("#IDKab").append(option);
                            });
                            $("#IDKab").trigger('change.select2');
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }

            function setkec(prov, kab) {
                $("#IDKec").empty().append(`<option value="">Pilih Kecamatan</option>`);
                if (!prov || !kab) {
                    $("#IDKec").trigger('change.select2');
                    return;
                }
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getkec/${prov}/${kab}`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                let option = `<option value="${el.IDKec}">${el.NamaKec}</option>`;
                                $("#IDKec").append(option);
                            });
                            $("#IDKec").trigger('change.select2');
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }

            function setdesa(prov, kab, kec) {
                $("#IDDesa").empty().append(`<option value="">Pilih Desa</option>`);
                if (!prov || !kab || !kec) {
                    $("#IDDesa").trigger('change.select2');
                    return;
                }
                $.ajax({
                    url: `${baseURL}admin/Lokasi/getdesa/${prov}/${kab}/${kec}`,
                    type: 'GET',
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res && res.data) {
                            res.data.forEach(el => {
                                let option = `<option value="${el.IDDesa}">${el.NamaDesa}</option>`;
                                $("#IDDesa").append(option);
                            });
                            $("#IDDesa").trigger('change.select2');
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error');
                    }
                });
            }

            $("#SelectJamaah").on('change', function() {
                let val = $(this).val();
                if (val) {
                    $("#form-SelectJamaah").submit();
                }
            });

            // Superadmin Travel change -> reload Mutasi Jamaah options
            if (isSuperadmin) {
                $('#IDTravel').on('change', function() {
                    let travelId = $(this).val();
                    if (!travelId) return;
                    $.ajax({
                        url: `${baseURL}admin/Agen/getjamaahbytravel?id_travel=${travelId}`,
                        type: 'GET',
                        success: function(res) {
                            res = typeof res === 'string' ? JSON.parse(res) : res;
                            if (res && res.data) {
                                $('#SelectJamaah').empty().append('<option value="">-- Pilih Jamaah untuk Dimutasi --</option>');
                                res.data.forEach(j => {
                                    $('#SelectJamaah').append(`<option value="${btoa(j.IDPerson)}">${j.NamaJamaah}</option>`);
                                });
                                $('#SelectJamaah').trigger('change.select2');
                            }
                        }
                    });
                });
            }

        });
    </script>
@endpush
