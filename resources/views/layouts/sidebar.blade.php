@php
    $currentUser = Auth::user();
    if ($currentUser) {
        $fiturLevel = new \App\Models\FiturLevel;
        $daftarmenu = $fiturLevel->getMenuByLevel($currentUser->LevelID);
        foreach ($daftarmenu as $m) {
            if ((int)$m->IsHasSubmenu === 1) {
                $m->Submenu = $fiturLevel->getSubmenuByLevel($currentUser->LevelID, $m->FiturID);
            }
        }
    } else {
        $daftarmenu = session('Menu') ?? [];
    }

    $namaTravel = session('NamaTravel');
    $namaLengkap = session('NamaLengkap');
    $email = session('Email');
    $foto = session('Foto');

    $isAdminCabang = ($currentUser && (int)$currentUser->LevelID === 2 && !empty($currentUser->IDCabang));
    $hiddenForCabang = [
        'Manajemen Banner',
        'Master Kota',
        'Manajemen Artikel',
        'Manajemen Lokasi',
        'Fee Haromain'
    ];
@endphp

<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link flex-column">
                <div>
                    <img src="{{ asset('assets/haromainv0.png') }}" class="img-fluid" alt="">
                </div>
            </a>
        </li>
        <li class="text-center h4 my-4">
            {{ $namaTravel }}
        </li>

        <li class="nav-item py-2">
            <a class="nav-link font-weight-bold" href="{{ url('admin/dashboard') }}">
                <span class="menu-title">DASHBOARD</span>
            </a>
        </li>
        <hr class="sidebar-hr">

        @foreach ($daftarmenu as $row)
            @if ($isAdminCabang && in_array($row->FiturName, $hiddenForCabang))
                @continue
            @endif
            @if (!$isAdminCabang && $currentUser && (int)$currentUser->LevelID === 2 && in_array($row->FiturName, ['Fee Haromain']))
                @continue
            @endif
            @if ($row->FiturName != 'Paket Promo' && $row->FiturName != 'Tagihan Biaya')
                @php
                    $hasSubmenu = $row->IsHasSubmenu == 1;
                @endphp
                <li class="nav-item {{ $row->FiturName === $menu ? 'active' : '' }}"
                    data-fitur-name="{{ $row->FiturName }}">
                    <a class="nav-link"
                        @if ($hasSubmenu) data-toggle="collapse"
                           href="#submenu{{ $row->FiturID }}"
                           aria-expanded="false"
                           aria-controls="submenu{{ $row->FiturID }}"
                       @else
                           href="{{ url($row->URL) }}" @endif>
                        <img class="pr-2" src="{{ asset('assets/admin-page/icon/' . $row->Icon) }}"
                            style="width: 35px;">
                        <span class="menu-title">{{ $row->FiturName }}</span>
                        @if ($hasSubmenu)
                            <i class="menu-arrow"></i>
                        @endif
                    </a>

                    @if ($hasSubmenu && isset($row->Submenu))
                        <div class="collapse" id="submenu{{ $row->FiturID }}">
                            <ul class="nav flex-column sub-menu">
                                @foreach ($row->Submenu as $item)
                                    @if ($isAdminCabang && in_array($item->FiturName, $hiddenForCabang))
                                        @continue
                                    @endif
                                    @if (!$isAdminCabang && $currentUser && (int)$currentUser->LevelID === 2 && in_array($item->FiturName, ['Fee Haromain']))
                                        @continue
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ url($item->URL) }}">{{ $item->FiturName }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endif
        @endforeach

        <hr class="sidebar-hr">
        <div class="mx-4 rounded-lg p-3" style="background: #F3C367">
            <div class="d-flex">
                <div class="nav-profile-image">
                    @php
                        $avatarURL = $foto
                            ? asset('assets/foto-profil/' . $foto)
                            : asset('assets/admin-page/images/default-avatar.jpg');
                    @endphp
                    <div class="bg-cover rounded-circle"
                        style="width: 40px; height:40px; background-image:url('{{ $avatarURL }}')"></div>
                </div>
                <div class="nav-profile-text p-2 font-weight-normal" style="line-height: 16px;">
                    <div class="font-weight-bold">{{ $namaLengkap }}</div>
                    <div style="word-break: break-word;">{{ $email }}</div>
                </div>
            </div>
            <a href="{{ url('auth/logout') }}" class="btn btn-primary">Log Out</a>
        </div>
    </ul>
</nav>
