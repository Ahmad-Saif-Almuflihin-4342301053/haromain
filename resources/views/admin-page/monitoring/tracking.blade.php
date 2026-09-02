@extends('layouts.master')

@section('content')

@push('css')
<style>
    .btn-tab {
        border-radius: 0;
    }

    .tab-active {
        border-bottom: 3px solid #e5a11d;
        color: black !important;
    }

    #map {
        height: 600px;
    }
</style>
@endpush

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <a href="{{ url('admin/Monitoring') }}" class="btn btn-primary btn-sm mb-2">
            <i style="font-size: 12px;" class="fa fa-arrow-left"></i> Kembali
        </a>
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            <form action="" class="col-md-12 mb-3">
                                <input type="hidden" id="id" name="id" value="{{ base64_encode($IDPaket) }}">
                                <button type="submit" value="Detail" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Detail' ? 'tab-active' : '' }}">Detil Paket</button>
                                <button type="submit" value="Tracking" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Tracking' ? 'tab-active' : '' }}">Live Tracking</button>
                                <button type="submit" value="Jamaah" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Jamaah' ? 'tab-active' : '' }}">Jamaah</button>
                                <button type="submit" value="Pembayaranjamaah" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Pembayaranjamaah' ? 'tab-active' : '' }}">Pembayaran</button>
                                <button type="submit" value="Pembayaranharomain" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Pembayaranharomain' ? 'tab-active' : '' }}">Haromain</button>
                            </form>

                            <div style="height:700px; width: 100%;">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- content-wrapper ends -->
</div>

@endsection
@push('js')
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.AUTH_MAPS') }}&language=id&region=ID&callback=init">
</script>

<script type="text/javascript">
    function init() {
        window.onload = function() {
            let lokasi = @json($tracking);
            let infowindow = new google.maps.InfoWindow();

            let defaultPos = new google.maps.LatLng(0.02468464083410471, 118.92305045216865);
            let mapOptions = {
                center: defaultPos,
                zoom: 0,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            let map = new google.maps.Map(document.getElementById('map'), mapOptions);

            let bounds = new google.maps.LatLngBounds();
            let markers = [];
            const baseIMG = "{{ url('assets/foto-profil/') }}";

            lokasi.forEach((item) => {
                if (item.KoorLat && item.KoorLong) {
                    let pos = new google.maps.LatLng(item.KoorLat, item.KoorLong);
                    
                    let markerOptions = {
                        position: pos,
                        map: map,
                        data: item
                    };

                    let iconConfig = {};

                    if (item.IsBantuan === true) {
                        iconConfig = {
                            url: "{{ url('assets/icon/pin_bantuan.png') }}",
                            scaledSize: new google.maps.Size(50, 50) // Ukuran khusus untuk Bantuan
                        };
                    } else {
                        iconConfig = {
                            url: "{{ url('assets/icon/pin_jamaah.png') }}",
                            scaledSize: new google.maps.Size(40, 50) // Ukuran khusus untuk Jamaah Normal
                        };
                    }

                    markerOptions.icon = iconConfig;

                    let marker = new google.maps.Marker(markerOptions);

                    google.maps.event.addListener(marker, 'click', function() {
                        let img = item.FotoProfil ? baseIMG + item.FotoProfil : "{{ url('assets/admin-page/images/default-avatar.jpg') }}";
                        let template = `
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <img src="${img}" style="width: 100px;">
                                </div>
                                <div class="col-md-12 text-center">
                                    <h5 class="fs-6">${item?.NamaJamaah || ''}</h5>
                                    <h5 class="fs-6 text-primary">NIK ${item?.NIK || ''}</h5>
                                    <h6 class="fs-6 text-small text-muted">Terakhir diperbarui: ${item?.LastModified || ''}</h6>
                                </div>
                            </div>
                        `;
                        infowindow.setContent(template);
                        infowindow.open(map, this);
                    });

                    markers.push(marker);
                    bounds.extend(pos);
                }
            });

            map.fitBounds(bounds);
            map.panToBounds(bounds);
        }
    }
</script>
@endpush
