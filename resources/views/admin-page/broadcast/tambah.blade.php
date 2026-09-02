@extends('layouts.master')

@section('content')

@php
    $edit = isset($pengumuman->IDPengumuman);
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
                                <div class="form-group col-md-12">
                                    <label>Provinsi</label>
                                    <select name="prov" id="prov" class="form-control select2" required>
                                        <option value="">Pilih Provinsi</option>
                                        @if(isset($prov))
                                            @foreach($prov as $row)
                                                <option value="{{ $row->IDProv }}"
                                                    {{ isset($pengumuman) && $row->IDProv == $pengumuman->IDProv ? 'selected' : '' }}>
                                                    {{ $row->NamaProv }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="narasi">Narasi Broadcast</label>
                                    <textarea name="narasi" id="narasi" cols="30" rows="5" class="form-control" placeholder="Masukkan Narasi Broadcast">{{ $pengumuman->Isi ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="text-right">
                                <input type="hidden" name="id" id="id" value="{{ $pengumuman->IDPengumuman ?? '' }}">
                                <button type="button" onclick="history.back()" class="btn btn-outline-primary mr-2">Batal</button>
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
    const isEdit = $("#id").val();
    if (!isEdit) {
        setprov();
    }

    function setprov() {
        $("#prov").empty().append('<option value="">Pilih Provinsi</option>');
        $.ajax({
            url: "{{ url('admin/Lokasi/getprov') }}",
            type: 'GET',
            success: function(res) {

                res.data.forEach(el => {
                    $("#prov").append(`<option value="${el.IDProv}">${el.NamaProv}</option>`);
                });
            },
            error: function() {
                toast('error', 'Sistem Error');
            }
        });
    }

    $("#form-default").submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: "{{ url('admin/Broadcast/simpan') }}",
            type: 'POST',
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(res) {

                if (res.status) {
                    toast('success', res.message);
                    console.log(res);
                    window.location.href = res.redirect;
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
