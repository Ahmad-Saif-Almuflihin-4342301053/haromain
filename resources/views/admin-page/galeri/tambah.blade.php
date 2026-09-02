@extends('layouts.master')

@section('content')

@php
    $editMode = isset($data) && !empty($data);
@endphp

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ $editMode ? 'Edit' : 'Tambah' }} Galeri
                        </h4>

                        <form id="form-galeri" enctype="multipart/form-data" method="POST">
                            @csrf

                            @if ($editMode)
                                <input type="hidden" name="id" value="{{ $data->IDGaleri }}">
                                <input type="hidden" name="old_KontenGaleri" value="{{ $data->KontenGaleri }}">
                            @endif

                            <div class="form-group">
                                <label for="NamaGaleri">Nama Galeri</label>
                                <input type="text" class="form-control" id="NamaGaleri" name="NamaGaleri"
                                    value="{{ old('NamaGaleri', $editMode ? $data->NamaGaleri : '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="JenisGaleri">Jenis Galeri</label>
                                <select class="form-control" id="JenisGaleri" name="JenisGaleri" required>
                                    <option value="gambar" {{ old('JenisGaleri', $editMode ? $data->JenisGaleri : '') == 'gambar' ? 'selected' : '' }}>Gambar</option>
                                    <option value="video" {{ old('JenisGaleri', $editMode ? $data->JenisGaleri : '') == 'video' ? 'selected' : '' }}>Video</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="KategoriGaleri">Kategori Galeri</label>
                                <input type="text" class="form-control" id="KategoriGaleri" name="KategoriGaleri"
                                    value="{{ old('KategoriGaleri', $editMode ? $data->KategoriGaleri : '') }}" required placeholder="Contoh: Dokumentasi, Kegiatan, dll">
                            </div>

                            <div class="form-group" id="input-file">
                                <label for="KontenGaleri">File Konten</label>
                                <input type="file" class="dropify" id="KontenGaleri" name="KontenGaleri"
                                    data-max-file-size="5M"
                                    @if($editMode && $data->JenisGaleri == 'gambar' && !filter_var($data->KontenGaleri, FILTER_VALIDATE_URL))
                                        data-default-file="{{ asset('assets/galeri/' . $data->KontenGaleri) }}"
                                    @endif
                                >
                            </div>

                            <div class="form-group" id="input-link" style="display:none;">
                                <label for="LinkVideo">Link Video (Youtube/URL)</label>
                                <input type="text" class="form-control" id="LinkVideo" name="LinkVideo"
                                    value="{{ old('LinkVideo', ($editMode && $data->JenisGaleri == 'video') ? $data->KontenGaleri : '') }}"
                                    placeholder="https://youtube.com/...">
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                            <a href="{{ route('admin.galeri.index') }}" class="btn btn-light">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify();

        function toggleInput() {
            const jenis = $('#JenisGaleri').val();
            if (jenis === 'video') {
                $('#input-link').show();
                // Optionally hide file input or allow both
                $('#input-file').hide(); 
                $('.dropify-wrapper').find('p').text('Upload Video File (Optional)');
            } else {
                $('#input-link').hide();
                $('#input-file').show();
                $('.dropify-wrapper').find('p').text('Upload Gambar');
            }
        }

        $('#JenisGaleri').change(toggleInput);
        toggleInput(); // Init

        $('#form-galeri').submit(function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.galeri.simpan') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status) {
                        toast('success', response.message);
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 400);
                    } else {
                        toast('error', response.message);
                    }
                },
                error: function () {
                    toast('error', 'Terjadi kesalahan saat menyimpan data.');
                }
            });
        });
    });
</script>
@endpush
