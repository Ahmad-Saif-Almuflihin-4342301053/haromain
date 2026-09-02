@extends('layouts.master')

@section('content')

@php
    $editMode = isset($data) && !empty($data);
@endphp

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ $editMode ? 'Edit' : 'Tambah' }} FAQ
                        </h4>

                        <form id="form-faq" method="POST">
                            @csrf

                            @if ($editMode)
                                <input type="hidden" name="id" value="{{ $data->IDKonten }}">
                            @endif

                            <div class="form-group">
                                <label for="JudulKonten">Pertanyaan</label>
                                <input type="text" class="form-control" id="JudulKonten" name="JudulKonten"
                                    value="{{ old('JudulKonten', $editMode ? $data->JudulKonten : '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="IsiKonten">Jawaban</label>
                                <textarea class="form-control" id="IsiKonten" name="IsiKonten" rows="5" required>{{ old('IsiKonten', $editMode ? $data->IsiKonten : '') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                            <a href="{{ route('admin.faq.index') }}" class="btn btn-light">Batal</a>
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
    $('#form-faq').submit(function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.faq.simpan') }}",
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
</script>
@endpush
