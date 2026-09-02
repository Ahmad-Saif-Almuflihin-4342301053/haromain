@extends('layouts.front')

@section('content')

<section id="billboard" class="overflow-hidden">
    <button class="button-prev">
        <i class="icon icon-chevron-left" style="margin-left: 6px;"></i>
    </button>
    <button class="button-next">
        <i class="icon icon-chevron-right"></i>
    </button>

    <div class="swiper main-swiper">
        <div class="swiper-wrapper">
            @foreach ($banner as $row)
                <div class="swiper-slide overlay-hitam"
                     style="background-image: url('{{ asset('assets/banner/' . $row->Gambar) }}');
                     background-repeat: no-repeat;
                     background-size: cover;
                     background-position: center;">

                    <div class="banner-content">
                        <div class="container" style="height: 70vh; display: flex; align-items: end;">
                            <div style="background: black; color: white; padding: 37px; position: absolute; right: 25px;">
                                <div style="font-size: 36px; font-weight: 700;">{{ $row->Keterangan1 }}</div>
                                <div>{{ $row->Keterangan2 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if(!$promo->isEmpty())
<section id="featured-products" class="product-store padding-medium">
    <div class="container" style="background-color: #fafafa">
        <div class="section-header d-flex flex-wrap align-items-center justify-content-between">
            <h2 class="section-title">Paket Promo</h2>
        </div>

        <div class="swiper product-swiper overflow-hidden">
            <div class="swiper-wrapper" style="flex-flow: wrap;">

                @foreach ($promo as $row)
                {{-- @dd($row) --}}
                    <div class="product-item col-md-4 border-right">
                        <a href="{{ url('Paket/detail?id=' . base64_encode($row->IDPaket)) }}">

                            <div class="image-holder">
                                @php
                                    $imgPath = $row->Gambar1 != ''
                                        ? asset('assets/gambar-paket/' . $row->Gambar1)
                                        : asset('assets/front-page/images/default.png');
                                @endphp

                                <div style="background-image: url('{{ $imgPath }}');
                                    background-repeat: no-repeat;
                                    background-size: cover;
                                    background-position: center;
                                    width:100%; height:0; padding-bottom:100%">
                                </div>
                            </div>

                            <div class="product-detail">
                                <p style="margin:0; text-align:right">{{ ($row->TglBerangkat) }}</p>

                                <h3 class="product-title">{{ $row->NamaPaket }}</h3>

                                <p style="color:#CB0202; font-weight:700; margin:0">
                                    {{ ucwords(strtolower($row->KategoriPaket)) }}
                                </p>

                                <div class="h3 text-black" style="font-weight: 600;">
                                    {{ formatCurrency(@$row->BiayaQuad) }}
                                </div>

                                <div style="color:black">{{ $row->NamaTravel }}</div>

                                <div style="text-align:right">
                                    <span style="font-size: 15px;">
                                        <i class="fa fa-map-marker"></i>
                                        {{ ucwords(strtolower($row->NamaKab)) }}
                                    </span>
                                </div>
                            </div>

                        </a>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="swiper-pagination"></div>
    </div>
</section>
@endif


<section id="featured-products" class="product-store">
    <div class="container" style="background-color: #fafafa">

        <div class="section-header d-flex flex-wrap align-items-center justify-content-between">
            <h2 class="section-title">Katalog Paket</h2>
            <div class="btn-wrap">
                <a href="{{ url('Paket') }}" class="d-flex align-items-center">
                    Lihat Semua <i class="icon icon icon-arrow-io"></i>
                </a>
            </div>
        </div>

        <form action="" id="form-kategori" method="get" style="margin:0">
            <select name="type" id="filter-kategori" class="form-control" style="margin-left:15px">
                <option value="">Semua Jenis</option>

                @php $jenisList = ["HAJI PLUS", "UMRAH", "DOMESTIK", "MANCANEGARA"]; @endphp

                @foreach ($jenisList as $jenis)
                    <option value="{{ $jenis }}" {{ $type == $jenis ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="swiper product-swiper overflow-hidden">
            <div class="swiper-wrapper" style="flex-flow: wrap;">

                @if($paket)
                    @foreach ($paket as $row)
                        <div class="product-item col-md-3 border-right">
                            <a href="{{ url('Paket/detail?id=' . base64_encode($row->IDPaket)) }}">

                                @php
                                    $imgPath = $row->Gambar1 != ''
                                        ? asset('assets/gambar-paket/' . $row->Gambar1)
                                        : asset('assets/front-page/images/default.png');
                                @endphp

                                <div class="image-holder">
                                    <div style="background-image: url('{{ $imgPath }}');
                                        background-repeat: no-repeat;
                                        background-size: cover;
                                        background-position: center;
                                        width:100%; height:0; padding-bottom:100%">
                                    </div>
                                </div>

                                <div class="product-detail">

                                    <p style="margin:0; text-align:right">
                                        {{ ($row->TglBerangkat) }}
                                    </p>

                                    <h3 class="product-title">{{ $row->NamaPaket }}</h3>

                                    <p style="color:#CB0202; font-weight:700; margin:0">
                                        {{ ucwords(strtolower($row->KategoriPaket)) }}
                                    </p>

                                    <div class="h3 text-black" style="font-weight: 600;">
                                        {{ formatCurrency(@$row->BiayaQuad) }}
                                    </div>

                                    <div style="color:black">{{ $row->NamaTravel }}</div>

                                    <div style="text-align:right">
                                        <span style="font-size: 15px;">
                                            <i class="fa fa-map-marker"></i>
                                            {{ ucwords(strtolower($row->NamaKab)) }}
                                        </span>
                                    </div>

                                </div>

                            </a>
                        </div>
                    @endforeach

                @else
                    <div style="padding: 0 2rem 2rem 2rem;font-size: medium;text-align: center;width: 100%;color: black;">
                        Tidak ada data
                    </div>
                @endif

            </div>
        </div>

        <div class="swiper-pagination"></div>

    </div>
</section>


<section id="brand-collection" style="padding: 1rem 6rem 2rem 6rem;">
    <div class="container">
        <h4 style="text-align: center;">Travel yang bekerja sama dengan kami:</h4>

        <div class="d-flex justify-content-center" style="text-align: center; flex-wrap: wrap;">

            @foreach ($travel as $row)
                @if ($row->LogoKantor)
                    <div class="col-md-2">
                        <img src="{{ asset('assets/logo-kantor/' . $row->LogoKantor) }}"
                             alt="logo"
                             style="width: 100%; padding:1rem">
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</section>

@endsection
@push('js')

<script>
    document.getElementById('filter-kategori').addEventListener('change', function() {
        document.getElementById('form-kategori').submit();
    });
</script>

@endpush
