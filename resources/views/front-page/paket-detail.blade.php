@extends('layouts.front')

@section('content')

    @push('css')
        <style>
            .circle-logo {
                border: 1px solid #e5a11d;
                width: auto;
                display: inline-block;
                border-radius: 40px;
            }

            #nav-profile {
                max-height: 350px;
                overflow-y: auto;
                padding-right: 10px;
            }

            .table-grid td:nth-child(1) {
                width: 70%;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
            integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    @endpush

    <div class="container breadcrumbs" style="margin-top: 3rem; margin-bottom: 10px">
        <a href="{{ url('/') }}">Beranda</a> / <a href="{{ url('Paket') }}">Katalog</a> / {{ $paket->NamaPaket }}
    </div>

    <section id="billboard" class="product-store">
        <div class="container">
            <div class="row">
                @if (
                    $paket->Gambar1 != '' ||
                        $paket->Gambar2 != '' ||
                        $paket->Gambar3 != '' ||
                        $paket->Gambar4 != '' ||
                        $paket->Gambar5 != '')
                    <div class="col-md-5">
                        <button class="button-prev">
                            <i class="icon icon-chevron-left" style="margin-left: 6px;"></i>
                        </button>
                        <button class="button-next">
                            <i class="icon icon-chevron-right"></i>
                        </button>
                        <div class="swiper main-swiper" style="width:100%!important; height:0; padding-bottom:100%">
                            <div class="swiper-wrapper">
                                @php
                                    $arrGambar = ['Gambar1', 'Gambar2', 'Gambar3', 'Gambar4', 'Gambar5'];
                                @endphp
                                @foreach ($arrGambar as $value)
                                    @if ($paket->$value != '')
                                        <div class="swiper-slide overlay-hitam"
                                            style="background-image: url('{{ url('/') }}/assets/gambar-paket/{{ $paket->$value }}');background-repeat: no-repeat;background-size: contain; width:100%!important; height:0; padding-bottom:100%">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="section-title" style="margin: 1rem 0 0 0;">{{ $paket->NamaPaket }}</h2>
                            <p style="color:#CB0202; font-weight:700; margin:0">
                                {{ ucwords(strtolower($paket->KategoriPaket)) }}</p>
                            <div class="d-flex">
                                <div class=""> <img
                                        src="{{ asset('assets/front-page/icon/ic_baseline-date-range.svg') }}"
                                        alt="" srcset=""> </div>
                                <div class="align-items-center d-flex text-black" style="line-height: 20px;">
                                    {{ date_indo(datetime_to_date($paket->TglBerangkat)) }} -
                                    {{ date_indo(datetime_to_date($paket->TglPulang)) }}
                                    <br> {{ $jumlahhari }} Hari
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 padding-xsmall">
                            <div class="d-flex">
                                @if ($paket->LogoKantor)
                                    <div class="logo-wrapper">
                                        <div class="circle-logo d-flex" style="width: 50px; height:50px;">
                                            <img style="width: 60%; margin: auto;"
                                                src="{{ asset('assets/logo-kantor/' . $paket->LogoKantor) }}"
                                                alt="">
                                        </div>
                                    </div>
                                @endif
                                <div class="ml-2">
                                    <span class="text-black" style="font-weight: 600;">{{ $paket->NamaTravel }}</span>
                                    <br>
                                    <br>
                                    <div style="font-size: 15px; margin-top:-10px">
                                        <i class="fa fa-map-marker"></i>
                                        {{ ucwords(strtolower($paket->NamaKab)) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 2rem;">
                        <h5 class="mt-3" style="letter-spacing: 0;"><b>Deskripsi Paket</b></h5>
                        <div class="" id="nav-tabContent">
                            <div class="fade show active" id="nav-home" aria-labelledby="nav-home-tab">
                                <p style="white-space: pre-line; font-size: 1rem"> {{ $paket->Deskripsi }} </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="itenery" class="mt-5">
        <div class="container">
            <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Itenerary</h2>
            <div class="py-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal">Hari Ke</label>
                            <select id="HariKe" name="HariKe" class="form-control">
                                @foreach ($datahari as $val)
                                    <option data-tanggal="{{ $val['tanggal'] }}" value="{{ $val['harike'] }}">Hari Ke
                                        {{ $val['harike'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped mb-3" id="table-itenerary">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Waktu</th>
                        <th>Agenda</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($itenerary as $i => $row)
                        <tr>
                            <td class="no text-center" style="width: 10%;">{{ $i + 1 }}</td>
                            <td class="" style="width: 20%;">
                                {{ ($row['TglMulai'] ? date('H:i', strtotime($row['TglMulai'])) : '') . ' - ' . ($row['TglSelesai'] ? date('H:i', strtotime($row['TglSelesai'])) : '') }}
                            </td>
                            <td class="" style="width: 35%;">{{ $row['Agenda'] }}</td>
                            <td class="" style="width: 35%;">{{ $row['Lokasi'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section id="featured-products" class="product-store">
        <div class="container">
            <div class="section-header row">
                <div class="col-md-8">
                    <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Tujuan</h2>
                    <div class="list-unstyled text-black row">
                        @if ($paket->KotaTujuan != '')
                            @foreach (json_decode($paket->KotaTujuan) as $value)
                                <div class="col-md-6 mb-1">
                                    <img src="{{ asset('assets/front-page/icon/gg_check-o.svg') }}" style="width: 30px;"
                                        alt="" srcset=""> {{ $value }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="featured-products" class="product-store">
        <div class="container">
            <div class="section-header row">
                <div class="col-md-8">
                    <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Dokumen Yang Dibutuhkan</h2>
                    <div class="list-unstyled text-black row">
                        @foreach ($syaratpaket as $row)
                            <div class="col-md-6 mb-1">
                                <img src="{{ asset('assets/front-page/icon/system-uicons_document.svg') }}"
                                    style="width: 30px;" alt="" srcset=""> {{ $row->NamaSyarat }}
                            </div>
                        @endforeach
                        @if (count($syaratpaket) < 1)
                            <div class="col-12">
                                <i style="color:#a2a2a2">Tidak ada dokumen</i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="featured-products" class="product-store">
        <div class="container">
            <div class="section-header row">
                <div class="col-md-4">
                    <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Include</h2>
                    <div class="list-unstyled text-black row">
                        @if ($paket->IncludeFasilitas != '')
                            @foreach (json_decode($paket->IncludeFasilitas) as $value)
                                <div class="col-md-6 mb-1">
                                    <img src="{{ asset('assets/front-page/icon/tabler_checkbox.svg') }}"
                                        style="width: 30px;" alt="" srcset=""> {{ $value }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Exclude</h2>
                    <div class="list-unstyled text-black row">
                        @if ($paket->ExcludeFasilitas != '')
                            @foreach (json_decode($paket->ExcludeFasilitas) as $value)
                                <div class="col-md-6 mb-1">
                                    <img src="{{ asset('assets/front-page/icon/bxs_checkbox.svg') }}"
                                        style="width: 30px;" alt="" srcset=""> {{ $value }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="featured-products" class="product-store">
        <div class="container">
            <div class="section-header row">
                <div class="col-md-12">
                    <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Pendaftaran</h2>
                    <table class="text-black table-grid">
                        <tr>
                            <td>
                                <img src="{{ asset('assets/front-page/icon/material-symbols_date-range-sharp.svg') }}"
                                    style="width: 30px;" alt="" srcset=""> Maksimal Daftar
                            </td>
                            <td>{{ date_indo(datetime_to_date($paket->TglMaxBooking)) }}</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('assets/front-page/icon/material-symbols_date-range-sharp.svg') }}"
                                    style="width: 30px;" alt="" srcset=""> Maksimal Bayar
                            </td>
                            <td>{{ date_indo(datetime_to_date($paket->TglMaxBayar)) }}</td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('assets/front-page/icon/material-symbols_date-range-sharp.svg') }}"
                                    style="width: 30px;" alt="" srcset=""> Maksimal Upload Dokumen
                            </td>
                            <td>{{ date_indo(datetime_to_date($paket->TglMaxDokumen)) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section id="featured-products" class="product-store">
        <div class="container">
            <div class="section-header row">
                <div class="col-md-12">
                    <h2 class="section-title mb-1" style="margin: 1rem 0 0 0;">Penerbangan</h2>

                    <table class="text-black table-grid">
                        <tr>
                            <td>
                                <img src="{{ asset('assets/front-page/icon/flight.svg') }}" style="width: 30px;"
                                    alt="" srcset=""> Maskapai
                            </td>
                            <td>
                                {{ $paket->Maskapai }}
                                <img src="{{ asset('assets/maskapai/'.$paket->logo) }}" style="width: 30px;margin-left: 10px"
                                    alt="" srcset="">
                            </td>
                            {{-- <td></td> --}}
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('assets/front-page/icon/airplane_ticket.svg') }}" style="width: 30px;"
                                    alt="" srcset=""> Jenis Penerbangan
                            </td>
                            <td style="min-width: 100px;">{{ $paket->JenisPenerbangan }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('js')
    <script>
        const baseURL = "{{ url('/') }}/";
        $('#HariKe').change(function(e) {
            let selectedOption = $(this).find('option:selected');
            let tanggal = selectedOption.data('tanggal');
            $('#Tanggal').val(tanggal);
            getItenerary(selectedOption.val());
        });

        function getItenerary(hariKe) {

            $('#table-itenerary tbody').html('');
            const encodedIDPaket = "<?= strtr(base64_encode($paket->IDPaket), '+/=', '._-') ?>";
            const encodedIDTravel = "<?= strtr(base64_encode($paket->IDTravel), '+/=', '._-') ?>";
            const url = `${baseURL}Paket/get_itenerary_hari/${encodedIDPaket}/${encodedIDTravel}/${hariKe}`;
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        res.data.forEach(el => {
                            let draw = `<tr>
									<td class="no text-center" style="width: 10%;"><span class="no">1</span>
									</td>
									<td class="" style="width: 20%;">${el.TglMulai} - ${el.TglSelesai}</td>
									<td class="" style="width: 35%;">${el.Agenda}</td>
									<td class="" style="width: 35%;">${el.Lokasi}</td>
								</tr>`;

                            $('#table-itenerary tbody').append(draw);
                            orderNumber();
                        });
                    }


                    if (res.data.length < 1) {
                        draw = `<tr><td colspan="4" class="text-center"> Data tidak ditemukan </td></tr>`;
                        $('#table-itenerary tbody').html(draw);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function orderNumber() {
            no = 1;
            $('#table-itenerary tbody tr').each(function() {
                $(this).find('.no').text(no);
                no++;
            })
        }
    </script>
@endpush
