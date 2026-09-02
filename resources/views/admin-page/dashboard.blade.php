@extends('layouts.master')

@section('content')

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <!-- first row starts here -->
        <div class="row">
            <div class="col-xl-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body bg-primary" style="border-radius: 15px 15px 0 0;">
                        <div class="row">
                            <div class="col-md-4 p-3 pl-4">
                                <div class="h4">Jumlah Agen</div>
                                <div class="d-flex">
                                    <img src="{{ asset('assets/admin-page/icon/mdi_face-agent.svg') }}" class="mr-2" alt="">
                                    <span class="h2 text-white">{{ $totalAgen }}</span>
                                </div>
                            </div>
                            <div class="col-md-4 p-3 border-left-lg border-right-lg pl-4">
                                <div class="h4">Jumlah Jama'ah</div>
                                <div class="d-flex">
                                    <div class="w-50">
                                        <img src="{{ asset('assets/admin-page/icon/fluent_people-community-48-regular.svg') }}" class="mr-2" alt="">
                                        <span class="h2 text-white">{{ $totalJamaah }}</span>
                                    </div>
                                    <div class="w-50 text-right">
                                        <div class="align-self-center bg-brown text-white rounded-pill px-3 py-1 d-inline">
                                            {{ $selisihBooking > 0 ? '+' : '' }}{{ $selisihBooking }}
                                            <img class="{{ $selisihBooking < 0 ? 'rotate-90' : '' }}" src="{{ asset('assets/admin-page/icon/ph_trend-up-thin.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 p-3 pl-4">
                                <div class="h4">Jumlah Jama'ah Tracking</div>
                                <div class="d-flex">
                                    <div class="w-50">
                                        <img src="{{ asset('assets/admin-page/icon/fluent_people-community-48-regular.svg') }}" class="mr-2" alt="">
                                        <span class="h2 text-white">{{ $totalJamaahTracking }}</span>
                                    </div>
                                    <div class="w-50 text-right">
                                        <div class="align-self-center bg-brown text-white rounded-pill px-3 py-1 d-inline">
                                            {{ $selisihTracking > 0 ? '+' : '' }}{{ $selisihTracking }}
                                            <img class="{{ $selisihTracking < 0 ? 'rotate-90' : '' }}" src="{{ asset('assets/admin-page/icon/ph_trend-up-thin.svg') }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mx-lg-5" style="margin-bottom: -8rem;">
                            <div class="col-md-4 mt-3">
                                <div class="card p-3 rounded-lg">
                                    <div class="text-primary h4">Persiapan</div>
                                    <div class="d-flex">
                                        <div class="w-75 align-self-end">
                                            <span class="h3 text-primary">{{ $totalPersiapan }}</span> Paket
                                        </div>
                                        <div class="w-25">
                                            <img class="w-100" style="max-width: 50px;" src="{{ asset('assets/admin-page/icon/uil_package.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="align-self-baseline bg-brown text-white rounded-pill px-3 py-1 d-inline">
                                        {{ $selisihPersiapan > 0 ? '+' : '' }}{{ $selisihPersiapan }}
                                        <img class="{{ $selisihPersiapan < 0 ? 'rotate-90' : '' }}" src="{{ asset('assets/admin-page/icon/ph_trend-up-thin.svg') }}" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mt-3">
                                <div class="card p-3 rounded-lg">
                                    <div class="text-primary h4">Perjalanan</div>
                                    <div class="d-flex">
                                        <div class="w-75 align-self-end">
                                            <span class="h3 text-primary">{{ $totalPerjalanan }}</span> Paket
                                        </div>
                                        <div class="w-25">
                                            <img class="w-100" style="max-width: 50px;" src="{{ asset('assets/admin-page/icon/uil_package.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="align-self-baseline bg-brown text-white rounded-pill px-3 py-1 d-inline">
                                        {{ $selisihPerjalanan > 0 ? '+' : '' }}{{ $selisihPerjalanan }}
                                        <img class="{{ $selisihPerjalanan < 0 ? 'rotate-90' : '' }}" src="{{ asset('assets/admin-page/icon/ph_trend-up-thin.svg') }}" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mt-3">
                                <div class="card p-3 rounded-lg">
                                    <div class="text-primary h4">Selesai</div>
                                    <div class="d-flex">
                                        <div class="w-75 align-self-end">
                                            <span class="h3 text-primary">{{ $totalSelesai }}</span> Paket
                                        </div>
                                        <div class="w-25">
                                            <img class="w-100" style="max-width: 50px;" src="{{ asset('assets/admin-page/icon/uil_package.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="align-self-baseline bg-brown text-white rounded-pill px-3 py-1 d-inline">
                                        {{ $selisihSelesai > 0 ? '+' : '' }}{{ $selisihSelesai }}
                                        <img class="{{ $selisihSelesai < 0 ? 'rotate-90' : '' }}" src="{{ asset('assets/admin-page/icon/ph_trend-up-thin.svg') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" style="padding-top:10rem">
                        <h4>Grafik Perjalanan Bulanan Tahun {{ date('Y') }}</h4>
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {

        // Panggil route Laravel untuk data grafik
        $.get("{{ route('admin.dashboard.grafikPerjalananBulanan') }}", function(data) {
            renderChart(data);
        });

        function renderChart(monthlyData) {
            let options = {
                series: [{
                    name: 'Total Keberangkatan',
                    data: monthlyData
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        borderRadius: 10,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                xaxis: {
                    categories: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                    position: 'bottom',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    crosshairs: {
                        fill: {
                            type: 'gradient',
                            gradient: {
                                colorFrom: '#D8E3F0',
                                colorTo: '#BED1E6',
                                stops: [0, 100],
                                opacityFrom: 0.4,
                                opacityTo: 0.5,
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                    }
                },
                yaxis: {
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        show: true,
                    },
                    title: {
                        text: "Paket"
                    },
                },
            };

            let chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        }
    });
</script>

@endpush

