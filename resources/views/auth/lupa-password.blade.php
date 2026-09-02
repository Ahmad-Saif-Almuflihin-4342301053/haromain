@extends('layouts.login')

@section('content')
    <div class="overlay-black">
        <div class="login-container" style="width: 100%;">
            <div class="login-info">

                <a href="{{ url('/') }}">
                    <div class="login-logo">
                        <img style="max-width: 150px;" src="{{ asset('assets/haromainv0.png') }}" alt="">
                    </div>
                </a>

                <br>

                <p class="login-p1">Selamat Datang!</p>
                <p class="login-p2">
                    Selamat beraktivitas, Semoga Allah berkahi usaha Anda... Aamiiin...
                </p>
            </div>

            <div class="login-form">
                <div class="login-card">

                    <p class="login-header">Lupa Password</p>

                    <form id="form-reset" method="POST">
                        @csrf

                        <div class="login-input-group">
                            <label class="login-label">Username / No HP</label>
                            <input type="text" name="UserNameOrHP" class="login-input"
                                placeholder="Masukkan Username atau No HP">
                        </div>

                        <div>
                            <button type="submit" id="btn-submit" class="btn-black">SUBMIT</button>
                        </div>
                    </form>

                    <hr style="margin-top: 6rem;">

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

    <script>
        $(document).ready(function() {
            const baseURL = "{{ url('/') }}/";
            $("#form-reset").submit(function(e) {
                e.preventDefault()
                $('#btn-submit').prop('disabled', true)
                $('#btn-submit').text('Memproses...')
                const formData = $("#form-reset").serializeArray();
                $.ajax({
                    url: `${baseURL}auth/sendemail`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: formData,
                    success: function(res) {
                        if (res.status) {
                            alert2('success', 'Berhasil', res.message)
                        } else {
                            toast('error', res.message)
                        }
                        enableButton()
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                        enableButton()
                    }
                });

            });

            function enableButton() {
                $('#btn-submit').prop('disabled', false)
                $('#btn-submit').text('SUBMIT')
            }
        })
    </script>
@endpush
