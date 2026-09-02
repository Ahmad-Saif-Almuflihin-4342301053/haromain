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

                        <form id="form-default" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Gambar</label>

                                <input type="file"
                                       name="Gambar"
                                       id="Gambar"
                                       class="file-upload-default"
                                       accept=".jpg,.jpeg,.png">

                                <div class="input-group">
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

                            <div class="text-right">
                                <input type="hidden" name="id" value="{{ $edit ? $banner->IDKonten : '' }}">
                                <input type="hidden" name="old_Gambar" value="{{ $edit ? $banner->GambarKonten : '' }}">

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
