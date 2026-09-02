@extends('layouts.master')

@section('content')

@push('css')
    <style>
        #map #infowindow-content {
            display: inline;
        }

        .pac-card {
            margin: 10px 10px 0 0;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            background-color: #fff;
            font-family: Roboto;
        }

        #pac-container {
            padding-bottom: 12px;
            margin-right: 12px;
        }

        .pac-controls {
            display: inline-block;
            padding: 5px 11px;
        }

        .pac-controls label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

        #pacinput,
        #pacinputpengambilan {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin: 10px 12px;
            padding: 5px;
            text-overflow: ellipsis;
            width: 250px;
        }

        #pacinput:focus {
            border-color: #4d90fe;
        }
    </style>
@endpush

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <form id="form-default" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">
                                <div class="row">

                                    {{-- Tanggal --}}
                                    <div class="col-12">
                                        <div class="form-input">
                                            <label for="tanggal">Tanggal</label>
                                            <input type="date" class="form-control" name="tanggal" id="tanggal"
                                                value="{{ isset($data->TglMulai) ? \Carbon\Carbon::parse($data->TglMulai)->format('Y-m-d') : '' }}"
                                                required>

                                            <input type="hidden" name="idpaket" value="{{ $idpaket }}">
                                            <input type="hidden" name="idtravel" value="{{ $idtravel }}">
                                            <input type="hidden" name="nourut" value="{{ $data->NoUrut ?? '' }}">
                                        </div>
                                    </div>

                                    {{-- Jam Awal --}}
                                    <div class="col-6 mt-3">
                                        <div class="form-input">
                                            <label for="jamawal">Jam Awal</label>
                                            <input type="time" class="form-control" name="jamawal" id="jamawal"
                                                value="{{ isset($data->TglMulai) ? \Carbon\Carbon::parse($data->TglMulai)->format('H:i') : '' }}"
                                                required>
                                        </div>
                                    </div>

                                    {{-- Jam Akhir --}}
                                    <div class="col-6 mt-3">
                                        <div class="form-input">
                                            <label for="jamakhir">Jam Akhir</label>
                                            <input type="time" class="form-control" name="jamakhir" id="jamakhir"
                                                value="{{ isset($data->TglSelesai) ? \Carbon\Carbon::parse($data->TglSelesai)->format('H:i') : '' }}"
                                                required>
                                        </div>
                                    </div>

                                    {{-- Agenda --}}
                                    <div class="col-12 mt-3">
                                        <div class="form-input">
                                            <label for="agenda">Agenda</label>
                                            <textarea class="form-control" name="agenda" id="agenda" rows="5" placeholder="Masukkan keterangan agenda"
                                                required>{{ $data->Agenda ?? '' }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Lokasi --}}
                                    <div class="col-12 mt-3">
                                        <div class="form-input">
                                            <label for="lokasi">Lokasi Kegiatan</label>
                                            <input type="text" class="form-control" name="lokasi" id="lokasi"
                                                placeholder="Masukkan lokasi kegiatan" value="{{ $data->Lokasi ?? '' }}"
                                                required>
                                        </div>
                                    </div>

                                    {{-- Map --}}
                                    <div class="col-12 mt-3">
                                        <div class="form-input">
                                            <label>Titik Lokasi</label>

                                            <input id="pacinputpengambilan" type="text" placeholder="Cari Lokasi"
                                                style="z-index: 0; position: absolute; left: 113px; top: 300px;">

                                            <div id="mappengambilan" style="height:600px;width:100%;margin-bottom:30px;">
                                            </div>

                                            <input id="latpengambilan" name="koorlat" type="hidden">
                                            <input id="lngpengambilan" name="koorlong" type="hidden">
                                        </div>
                                    </div>

                                    {{-- Action --}}
                                    <div class="col-12 mt-3 text-right">
                                        <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.AUTH_MAPS') }}&libraries=places&callback=initMap">
    </script>

    <script>
        let lat = {{ isset($data) && $data->KoorLat ? $data->KoorLat : 0 }};
        let lng = {{ isset($data) && $data->KoorLong ? $data->KoorLong : 0 }};

        let lat2 = {{ isset($data) && $data->KoorLat ? $data->KoorLat : -7.546839 }};
        let lng2 = {{ isset($data) && $data->KoorLong ? $data->KoorLong : 112.226479 }};

        function initAutocomplete() {
            var map_ambil = new google.maps.Map(document.getElementById('mappengambilan'), {
                center: {
                    lat: lat2,
                    lng: lng2
                },
                zoom: 14,
                mapTypeControl: false,
            });

            var input_ambil = document.getElementById('pacinputpengambilan');
            var searchBox_ambil = new google.maps.places.SearchBox(input_ambil);
            map_ambil.controls[google.maps.ControlPosition.TOP_LEFT].push(input_ambil);

            map_ambil.addListener('bounds_changed', function() {
                searchBox_ambil.setBounds(map_ambil.getBounds());
            });

            var markers_ambil = [];

            var places_ambil = [];
            if (lat != 0 && lng != 0) {
                places_ambil.push({
                    geometry: {
                        location: {
                            lat: lat,
                            lng: lng
                        }
                    }
                });

                markers_ambil.forEach(function(marker_ambil) {
                    marker_ambil.setMap(null);
                });
                markers_ambil = [];

                var bounds_ambil = new google.maps.LatLngBounds();
                for (var j = 0, place_ambil; place_ambil = places_ambil[j]; j++) {
                    var url_image = `${baseURL}assets/marker_small.png`;
                    var image_ambil = {
                        url: place_ambil.icon,
                        url: url_image,
                        size: new google.maps.Size(100, 100),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 45),
                        scaledSize: new google.maps.Size(34, 45)
                    };
                    var marker_ambil = new google.maps.Marker({
                        draggable: true,
                        map: map_ambil,
                        icon: image_ambil,
                        title: place_ambil.name,
                        position: place_ambil.geometry.location
                    });

                    console.log(JSON.stringify(place_ambil.geometry.location));

                    document.getElementById('latpengambilan').value = marker_ambil.getPosition().lat().toFixed(6);
                    document.getElementById('lngpengambilan').value = marker_ambil.getPosition().lng().toFixed(6);
                    google.maps.event.addListener(marker_ambil, 'dragend', function(e) {
                        displayPosition_ambil(this.getPosition());
                    });
                    google.maps.event.addListener(marker_ambil, 'click', function(e) {
                        displayPosition_ambil(this.getPosition());
                    });
                    markers_ambil.push(marker_ambil);
                    bounds_ambil.extend(place_ambil.geometry.location);
                }
            }
            searchBox_ambil.addListener('places_changed', function() {
                var places_ambil = searchBox_ambil.getPlaces();

                if (places_ambil.length == 0) {
                    return;
                }

                markers_ambil.forEach(function(marker_ambil) {
                    marker_ambil.setMap(null);
                });
                markers_ambil = [];

                var bounds_ambil = new google.maps.LatLngBounds();
                for (var j = 0, place_ambil; place_ambil = places_ambil[j]; j++) {
                    var url_image = `${baseURL}assets/marker_small.png`;
                    var image_ambil = {
                        url: place_ambil.icon,
                        url: url_image,
                        size: new google.maps.Size(100, 100),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 45),
                        scaledSize: new google.maps.Size(34, 45)
                    };
                    var marker_ambil = new google.maps.Marker({
                        draggable: true,
                        map: map_ambil,
                        icon: image_ambil,
                        title: place_ambil.name,
                        position: place_ambil.geometry.location
                    });


                    document.getElementById('latpengambilan').value = marker_ambil.getPosition().lat().toFixed(6);
                    document.getElementById('lngpengambilan').value = marker_ambil.getPosition().lng().toFixed(6);
                    google.maps.event.addListener(marker_ambil, 'dragend', function(e) {
                        displayPosition_ambil(this.getPosition());
                    });

                    google.maps.event.addListener(marker_ambil, 'click', function(e) {
                        displayPosition_ambil(this.getPosition());
                    });
                    markers_ambil.push(marker_ambil);
                    bounds_ambil.extend(place_ambil.geometry.location);
                }

                map_ambil.fitBounds(bounds_ambil);
            });

            google.maps.event.addListener(map_ambil, 'bounds_changed', function() {
                var bounds_ambil = map_ambil.getBounds();
                searchBox_ambil.setBounds(bounds_ambil);
            });


            function displayPosition_ambil(post) {
                document.getElementById('latpengambilan').value = post.lat();
                document.getElementById('lngpengambilan').value = post.lng();
            }
        }

        $("#form-default").submit(function(e) {
            e.preventDefault()
            const formData = new FormData($("#form-default")[0]);
            $.ajax({
                url: `${baseURL}admin/Paket/simpan_itenerary`,
                type: 'POST',
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                data: formData,
                success: function(res) {
                    res = JSON.parse(res)
                    if (res.status) {
                        window.location.href = res.redirect
                        toast('success', res.message)
                    } else {
                        toast('error', res.message)
                    }
                },
                error: function(xhr, status, error) {
                    toast('error', 'Sistem Error')
                }
            });
        });
    </script>
@endpush
