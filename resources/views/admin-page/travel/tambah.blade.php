@extends('layouts.master')

@section('content')

@php
$edit = isset($travel->IDTravel) ? true : false;
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
                                    <label for="">Tanggal Register</label>
                                    <input type="text" class="form-control"
                                        value="{{ $travel->TglRegister ?? date('Y-m-d') }}" required readonly>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Nama Travel</label>
                                    <input type="text" name="NamaTravel" placeholder="Masukkan Nama Travel"
                                        class="form-control" value="{{ old('NamaTravel', $travel->NamaTravel ?? '') }}"
                                        required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">No PPIU</label>
                                    <input type="text" name="NoPPIU" placeholder="Masukkan Nomor PPIU"
                                        class="form-control" value="{{ old('NoPPIU', $travel->NoPPIU ?? '') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Tanggal Berlaku PPIU</label>
                                    <input type="text" name="TglBerlakuPPIU"
                                        value="{{ old('TglBerlakuPPIU', $travel->TglBerlakuPPIU ?? '') }}"
                                        placeholder="Masukkan Tanggal Berlaku PPIU" class="form-control datepicker">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Akreditasi</label>
                                    <input type="text" name="Akreditasi" placeholder="Masukkan Akreditasi"
                                        class="form-control" value="{{ old('Akreditasi', $travel->Akreditasi ?? '') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Logo Kantor</label>
                                    <input type="file" name="LogoKantor" id="LogoKantor" class="file-upload-default"
                                        accept=".png, .jpg, .jpeg">
                                    <div class="input-group col-xs-12">
                                        <input type="text" name="LogoKantor" class="form-control file-upload-info"
                                             disabled placeholder="Upload foto (.jpg, .png)">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button"> Pilih
                                                Gambar </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Nama Pemilik</label>
                                    <input type="text" name="NamaPemilik" placeholder="Masukkan Nama Pemilik"
                                        class="form-control"
                                        value="{{ old('NamaPemilik', $travel->NamaPemilik ?? '') }}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">No. Telepon Pemilik</label>
                                    <input type="text" name="NoTelpPemilik" placeholder="Masukkan No Telepon Pemilik"
                                        class="form-control"
                                        value="{{ old('NoTelpPemilik', $travel->NoTelpPemilik ?? '') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">Nama CP</label>
                                    <input type="text" name="NamaCP" placeholder="Masukkan Nama CP"
                                        class="form-control" value="{{ old('NamaCP', $travel->NamaCP ?? '') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">No. Telepon CP</label>
                                    <input type="text" name="TelpCP" placeholder="Masukkan No Telepon CP"
                                        class="form-control" value="{{ old('TelpCP', $travel->TelpCP ?? '') }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="">Alamat</label>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <select name="IDProv" id="IDProv" class="form-control select2" required>
                                                <option value="">Pilih Provinsi</option>
                                                @if (isset($prov))
                                                    @foreach ($prov as $row)
                                                        <option value="{{ $row->IDProv }}"
                                                            {{ ($row->IDProv ?? '') == ($travel->IDProv ?? '') ? 'selected' : '' }}>
                                                            {{ $row->NamaProv }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <select name="IDKab" id="IDKab" class="form-control select2">
                                                <option value="">Pilih Kabupaten</option>
                                                @if (isset($kab))
                                                    @foreach ($kab as $row)
                                                        <option value="{{ $row->IDKab }}"
                                                            {{ ($row->IDKab ?? '') == ($travel->IDKab ?? '') ? 'selected' : '' }}>
                                                            {{ $row->NamaKab }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <select name="IDKec" id="IDKec" class="form-control select2">
                                                <option value="">Pilih Kecamatan</option>
                                                @if (isset($kec))
                                                    @foreach ($kec as $row)
                                                        <option value="{{ $row->IDKec }}"
                                                            {{ ($row->IDKec ?? '') == ($travel->IDKec ?? '') ? 'selected' : '' }}>
                                                            {{ $row->NamaKec }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-lg-3">
                                            <select name="IDDesa" id="IDDesa" class="form-control select2">
                                                <option value="">Pilih Desa</option>
                                                @if (isset($desa))
                                                    @foreach ($desa as $row)
                                                        <option value="{{ $row->IDDesa }}"
                                                            {{ ($row->IDDesa ?? '') == ($travel->IDDesa ?? '') ? 'selected' : '' }}>
                                                            {{ $row->NamaDesa }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <input type="text" name="Alamat" placeholder="Masukkan Alamat Travel"
                                        class="form-control mt-2" value="{{ old('Alamat', $travel->Alamat ?? '') }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Dokumen Legalitas Travel <span class="text-small">(File PDF maks
                                            2MB)</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-sm mt-3 table-bordered border" id="tabel-dokumen">
                                            <thead>
                                                <tr>
                                                    <th>Nama Dokumen</th>
                                                    <th>Tanggal Dokumen</th>
                                                    <th>Masa Aktif Dokumen</th>
                                                    <th>Upload Dokumen</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-dokumen"></tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5">
                                                        <button type="button" id="btn-add-dokumen"
                                                            class="btn btn-block btn-github btn-sm">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <input type="hidden" name="id" value="{{ $travel->IDTravel ?? '' }}">
                                <input type="hidden" name="old_LogoKantor" value="{{ $travel->LogoKantor ?? '' }}">
                                <div id="deleted_FileDokumen"></div>
                                <button type="button" onclick="history.back()"
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
        const isEdit = @json($edit);
        if (!isEdit) {
            setprov();
        } else {
            initDokumen();
        }

        $("#form-default").submit(function(e) {
            e.preventDefault();
            const formData = new FormData($("#form-default")[0]);
            $.ajax({
                url: "{{ url('admin/Travel/simpan') }}",
                type: 'POST',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: formData,
                success: function(res) {

                    if (res.status) {
                        window.location.href = res.redirect;
                        toast('success', res.message);
                    } else {
                        toast('error', res.message);
                    }
                },
                error: function(xhr, status, error) {
                    toast('error', 'Sistem Error');
                }
            });
        });

        $("#IDProv").on('change', function() {
            setkab($(this).val());
            $("#IDKec, #IDDesa").empty().append('<option value="">Pilih Kecamatan/Desa</option>');
        });
        $("#IDKab").on('change', function() {
            setkec($("#IDProv").val(), $(this).val());
            $("#IDDesa").empty().append('<option value="">Pilih Desa</option>');
        });
        $("#IDKec").on('change', function() {
            setdesa($("#IDProv").val(), $("#IDKab").val(), $(this).val());
        });

        function initDokumen() {
            let dokumentravel = @json($dokumentravel ?? []);
            dokumentravel.forEach(el => {
                let insertrow = `<tr id="dokumen-${doc_index}">
                <td>
                    <input type="text" value="${el.NamaDokumen}" name="NamaDokumen[${doc_index}]" class="form-control" placeholder="Masukkan Nama Dokumen" required>
                </td>
                <td>
                    <input type="date" value="${el.TglDokumen}" name="TglDokumen[${doc_index}]" class="form-control">
                </td>
                <td>
                    <input type="date" value="${el.MasaAktifSampai}" name="MasaAktifSampai[${doc_index}]" class="form-control">
                </td>
                <td>
                    <input type="file" name="FileDokumen[${doc_index}]" class="form-control" accept="application/pdf">
                    <input type="hidden" name="IDDokumen[${doc_index}]" value="${el.IDDokumen}">
                    <input type="hidden" name="old_FileDokumen[${doc_index}]" value="${el.FileDokumen}">
                </td>
                <td>
                    <input type="hidden" name="doc_index" value="${doc_index}">
                    <div class="d-flex">
                        <button type="button" class="btn btn-inverse-danger btn-sm pt-0 mr-2 del-dokumen" data-id="${el.IDDokumen}" data-index="${doc_index}">
                            <i class="fa fa-trash"></i>
                        </button>
                        <a href="{{ asset('assets/dokumen-travel') }}/${el.FileDokumen}" class="btn btn-inverse-info btn-sm pt-0" data-index="${doc_index}">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                </td>
            </tr>`;
                $("#tbody-dokumen").append(insertrow);
                doc_index++;
            });
        }

        function setprov() {
            $("#IDProv").empty().append('<option value="">Pilih Provinsi</option>');
            $.ajax({
                url: "{{ url('admin/Lokasi/getprov') }}",
                type: 'GET',
                success: async function(res) {

                    res.data.forEach(el => {
                        $("#IDProv").append(
                            `<option value="${el.IDProv}">${el.NamaProv}</option>`);
                    });
                },
                error: function() {
                    toast('error', 'Sistem Error');
                }
            });
        }

        function setkab(prov) {
            $("#IDKab").empty().append('<option value="">Pilih Kabupaten</option>');
            $.ajax({
                url: "{{ url('admin/Lokasi/getkab') }}/" + prov,
                type: 'GET',
                success: function(res) {

                    res.data.forEach(el => {
                        $("#IDKab").append(
                            `<option value="${el.IDKab}">${el.NamaKab}</option>`);
                    });
                },
                error: function() {
                    toast('error', 'Sistem Error');
                }
            });
        }

        function setkec(prov, kab) {
            $("#IDKec").empty().append('<option value="">Pilih Kecamatan</option>');
            $.ajax({
                url: "{{ url('admin/Lokasi/getkec') }}/" + prov + "/" + kab,
                type: 'GET',
                success: function(res) {

                    res.data.forEach(el => {
                        $("#IDKec").append(
                            `<option value="${el.IDKec}">${el.NamaKec}</option>`);
                    });
                },
                error: function() {
                    toast('error', 'Sistem Error');
                }
            });
        }

        function setdesa(prov, kab, kec) {
            $("#IDDesa").empty().append('<option value="">Pilih Desa</option>');
            $.ajax({
                url: "{{ url('admin/Lokasi/getdesa') }}/" + prov + "/" + kab + "/" + kec,
                type: 'GET',
                success: function(res) {

                    res.data.forEach(el => {
                        $("#IDDesa").append(
                            `<option value="${el.IDDesa}">${el.NamaDesa}</option>`);
                    });
                },
                error: function() {
                    toast('error', 'Sistem Error');
                }
            });
        }

        $("#btn-add-dokumen").on('click', function() {
            let insertrow = `<tr id="dokumen-${doc_index}">
            <td>
                <input type="text" name="NamaDokumen[${doc_index}]" class="form-control" placeholder="Masukkan Nama Dokumen" required>
            </td>
            <td>
                <input type="date" name="TglDokumen[${doc_index}]" class="form-control">
            </td>
            <td>
                <input type="date" name="MasaAktifSampai[${doc_index}]" class="form-control">
            </td>
            <td>
                <input type="file" name="FileDokumen[${doc_index}]" class="form-control" accept="application/pdf" required>
            </td>
            <td>
                <input type="hidden" name="doc_index" value="${doc_index}">
                <button type="button" class="btn btn-inverse-danger del-dokumen" data-index="${doc_index}">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
            $("#tbody-dokumen").append(insertrow);
            doc_index++;
        });

        $("#tabel-dokumen").on('click', '.del-dokumen', function() {
            if ($(this).data('id')) {
                $("#deleted_FileDokumen").append(
                    `<input type="hidden" name="delete_Dokumen[]" value="${$(this).data('id')}">`);
            }
            let index = $(this).data('index');
            $("#dokumen-" + index).remove();
        });
    });
</script>
@endpush
