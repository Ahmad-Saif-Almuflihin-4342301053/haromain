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

                    @if ($status)
                        <form id="form-submit">
                            @csrf

                            <p class="login-header">Reset Password</p>

                            <div class="login-input-group">
                                <label class="login-label">Password Baru</label>
                                <div class="password-group">
                                    <input type="password" name="Password" id="input-password1" class="password-input"
                                        placeholder="Masukkan Password" required>
                                    <a href="#" style="color: #b8b8b8;" id="togglePassword1">
                                        <i class="fa fa-eye" id="icon-show1"></i>
                                        <i class="fa fa-eye-slash" id="icon-hide1" style="display: none;"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="login-input-group">
                                <label class="login-label">Ulangi Password</label>
                                <div class="password-group">
                                    <input type="password" name="PasswordConfirm" id="input-password2"
                                        class="password-input" placeholder="Konfirmasi Password" required>
                                    <a href="#" style="color: #b8b8b8;" id="togglePassword2">
                                        <i class="fa fa-eye" id="icon-show2"></i>
                                        <i class="fa fa-eye-slash" id="icon-hide2" style="display: none;"></i>
                                    </a>
                                </div>
                            </div>

                            <input type="hidden" name="token" value="{{ $token }}">

                            <button type="submit" class="btn-black">RESET</button>
                        </form>
                    @else
                        <div
                            style="
                            color: black;
                            text-align: center;
                            font-size: 20px;
                            min-height: 200px;
                            padding-top: 100px;
                        ">
                            Link pemulihan akun tidak valid
                            <br><br>
                            <a href="{{ url('auth/lupapassword') }}" class="btn-black">
                                COBA LAGI
                            </a>
                        </div>
                    @endif

                    <hr style="margin-top: 6rem;">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#togglePassword1').on('click', function() {
                togglePassword(1)
            });
            $('#togglePassword2').on('click', function() {
                togglePassword(2)
            });

            function togglePassword(id) {
                toggleIcon(id)
                var passwordField = $('#input-password' + id);
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                } else {
                    passwordField.attr('type', 'password');
                }
            }

            function toggleIcon(id) {
                $('#icon-hide' + id).toggle()
                $('#icon-show' + id).toggle()
            }

            $("#form-submit").submit(function(e) {
                e.preventDefault()
                const formData = $(this).serializeArray();

                $.ajax({
                    url: `${baseURL}auth/updatepassword`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: formData,
                    success: function(res) {

                        if (res.status) {
                            toast('success', 'Berhasil', res.message)

                            setTimeout(() => {
                                window.location.href = `${res.redirect}`
                            }, 2000);
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });

            });
        });
    </script>
@endpush
