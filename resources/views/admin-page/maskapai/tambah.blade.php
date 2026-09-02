@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css"
            integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endpush

    @php
        $editMode = isset($data) && !empty($data);
        $maskapaiData = $editMode ? $data : null;
    @endphp

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ $editMode ? 'Edit' : 'Tambah' }} Maskapai
                            </h4>

                            <div id="alert-container" style="display:none;">
                                <div id="alert-message" class="alert"></div>
                            </div>

                            <form id="form-maskapai" enctype="multipart/form-data">
                                @csrf

                                @if ($editMode)
                                    <input type="hidden" name="id" value="{{ $maskapaiData['IDMaskapai'] }}">
                                    <input type="hidden" name="old_logo" value="{{ $maskapaiData['Logo'] }}">
                                @endif

                                <div class="form-group">
                                    <label for="nama">Nama Maskapai</label>
                                    <input type="text" class="form-control" id="nama" name="maskapai"
                                        value="{{ $editMode ? $maskapaiData['Maskapai'] : '' }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="logo">Logo Maskapai</label>
                                        <input type="file" name="logo" id="logo" class="file-upload-default"
                                            accept=".png, .jpg, .jpeg">
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info"
                                                disabled placeholder="Upload foto (.jpg, .png)">
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-primary" type="button"> Pilih
                                                    Gambar </button>
                                            </span>
                                        </div>
                                </div>

                                <div class="mt-4 text-right">
                                    <button type="submit" class="btn btn-primary" id="btn-submit">
                                        {{ $editMode ? 'Update' : 'Simpan' }}
                                    </button>

                                    <a href="{{ url('admin/maskapai') }}" class="btn btn-light">
                                        Batal
                                    </a>

                                    <button class="btn btn-primary loading d-none" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm"></span>
                                        Loading...
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script>
        $(document).ready(function() {
            $('.dropify').dropify({
                messages: {
                    default: 'Drag dan drop gambar di sini atau klik',
                    replace: 'Drag dan drop atau klik untuk mengganti gambar',
                    remove: 'Hapus',
                    error: 'Oops, terjadi kesalahan.'
                }
            });

            $('#form-maskapai').on('submit', function(e) {
                e.preventDefault();

                $('#btn-submit').addClass('d-none');
                $('.loading').removeClass('d-none');

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ url('admin/maskapai/simpan') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if (response.status) {
                                showAlert('success', response.message);

                                if (response.redirect) {
                                    setTimeout(function() {
                                        window.location.href = response.redirect;
                                    }, 1500);
                                }
                            } else {
                                showAlert('danger', response.message);
                                resetButton();
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                            showAlert('danger', 'Terjadi kesalahan pada sistem');
                            resetButton();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);

                        let message = 'Gagal mengirim data. Silakan coba lagi.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        showAlert('danger', message);
                        resetButton();
                    }
                });
            });

            function resetButton() {
                $('#btn-submit').removeClass('d-none');
                $('.loading').addClass('d-none');
            }

            function showAlert(type, message) {
                $('#alert-message')
                    .removeClass()
                    .addClass('alert alert-' + type)
                    .html(message);

                $('#alert-container').show();

                $('html, body').animate({
                    scrollTop: $('#alert-container').offset().top - 100
                }, 200);
            }
        });
    </script>
@endpush
