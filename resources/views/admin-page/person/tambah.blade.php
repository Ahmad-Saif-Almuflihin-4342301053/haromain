@extends('layouts.master')

@section('content')
@php
$edit = isset($jamaah->IDPerson);
@endphp

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">

                        <form id="form-default" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="d-flex justify-content-between flex-wrap">

                                @if (Auth::user()->LevelID == 1)
                                    {{-- Perusahaan Travel --}}
                                    <div class="form-group col-md-6">
                                        <label>Perusahaan Travel</label>
                                        <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                            <option value="">Pilih Perusahaan Travel</option>
                                            @foreach ($travel as $row)
                                                <option value="{{ $row->IDTravel }}"
                                                    @selected(old('IDTravel', $jamaah->IDTravel ?? '') == $row->IDTravel)>
                                                    {{ $row->NamaTravel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Cabang (Opsional jika jamaah didaftarkan di cabang) --}}
                                    <div class="form-group col-md-6">
                                        <label>Cabang Travel (Opsional)</label>
                                        <select name="IDCabang" id="IDCabang" class="form-control select2" {{ !$edit || empty($jamaah->IDTravel) ? 'disabled' : '' }}>
                                            <option value="">Pusat (Tanpa Cabang)</option>
                                            @foreach ($cabang as $row)
                                                <option value="{{ $row->IDCabang }}"
                                                    @selected(old('IDCabang', $jamaah->IDCabang ?? '') == $row->IDCabang)>
                                                    {{ $row->NamaCabang }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Biarkan "Pusat" jika dibuat untuk pusat.</small>
                                    </div>
                                @endif

                                {{-- Nama Lengkap --}}
                                <div class="form-group col-md-6">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="NamaJamaah" class="form-control"
                                        placeholder="Masukkan Nama Lengkap"
                                        value="{{ old('NamaJamaah', $jamaah->NamaJamaah ?? '') }}"
                                        {{ $readonly_form ?? '' }} required>
                                </div>

                                {{-- NIK --}}
                                <div class="form-group col-md-6">
                                    <label>NIK</label>
                                    <input type="text" name="NIK" class="form-control" placeholder="Masukkan NIK"
                                        value="{{ old('NIK', $jamaah->NIK ?? '') }}" {{ $readonly_form ?? '' }}>
                                </div>

                                {{-- No Passport --}}
                                <div class="form-group col-md-6">
                                    <label>No Passport</label>
                                    <input type="text" name="NoPasspor" class="form-control"
                                        value="{{ old('NoPasspor', $jamaah->NoPasspor ?? '') }}"
                                        {{ $readonly_form ?? '' }}>
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="form-group col-md-6">
                                    <label>Jenis Kelamin</label>
                                    <select name="JenisKelamin" class="form-control select2 {{ $readonly_form ?? '' }}">
                                        <option value="">Pilih</option>
                                        <option value="L" @selected(($jamaah->JenisKelamin ?? '') === 'L')>Laki-laki</option>
                                        <option value="P" @selected(($jamaah->JenisKelamin ?? '') === 'P')>Perempuan</option>
                                    </select>
                                </div>

                                {{-- No HP --}}
                                <div class="form-group col-md-6">
                                    <label>No HP (Username)</label>
                                    <input type="text" name="NoHP" class="form-control"
                                        value="{{ old('NoHP', $jamaah->NoHP ?? '') }}" {{ $readonly_form ?? '' }}
                                        required>
                                </div>

                                {{-- Foto Profil (hanya create) --}}
                                @if (!$edit)
                                <div class="form-group col-md-6">
                                    <label>Foto Profil</label>
                                    <input type="file" name="FotoProfil" class="file-upload-default"
                                        accept=".png,.jpg,.jpeg">
                                    <div class="input-group">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Foto (jpg/png maks 2MB)">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">
                                                Pilih File
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                @endif

                                {{-- Tempat Lahir --}}
                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="TempatLahir" class="form-control"
                                        value="{{ old('TempatLahir', $jamaah->TempatLahir ?? '') }}"
                                        {{ $readonly_form ?? '' }}>
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="text" name="TanggalLahir" class="form-control datepicker"
                                        value="{{ old('TanggalLahir', isset($jamaah) && $jamaah->TanggalLahir ? datetime_to_date($jamaah->TanggalLahir) : '') }}"
                                        {{ $readonly_form ?? '' }}>
                                </div>


                                {{-- Alamat --}}
                                <div class="form-group col-md-12">
                                    <label>Alamat</label>

                                    <div class="row">
                                        <div class="col-lg-3">
                                            <select name="IDProv" class="form-control select2" id='IDProv' required>
                                                <option value="">Pilih Provinsi</option>
                                                @foreach ($prov ?? [] as $row)
                                                @php
                                                    $val = is_object($row) ? ($row->IDProv ?? '') : ($row['IDProv'] ?? '');
                                                    $text = is_object($row) ? ($row->NamaProv ?? '') : ($row['NamaProv'] ?? '');
                                                @endphp
                                                <option value="{{ $val }}" @selected($val==($jamaah->IDProv ?? null))>
                                                    {{ $text }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <select name="IDKab" class="form-control select2" id="IDKab" required>
                                                <option value="">Pilih Kabupaten</option>
                                                @foreach ($kab ?? [] as $row)
                                                @php
                                                    $val = is_object($row) ? ($row->IDKab ?? '') : ($row['IDKab'] ?? '');
                                                    $text = is_object($row) ? ($row->NamaKab ?? '') : ($row['NamaKab'] ?? '');
                                                @endphp
                                                <option value="{{ $val }}" @selected($val==($jamaah->IDKab ?? null))>
                                                    {{ $text }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <select name="IDKec" class="form-control select2" id="IDKec" required>
                                                <option value="">Pilih Kecamatan</option>
                                                @foreach ($kec ?? [] as $row)
                                                @php
                                                    $val = is_object($row) ? ($row->IDKec ?? '') : ($row['IDKec'] ?? '');
                                                    $text = is_object($row) ? ($row->NamaKec ?? '') : ($row['NamaKec'] ?? '');
                                                @endphp
                                                <option value="{{ $val }}" @selected($val==($jamaah->IDKec ?? null))>
                                                    {{ $text }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <select name="IDDesa" class="form-control select2" id="IDDesa" required>
                                                <option value="">Pilih Desa</option>
                                                @foreach ($desa ?? [] as $row)
                                                @php
                                                    $val = is_object($row) ? ($row->IDDesa ?? '') : ($row['IDDesa'] ?? '');
                                                    $text = is_object($row) ? ($row->NamaDesa ?? '') : ($row['NamaDesa'] ?? '');
                                                @endphp
                                                <option value="{{ $val }}" @selected($val==($jamaah->IDDesa ?? null))>
                                                    {{ $text }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <input type="text" name="Alamat" class="form-control mt-2"
                                        value="{{ old('Alamat', $jamaah->Alamat ?? '') }}">
                                </div>

                                {{-- Email --}}
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="Email" id="email-jamaah" class="form-control"
                                        value="{{ old('Email', $jamaah->Email ?? '') }}" required>
                                </div>

                                {{-- Password --}}
                                <div class="form-group col-md-6">
                                    <label>{{ $edit ? 'Ubah' : '' }} Password</label>
                                    <input type="password" name="Password" class="form-control"
                                        {{ $edit ? '' : 'required' }}>
                                </div>

                            </div>

                            {{-- Hidden --}}
                            <input type="hidden" name="id" value="{{ $jamaah->IDPerson ?? '' }}">
                            <input type="hidden" name="old_FotoProfil" value="{{ $jamaah->FotoProfil ?? '' }}">
                            <input type="hidden" name="old_IDPaket" value="{{ $jamaah->IDPaket ?? '' }}">

                            <div class="text-right">
                                <button type="button" id="btn-batal" class="btn btn-outline-primary mr-2">
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
<script type="module">
    import "https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js";
    import "https://www.gstatic.com/firebasejs/7.16.1/firebase-auth.js";
    import "https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js";
    import "https://www.gstatic.com/firebasejs/7.16.1/firebase-analytics.js";
</script>

<script>
    $(document).ready(function() {
        firebase.initializeApp({
            apiKey: "AIzaSyCOnNZ4zxyNFw7LiI8GVDnLN0xCndsqKi4",
            authDomain: "haromain-afindo.firebaseapp.com",
            databaseURL: "https://haromain-afindo-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "haromain-afindo",
            storageBucket: "haromain-afindo.appspot.com",
            messagingSenderId: "613708238940",
            appId: "1:613708238940:web:30b8f18da0d9c7cfa29699"
        });
        const auth = firebase.auth();
        let doc_index = 0;
        const isEdit = "<?= $edit ?>"

        @if (Auth::user()->LevelID == 1)
        $('#IDTravel').on('change', function() {
            let idTravel = $(this).val();
            let $cabangSelect = $('#IDCabang');
            $cabangSelect.empty();

            if (!idTravel) {
                $cabangSelect.attr('disabled', true);
                $cabangSelect.append('<option value="">Pusat (Tanpa Cabang)</option>');
                return;
            }

            $cabangSelect.removeAttr('disabled');
            $cabangSelect.append('<option value="">Pusat (Tanpa Cabang)</option>');

            $.ajax({
                url: `${baseURL}admin/User/getCabangByTravel/${idTravel}`,
                type: 'GET',
                success: function(res) {
                    if (res && res.length > 0) {
                        res.forEach(function(item) {
                            $cabangSelect.append(`<option value="${item.IDCabang}">${item.NamaCabang}</option>`);
                        });
                    }
                }
            });
        });
        @endif

        // setagen()
        if (isEdit == false) {
            setprov()
        }
        let isSubmit = false;
        $("#btn-batal").on('click', function() {
            if (isSubmit) {
                deleteAuth()
            }
            history.back()
        })
        $("#form-default").submit(function(e) {
            isSubmit = true;
            e.preventDefault()
            if (isEdit == true) {
                submit();
            } else {
                let email = $('#email-jamaah').val()
                auth.createUserWithEmailAndPassword(email, '123456')
                    .then((userCredential) => {
                        const user = userCredential.user;
                        submit()
                    })
                    .catch((error) => {
                        const errorCode = error.code;
                        const errorMessage = error.message;
                        toast('error', 'Gagal menambahkan jamaaah, email tidak valid')
                        deleteAuth()
                    });
            }
        });


        function deleteAuth() {
            var user = firebase.auth().currentUser;
            if (user) {
                user.delete().then(function() {}).catch(function(error) {});
            }
        }

        function submit() {
            const formData = new FormData($("#form-default")[0]);
            $.ajax({
                url: `${baseURL}admin/Person/simpan`,
                type: 'POST',
                enctype: 'multipart/form-data',
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
        }

        function setprov() {
            $("#IDProv").empty().append(`<option value="">Pilih Provinsi</option>`);
            $.ajax({
                url: `${baseURL}admin/Lokasi/getprov`,
                type: 'GET',
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

        function setagen() {
            let prov = $("#IDProv").val();
            let kab = $("#IDKab").val();
            let kec = $("#IDKec").val();
            let desa = $("#IDDesa").val();
            $("#IDPersonAgen").empty().append(`<option value="">Pilih Agen</option>`);
            $.ajax({
                url: `${baseURL}admin/Jamaah/getAgenByLevel?prov=${prov}&kab=${kab}&kec=${kec}&desa=${desa}`,
                type: 'GET',
                success: function(res) {
                    res = typeof res === 'string' ? JSON.parse(res) : res;
                    if (res && res.data) {
                        res.data.forEach(el => {
                            let option = `<option value="${el.IDPerson}">${el.NamaJamaah}</option>`;
                            $("#IDPersonAgen").append(option);
                        });
                        $("#IDPersonAgen").trigger('change.select2');
                    }
                },
                error: function(xhr, status, error) {
                    toast('error', 'Sistem Error');
                }
            });
        }
        $("#IDProv").on('change', function() {
            setagen();
            setkab($(this).val());
            $("#IDKec").empty().append(`<option value="">Pilih Kecamatan</option>`).trigger('change.select2');
            $("#IDDesa").empty().append(`<option value="">Pilih Desa</option>`).trigger('change.select2');
        });
        $("#IDKab").on('change', function() {
            setagen();
            setkec($("#IDProv").val(), $(this).val());
            $("#IDDesa").empty().append(`<option value="">Pilih Desa</option>`).trigger('change.select2');
        });
        $("#IDKec").on('change', function() {
            setagen();
            setdesa($("#IDProv").val(), $("#IDKab").val(), $(this).val());
        });
        $("#IDDesa").on('change', function() {
            setagen();
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

    })
</script>
@endpush