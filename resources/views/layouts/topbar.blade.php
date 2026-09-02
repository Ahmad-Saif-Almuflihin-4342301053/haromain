@php
    $user = Auth::user();

    $namaLengkap = $user?->NamaLengkap;
    $email = $user?->Email;
    $foto = $user?->Foto;

    $titleMenu = $title ?? $menu ?? '';

    $avatarURL = $foto
        ? asset('assets/foto-profil/' . $foto)
        : asset('assets/admin-page/images/default-avatar.jpg');
@endphp


<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row shadow-sm">
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-chevron-double-left"></span>
        </button>

        <ul class="navbar-nav h4">
            {{ $titleMenu }}
        </ul>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown d-none d-md-block">
                <a class="nav-link dropdown-toggle font-13" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-text pr-2 font-weight-normal" style="line-height: 16px;">
                        <div>{{ $namaLengkap }}</div>
                        <div>{{ $email }}</div>
                    </div>
                    <div class="nav-profile-image">
                        <div class="bg-cover rounded-circle" style="width: 40px; height:40px; background-image:url('{{ $avatarURL }}')"></div>
                    </div>
                </a>
                <div class="dropdown-menu center navbar-dropdown" aria-labelledby="profileDropdown">
                    @if(Auth::user()->LevelID == 2)
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalChangePassword">
                            <i class="fa fa-key mr-3"></i> Ubah Password
                        </a>
                    @endif
                    <a class="dropdown-item" href="{{ url('auth/logout') }}">
                        <i class="fa fa-sign-out mr-3"></i> Keluar
                    </a>
                </div>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>

<!-- Modal Change Password -->
<div class="modal fade" id="modalChangePassword" tabindex="-1" role="dialog" aria-labelledby="modalChangePasswordLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalChangePasswordLabel">Ubah Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-change-password">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label>Password Lama</label>
                        <div class="input-group">
                            <input type="password" name="old_password" class="form-control" required>
                            <div class="input-group-append">
                                <span class="input-group-text cursor-pointer toggle-password" style="cursor: pointer;">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="5">
                            <div class="input-group-append">
                                <span class="input-group-text cursor-pointer toggle-password" style="cursor: pointer;">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control" required minlength="5">
                            <div class="input-group-append">
                                <span class="input-group-text cursor-pointer toggle-password" style="cursor: pointer;">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Password Visibility
        $(document).on('click', '.toggle-password', function() {
            let input = $(this).closest('.input-group').find('input');
            let icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        const formChangePassword = document.getElementById('form-change-password');
        if (formChangePassword) {
            formChangePassword.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                // Validate Password Match
                let newPass = formData.get('new_password');
                let confirmPass = formData.get('confirm_password');
                
                if (newPass !== confirmPass) {
                    toast('error', 'Konfirmasi password tidak cocok');
                    return;
                }

                // Use jQuery ajax for consistency with project
                $.ajax({
                    url: "{{ route('admin.user.changePassword') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            toast('success', res.message);
                            $('#modalChangePassword').modal('hide');
                            formChangePassword.reset();
                            // Reset eye icons
                            $('.toggle-password i').removeClass('fa-eye-slash').addClass('fa-eye');
                            $('#form-change-password input[type="text"]').attr('type', 'password');
                        } else {
                            toast('error', res.message);
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal mengubah password';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toast('error', msg);
                    }
                });
            });
        }
    });
</script>
