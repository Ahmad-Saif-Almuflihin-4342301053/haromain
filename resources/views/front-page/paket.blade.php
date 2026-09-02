@extends('layouts.front')

@section('content')
    @push('css')
        <style>
            .pagination {
                display: flex;
                padding: 1em 0;
            }

            .pagination a,
            .pagination .active .page-link,
            .pagination strong {
                border-radius: 8px;
                color: black;
                padding: 0.5em;
                margin-right: 0.5em;
                text-decoration: none;
            }

            .pagination .active .page-link {
                background-color: red;

            }

            .pagination a:hover,
            .pagination strong {
                background-color: #e5a11d;
                color: white;
            }

            li.paginate_button.page-item {
                list-style: none;
            }
        </style>
    @endpush
    <section id="featured-products" class="product-store padding-medium">
       <div class="container breadcrumbs text-right">
    <a href="{{ url('/') }}">Beranda</a> / Katalog
</div>

<div class="container" style="background-color: #fafafa">
    <div class="section-header d-flex flex-wrap align-items-center justify-content-between">
        <h2 class="section-title">Katalog Paket</h2>
    </div>

    <form action="" id="form-kategori" method="GET" style="margin:0;">
        <select name="type" id="filter-kategori" class="form-control" style="width: 200px;">
            <option value="">Semua Jenis</option>
            @php
                $jenisList = ["HAJI PLUS", "UMRAH", "DOMESTIK", "MANCANEGARA"];
            @endphp
            @foreach ($jenisList as $jenis)
                <option value="{{ $jenis }}" {{ (isset($type) && $type == $jenis) ? 'selected' : '' }}>
                    {{ $jenis }}
                </option>
            @endforeach
        </select>
    </form>

    <div class="swiper product-swiper overflow-hidden">
        <div class="swiper-wrapper" style="flex-flow: wrap;">
            @if(!empty($paket) && count($paket))
                @foreach($paket as $row)
                    <div class="product-item col-md-3 border-right">
                        <a href="{{ url('Paket/detail') }}?id={{ base64_encode($row->IDPaket) }}">
                            <div class="image-holder">
                                @php
                                    $imgPath = $row->Gambar1 != ''
                                        ? url('assets/gambar-paket/' . $row->Gambar1)
                                        : url('assets/front-page/images/default.png');
                                @endphp
                                <div style="background-image: url('{{ $imgPath }}');background-repeat: no-repeat;background-size: cover;background-position: center;width:100%;height:0;padding-bottom:100%">
                                </div>
                            </div>

                            <div class="product-detail">
                                <p style="margin:0; text-align:right">{{ date_indo(datetime_to_date($row->TglBerangkat)) }}</p>
                                <h3 class="product-title">{{ $row->NamaPaket }}</h3>
                                <p style="color:#CB0202; font-weight:700; margin:0">{{ ucwords(strtolower($row->KategoriPaket)) }}</p>
                                <div class="h3 text-black" style="font-weight: 600;">{{ formatCurrency($row->BiayaQuad) }}</div>
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
{{--
                <div style="display: block;width: 100%;">
                    {{ $pagination }}
                </div> --}}
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
@endsection
@push('js')
    <script>
        document.getElementById('filter-kategori').addEventListener('change', function() {
            document.getElementById('form-kategori').submit();
        });
    </script>
@endpush
