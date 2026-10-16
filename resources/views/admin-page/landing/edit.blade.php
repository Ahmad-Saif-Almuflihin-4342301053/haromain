@extends('layouts.master')

@section('content')
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs4.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .form-submitting {
        pointer-events: none;
        opacity: 0.6;
    }
    .current-media-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 6px;
        padding: 10px;
        text-align: center;
        margin-bottom: 10px;
    }
    .current-media-img {
        max-height: 120px;
        max-width: 100%;
        object-fit: contain;
    }
</style>
@endpush

@php
    $isMediaUrl = !empty($data->GambarKonten) && (str_starts_with($data->GambarKonten, 'http://') || str_starts_with($data->GambarKonten, 'https://'));
    $hasLocalImage = !empty($data->GambarKonten) && !$isMediaUrl;
    $seksiLabel = $kategori_list[$data->JenisKonten] ?? $data->JenisKonten;
@endphp

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-10 offset-md-1 stretch-card grid-margin">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Edit Konten ({{ $seksiLabel }})</h4>
                            <a href="{{ route('admin.landing.index') }}" class="btn btn-light btn-sm">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali
                            </a>
                        </div>

                        {{-- Alert Container --}}
                        <div id="alert-container" style="display:none;" class="mb-3">
                            <div id="alert-message" class="alert"></div>
                        </div>

                        <form id="form-edit-landing" enctype="multipart/form-data" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $data->IDKonten }}">
                            <input type="hidden" name="old_GambarKonten" value="{{ $data->GambarKonten ?? '' }}">
                            <input type="hidden" name="is_remove" id="is_remove" value="0">

                            {{-- Judul Konten / Headline --}}
                            <div class="form-group">
                                <label for="JudulKonten" class="font-weight-bold">
                                    Judul / Headline <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="JudulKonten"
                                       name="JudulKonten"
                                       value="{{ old('JudulKonten', $data->JudulKonten ?? '') }}"
                                       placeholder="Masukkan judul atau headline seksi..."
                                       required>
                                <small class="form-text text-muted">
                                    Judul utama yang ditampilkan pada kartu atau header seksi.
                                </small>
                            </div>

                            {{-- Urutan Tampil (NoUrut) --}}
                            <div class="form-group">
                                <label for="NoUrut" class="font-weight-bold">No. Urut (Urutan Tampil)</label>
                                <input type="number"
                                       class="form-control col-md-3"
                                       id="NoUrut"
                                       name="NoUrut"
                                       min="0"
                                       value="{{ old('NoUrut', $data->NoUrut ?? '') }}"
                                       placeholder="Contoh: 1, 2, 3...">
                                <small class="form-text text-muted">
                                    Digunakan untuk mengurutkan item list (opsional).
                                </small>
                            </div>

                            {{-- Input Khusus URL Video jika jenis LANDING_KONDISI_PENGGUNA --}}
                            @if ($data->JenisKonten === 'LANDING_KONDISI_PENGGUNA')
                                <div class="form-group">
                                    <label for="VideoUrl" class="font-weight-bold">
                                        URL Video YouTube / Video Link
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-youtube-play text-danger"></i></span>
                                        </div>
                                        <input type="url"
                                               class="form-control"
                                               id="VideoUrl"
                                               name="VideoUrl"
                                               value="{{ old('VideoUrl', $isMediaUrl ? $data->GambarKonten : '') }}"
                                               placeholder="https://www.youtube.com/watch?v=...">
                                    </div>
                                    <small class="form-text text-muted">
                                        Tautan video demonstrasi untuk seksi kondisi pengguna.
                                    </small>
                                </div>
                            @endif

                            {{-- Upload Gambar / Ikon --}}
                            <div class="form-group">
                                <label for="GambarKonten" class="font-weight-bold">
                                    Gambar / Ikon Media
                                </label>

                                @if ($hasLocalImage)
                                    <div class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                        <small class="text-muted d-block mb-1 font-weight-bold">Gambar Saat Ini:</small>
                                        <img src="{{ asset('assets/konten/' . $data->GambarKonten) }}"
                                             alt="Preview"
                                             class="img-fluid rounded"
                                             style="max-height: 120px; object-fit: contain;">
                                        <div class="small text-muted mt-1">{{ $data->GambarKonten }}</div>
                                    </div>
                                    <input type="file"
                                           class="dropify"
                                           id="GambarKonten"
                                           name="GambarKonten"
                                           data-height="160"
                                           data-allowed-file-extensions="jpg jpeg png webp svg"
                                           data-max-file-size="2M"
                                           data-default-file="{{ asset('assets/konten/' . $data->GambarKonten) }}">
                                @else
                                    <input type="file"
                                           class="dropify"
                                           id="GambarKonten"
                                           name="GambarKonten"
                                           data-height="160"
                                           data-allowed-file-extensions="jpg jpeg png webp svg"
                                           data-max-file-size="2M">
                                @endif

                                <small class="form-text text-muted">
                                    Format yang didukung: JPG, JPEG, PNG, WEBP, SVG. Ukuran maksimal <strong>2MB</strong>.
                                </small>
                            </div>

                            {{-- Isi Konten / Deskripsi --}}
                            <div class="form-group">
                                <label for="IsiKonten" class="font-weight-bold">
                                    Deskripsi / Isi Konten
                                </label>
                                <textarea class="form-control summernote"
                                          id="IsiKonten"
                                          name="IsiKonten"
                                          rows="6">{{ old('IsiKonten', $data->IsiKonten ?? '') }}</textarea>
                                <small class="form-text text-muted">
                                    Format teks lengkap atau narasi konten seksi.
                                </small>
                            </div>

                            {{-- Tombol Submit Sesuai Standar Haromain (Gambar 1) --}}
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary mr-2" id="btn-submit">Simpan</button>
                                <button class="btn btn-primary loading mr-2 d-none" type="button" disabled><span class="spinner-border spinner-border-sm mr-1"></span> Loading...</button>
                                <a href="{{ route('admin.landing.index') }}" class="btn btn-light">Batal</a>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote.min.js"
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
$(document).ready(function() {
    // Inisialisasi Dropify
    $('.dropify').dropify({
        messages: {
            default: 'Seret dan lepas gambar ke sini atau klik untuk memilih',
            replace: 'Klik atau seret untuk mengganti gambar',
            remove: 'Hapus Gambar',
            error: 'Terjadi kesalahan saat memproses gambar.'
        },
        error: {
            'fileSize': 'Ukuran file terlalu besar (Maksimal 2MB).',
            'fileExtension': 'Format file tidak didukung (Hanya jpg, jpeg, png, webp).'
        }
    });

    // Inisialisasi Summernote
    $('.summernote').summernote({
        height: 250,
        placeholder: 'Tuliskan deskripsi konten di sini...',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    // Handle hapus gambar via dropify
    $('.dropify-clear').on('click', function() {
        $('#is_remove').val(1);
    });

    // Form submission dengan Anti-Rage Click / Submit Debounce
    let isSubmitting = false;

    function lockSubmit() {
        isSubmitting = true;
        $('#btn-submit').addClass('d-none');
        $('.loading').removeClass('d-none');
        $('#form-edit-landing').addClass('form-submitting');
    }

    function unlockSubmit() {
        isSubmitting = false;
        $('#btn-submit').removeClass('d-none');
        $('.loading').addClass('d-none');
        $('#form-edit-landing').removeClass('form-submitting');
    }

    $('#form-edit-landing').on('submit', function(e) {
        e.preventDefault();

        if (isSubmitting) return;
        lockSubmit();

        // Safety timeout fallback: unlock setelah 15 detik jika request hang
        const safetyTimeout = setTimeout(function() {
            if (isSubmitting) {
                unlockSubmit();
                showAlert('danger', 'Koneksi timeout, silakan periksa jaringan dan coba lagi.');
            }
        }, 15000);

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.landing.simpan') }}",
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            contentType: false,
            processData: false,
            success: function(response) {
                clearTimeout(safetyTimeout);

                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch(err) {}
                }

                if (response.status) {
                    showAlert('success', response.message);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(function() {
                        window.location.href = response.redirect || "{{ route('admin.landing.index') }}";
                    }, 1200);
                } else {
                    unlockSubmit();
                    showAlert('danger', response.message || 'Gagal menyimpan perubahan.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan validasi.'
                    });
                }
            },
            error: function(xhr) {
                clearTimeout(safetyTimeout);
                unlockSubmit();

                let errorMsg = 'Gagal menyimpan data ke server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                showAlert('danger', errorMsg);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: errorMsg
                });
            }
        });
    });

    function showAlert(type, message) {
        $('#alert-message').removeClass().addClass('alert alert-' + type).html(message);
        $('#alert-container').show();
        $('html, body').animate({
            scrollTop: $("#alert-container").offset().top - 120
        }, 250);
    }
});
</script>
@endpush
