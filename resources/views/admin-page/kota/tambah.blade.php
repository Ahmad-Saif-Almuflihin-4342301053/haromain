@extends('layouts.master')

@section('content')

@php
$edit = isset($data);
@endphp

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">

                        <h4 class="card-title">
                            {{ $edit ? 'Edit' : 'Tambah' }} Kota
                        </h4>

                        <div id="alert-container" style="display: none;">
                            <div id="alert-message" class="alert"></div>
                        </div>

                        <form id="form-kota" method="post">
                            @csrf

                            @if ($edit)
                            {{-- Hidden ID saat edit --}}
                            <input type="hidden" name="id" value="{{ $data['IDKota'] }}">
                            @endif

                            <div class="form-group">
                                <label for="nama">Nama Kota</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="nama"
                                    name="NamaKota"
                                    value="{{ $edit ? $data['NamaKota'] : '' }}"
                                    required>
                            </div>

                            <div class="mt-4 text-right">
                                <button type="submit" class="btn btn-primary" id="btn-submit">
                                    {{ $edit ? 'Update' : 'Simpan' }}
                                </button>

                                <a href="{{ url('admin/kota') }}" class="btn btn-light">
                                    Batal
                                </a>

                                <button class="btn btn-primary loading d-none" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
<script>
    $(document).ready(function () {

        $('#form-kota').on('submit', function (e) {
            e.preventDefault();

            $('#btn-submit').addClass('d-none');
            $('.loading').removeClass('d-none');

            $.ajax({
                url: "{{ url('admin/kota/simpan') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function (response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }

                        if (response.status) {
                            showAlert('success', response.message);

                            if (response.redirect) {
                                setTimeout(function () {
                                    window.location.href = response.redirect;
                                }, 1500);
                            }
                        } else {
                            showAlert('danger', response.message);
                            resetButton();
                        }
                    } catch (e) {
                        console.error('Parsing error:', e);
                        showAlert('danger', 'Terjadi kesalahan pada sistem');
                        resetButton();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showAlert('danger', 'Gagal mengirim data. Silakan coba lagi.');
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
