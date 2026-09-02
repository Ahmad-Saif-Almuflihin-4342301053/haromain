@extends('layouts.master')

@section('content')
@php
    $edit = isset($banner) && isset($banner->KodeBanner);
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

                                @if (Auth::user()->LevelID == 1)
                                    <div class="form-group col-md-12">
                                        <label>Pemilik Banner / Travel</label>
                                        <select name="IDTravel" id="IDTravel" class="form-control select2">
                                            <option value="" {{ empty($banner->IDTravel) ? 'selected' : '' }}>
                                                Haromain (Pusat / Umum)
                                            </option>
                                            @foreach ($travel as $row)
                                                <option value="{{ $row->IDTravel }}"
                                                    {{ ($banner->IDTravel ?? '') == $row->IDTravel ? 'selected' : '' }}>
                                                    {{ $row->NamaTravel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            Pilih <strong>Haromain (Pusat / Umum)</strong> jika banner milik Haromain, atau pilih travel jika banner khusus untuk travel tersebut.
                                        </small>
                                    </div>
                                @endif

                                <div class="form-group col-md-12">
                                    <label>Gambar</label>
                                    <input type="file"
                                           name="Gambar"
                                           id="Gambar"
                                           class="file-upload-default"
                                           accept=".png, .jpg, .jpeg">

                                    <div class="input-group col-xs-12">
                                        <input type="text"
                                               class="form-control file-upload-info"
                                               disabled
                                               placeholder="Upload gambar (.jpg, .png). Maks 2MB">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">
                                                Pilih Gambar
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Keterangan 1</label>
                                    <textarea name="Keterangan1"
                                              rows="5"
                                              class="form-control"
                                              placeholder="Masukkan Judul Banner">{{ $banner->Keterangan1 ?? '' }}</textarea>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Keterangan 2</label>
                                    <textarea name="Keterangan2"
                                              rows="5"
                                              class="form-control"
                                              placeholder="Masukkan Keterangan Banner">{{ $banner->Keterangan2 ?? '' }}</textarea>
                                </div>

                            </div>

                            <div class="text-right">
                                <input type="hidden" name="id" value="{{ $banner->KodeBanner ?? '' }}">
                                <input type="hidden" name="old_Gambar" value="{{ $banner->Gambar ?? '' }}">

                                <button type="button"
                                        onclick="history.back()"
                                        class="btn btn-outline-primary mr-2">
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
    $(document).ready(function () {

        const baseURL = "{{ url('/') }}/";

        $('#form-default').on('submit', function (e) {
            e.preventDefault()

            const formData = new FormData(this)

            $.ajax({
                url: "{{ route('admin.banner.simpan') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (res) {


                    if (res.status) {
                        toast('success', res.message)
                        window.location.href = res.redirect
                    } else {
                        toast('error', res.message)
                        console.log(res)
                    }
                },
                error: function () {
                    toast('error', 'Sistem Error')
                }
            })
        })

    })
</script>

@endpush
