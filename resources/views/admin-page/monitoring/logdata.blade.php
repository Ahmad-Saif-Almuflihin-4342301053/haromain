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
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <select id="selectJamaah" class="form-control">
                            <option value="">Pilih Nama Jamaah</option>
                            @foreach ($groupedData as $jamaah)
                                <option value="{{ $jamaah['IDPerson'] }}">{{ $jamaah['NamaJamaah'] }}</option>
                            @endforeach
                        </select>
                        <br>
                        <div class="d-flex justify-content-between flex-wrap">
                            <div style="height:700px; width: 100%;">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.AUTH_MAPS') }}&language=id&region=ID&callback=init">
</script>

<script type="text/javascript">
    function init() {
        window.onload = function() {
            let lokasi = @json($groupedData);
            let infowindow = new google.maps.InfoWindow();

            let pos = new google.maps.LatLng(0.02468464083410471, 118.92305045216865);

            let option = {
                center: pos,
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            let map = new google.maps.Map(document.getElementById('map'), option);
            let bounds = new google.maps.LatLngBounds();
            let markers = [];
            let polylines = [];

            const markerImage = {
                path: "M-10,0a10,10 0 1,0 20,0a10,10 0 1,0 -20,0",
                anchor: new google.maps.Point(12, 17),
                fillOpacity: 1,
                fillColor: "red",
                strokeWeight: 2,
                strokeColor: "white",
                scale: 0.8,
                labelOrigin: new google.maps.Point(12, 9)
            };

            function renderMarkers(dataJamaah) {
                clearMap();

                let pathCoordinates = [];
                bounds = new google.maps.LatLngBounds();

                dataJamaah.Lokasi.forEach((entry) => {
                    let pos = new google.maps.LatLng(entry.KoorLat, entry.KoorLong);
                    pathCoordinates.push(pos);

                    let marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        icon: {
                            ...markerImage,
                            fillColor: dataJamaah.colour
                        }
                    });

                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.setContent(`Nama: ${dataJamaah.NamaJamaah}<br>Waktu: ${entry.LastModified}`);
                        infowindow.open(map, marker);
                    });

                    markers.push(marker);
                    bounds.extend(pos); // extend bounds
                });

                let routePath = new google.maps.Polyline({
                    path: pathCoordinates,
                    geodesic: true,
                    strokeColor: dataJamaah.colour,
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                routePath.setMap(map);
                polylines.push(routePath);

                map.fitBounds(bounds);
                if (dataJamaah.Lokasi.length === 1) {
                    map.setZoom(15);
                }
            }

            function clearMap() {
                markers.forEach(marker => marker.setMap(null));
                markers = [];

                polylines.forEach(polyline => polyline.setMap(null));
                polylines = [];
            }

            document.getElementById('selectJamaah').addEventListener('change', function() {
                let selectedID = this.value;

                if (selectedID) {
                    let selectedData = lokasi.find(item => item.IDPerson === selectedID);
                    if (selectedData) {
                        renderMarkers(selectedData);
                    }
                } else {
                    clearMap();
                }
            });
        }
    }
</script>
@endpush
