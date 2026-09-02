@extends('layouts.login')

@section('content')

<div class="overlay-black">
    <div class="login-container" style="width:100%">

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

                <p class="login-header">Login</p>

                <form id="form-login" method="POST" action="{{ route('auth.login') }}">
                    @csrf

                    {{-- USERNAME --}}
                    <div class="login-input-group">
                        <label class="login-label">Username / No HP</label>
                        <input type="text" name="UserName" class="login-input"
                            placeholder="Masukkan Username atau No HP">
                    </div>

                    {{-- PASSWORD --}}
                    <div class="login-input-group">
                        <label class="login-label">Password</label>

                        <div class="password-group">
                            <input type="password" name="UserPsw" id="input-password" class="password-input"
                                placeholder="Masukkan Password">

                            <a href="#" style="color: #b8b8b8;" id="togglePassword">
                                <i class="fa fa-eye" id="icon-show"></i>
                                <i class="fa fa-eye-slash" id="icon-hide" style="display: none;"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn-black">
                            LOGIN
                        </button>

                        <a href="{{ url('auth/lupapassword') }}" class="btn-link">
                            Lupa Password?
                        </a>
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
        $('#togglePassword').on('click', function() {
            toggleIcon();
            let passwordField = $('#input-password');
            passwordField.attr('type',
                passwordField.attr('type') === 'password' ? 'text' : 'password'
            );
        });

        function toggleIcon() {
            $('#icon-hide').toggle();
            $('#icon-show').toggle();
        }

        $("#form-login").submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: "{{ route('auth.login') }}",
                type: "POST",
                data: formData,
                success: function(res) {
                    if (res.status) {
                        toast('success', res.message);
                        window.location.href = res.redirect;
                    } else {
                        toast('error', res.message);
                    }
                },
                error: function(xhr) {
                    toast('error', 'Sistem Error');
                }
            });
        });
    });
</script>

@endpush
