@extends('layouts.master')

@section('content')

@php
    $edit = isset($userlogin?->UserName);
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

                                <div class="form-group col-md-6">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="NamaLengkap"
                                           class="form-control"
                                           value="{{ $userlogin->NamaLengkap ?? '' }}"
                                           placeholder="Masukkan Nama Lengkap"
                                           required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="Email"
                                           class="form-control"
                                           value="{{ $userlogin->Email ?? '' }}"
                                           placeholder="Masukkan Email"
                                           required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Alamat</label>
                                    <input type="text" name="Alamat"
                                           class="form-control"
                                           value="{{ $userlogin->Alamat ?? '' }}"
                                           placeholder="Masukkan Alamat">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Jenis Kelamin</label>
                                    <select name="JenisKelamin" id="JenisKelamin" class="form-control select2">
                                        <option value="">Pilih</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>No HP</label>
                                    <input type="text" name="HPNo"
                                           class="form-control"
                                           value="{{ $userlogin->HPNo ?? '' }}"
                                           placeholder="Masukkan No HP">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Foto Profile</label>
                                    <input type="file" name="Foto" id="Foto"
                                           class="file-upload-default"
                                           accept=".png,.jpg,.jpeg">

                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info"
                                               disabled placeholder="Upload foto (.jpg, .png)">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">
                                                Pilih Gambar
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="TempatLahir"
                                           class="form-control"
                                           value="{{ $userlogin->TempatLahir ?? '' }}"
                                           placeholder="Masukkan Tempat Lahir">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="text" name="TanggalLahir"
                                           class="form-control datepicker"
                                           value="{{ $userlogin->TanggalLahir ?? '' }}"
                                           placeholder="Masukkan Tanggal Lahir">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Username</label>
                                    <input type="text" name="UserName"
                                           class="form-control"
                                           value="{{ $userlogin->UserName ?? '' }}"
                                           placeholder="Masukkan Username"
                                           {{ $edit ? 'readonly' : 'required' }}>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>{{ $edit ? 'Ubah' : '' }} Password</label>
                                    <input type="password" name="UserPsw"
                                           class="form-control"
                                           autocomplete="new-password"
                                           placeholder="Masukkan Password"
                                           {{ $edit ? '' : 'required' }}>
                                </div>

                                @if(Auth::user()->LevelID == 1)
                                    <div class="form-group col-md-6">
                                        <label>Travel</label>
                                        <select name="IDTravel" id="IDTravel" class="form-control select2">
                                            <option value="">Pilih Travel (Kosongkan jika Superadmin)</option>
                                            @foreach($travel as $row)
                                                <option value="{{ $row->IDTravel }}"
                                                    {{ $row->IDTravel == ($userlogin->IDTravel ?? '') ? 'selected' : '' }}>
                                                    {{ $row->NamaTravel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Cabang</label>
                                        <select name="IDCabang" id="IDCabang" class="form-control select2" disabled>
                                            <option value="">Pilih Travel Terlebih Dahulu</option>
                                        </select>
                                        <small class="text-muted">Pilih cabang jika user didaftarkan sebagai Admin Cabang. Kosongkan jika Admin Pusat.</small>
                                    </div>
                                @elseif(Auth::user()->LevelID == 2)
                                    <input type="hidden" name="IDTravel" id="IDTravel" value="{{ Auth::user()->IDTravel }}">
                                    @if(!empty(Auth::user()->IDCabang))
                                        <input type="hidden" name="IDCabang" id="IDCabang" value="{{ Auth::user()->IDCabang }}">
                                        <div class="form-group col-md-6">
                                            <label>Cabang</label>
                                            <input type="text" class="form-control" value="{{ $namaCabangUser ?? 'Cabang Anda' }}" readonly>
                                            <small class="text-muted">User otomatis terdaftar pada cabang Anda.</small>
                                        </div>
                                    @else
                                        <div class="form-group col-md-6">
                                            <label>Cabang</label>
                                            <select name="IDCabang" id="IDCabang" class="form-control select2">
                                                <option value="">Pusat / Bukan Admin Cabang</option>
                                                @isset($cabang)
                                                    @foreach($cabang as $c)
                                                        <option value="{{ $c->IDCabang }}"
                                                            {{ $c->IDCabang == ($userlogin->IDCabang ?? '') ? 'selected' : '' }}>
                                                            {{ $c->NamaCabang }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            <small class="text-muted">Pilih cabang jika user didaftarkan sebagai Admin Cabang. Kosongkan jika Admin Pusat.</small>
                                        </div>
                                    @endif
                                @endif

                            </div>

                            <div class="text-right">
                                <input type="hidden" name="id" value="{{ $userlogin->UserName ?? '' }}">
                                <input type="hidden" name="old_Foto" value="{{ $userlogin->Foto ?? '' }}">

                                <button type="button" onclick="history.back()" class="btn btn-outline-primary mr-2">
                                    Batal
                                </button>
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
    $(document).ready(function () {

        $('#JenisKelamin').val("{{ $userlogin->JenisKelamin ?? '' }}");

        const userLevel = {{ Auth::user()->LevelID }};
        const initialTravel = "{{ $userlogin->IDTravel ?? '' }}";
        const initialCabang = "{{ $userlogin->IDCabang ?? '' }}";

        function loadCabang(idTravel, selectedCabang = '') {
            if (!idTravel) {
                $('#IDCabang').empty().append('<option value="">Pilih Travel Terlebih Dahulu</option>').prop('disabled', true);
                return;
            }

            $('#IDCabang').empty().append('<option value="">Memuat cabang...</option>').prop('disabled', true);

            $.ajax({
                url: `${baseURL}admin/User/getCabangByTravel/${idTravel}`,
                type: "GET",
                success: function (res) {
                    $('#IDCabang').empty().append('<option value="">Pusat / Bukan Admin Cabang</option>');
                    if (res.status && res.data.length > 0) {
                        res.data.forEach(function (c) {
                            let isSelected = (c.IDCabang === selectedCabang) ? 'selected' : '';
                            $('#IDCabang').append(`<option value="${c.IDCabang}" ${isSelected}>${c.NamaCabang}</option>`);
                        });
                    }
                    $('#IDCabang').prop('disabled', false);
                },
                error: function () {
                    $('#IDCabang').empty().append('<option value="">Gagal memuat cabang</option>').prop('disabled', true);
                }
            });
        }

        if (userLevel === 1) {
            if (initialTravel) {
                loadCabang(initialTravel, initialCabang);
            }

            $('#IDTravel').on('change', function () {
                const selectedTravel = $(this).val();
                loadCabang(selectedTravel);
            });
        }

        $('#form-default').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.user.simpan') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.status) {
                        window.location.href = res.redirect;
                        toast('success', res.message);
                    } else {
                        toast('error', res.message);
                    }
                },
                error: function () {
                    toast('error', 'Sistem Error');
                }
            });
        });

    });
</script>

@endpush
