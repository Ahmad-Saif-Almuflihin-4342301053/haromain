@extends('layouts.master')

@section('content')

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" integrity="sha512-In/+MILhf6UMDJU4ZhDL0R0fEpsp4D3Le23m6+ujDWXwl3whwpucJG1PEmI3B07nyJx+875ccs+yX2CqQJUxUw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs4.min.css" integrity="sha512-rDHV59PgRefDUbMm2lSjvf0ZhXZy3wgROFyao0JxZPGho3oOuWejq/ELx0FOZJpgaE5QovVtRN65Y3rrb7JhdQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote.min.js" integrity="sha512-07bR+AJ2enmNU5RDrZkqMfVq06mQHgFxcmWN/hNSNY4E5SgYNOmTVqo/HCzrSxBhWU8mx3WB3ZJOixA9cRnCdA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.AUTH_MAPS') }}&libraries=places&callback=initMap"></script>
@endpush

@php
    $editMode = isset($data) && !empty($data);
    $locationData = $editMode ? $data : null;
@endphp

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $editMode ? 'Edit' : 'Tambah' }} Lokasi</h4>

                        <form id="form-tambah" method="post" enctype="multipart/form-data">
                            @csrf

                            @if ($editMode)
                                <input type="hidden" name="id" value="{{ $locationData->IDKonten }}">
                                <input type="hidden" name="old_GambarKonten" value="{{ $locationData->GambarKonten }}">
                            @endif

                            @if (Auth::user()->LevelID == 1)
                                <div class="form-group">
                                    <label for="IDTravel">Perusahaan Travel</label>
                                    <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                        <option value="">Pilih Perusahaan Travel</option>
                                        @foreach ($travel as $row)
                                            <option value="{{ $row->IDTravel }}"
                                                {{ $editMode && ($locationData->IDTravel ?? '') == $row->IDTravel ? 'selected' : '' }}>
                                                {{ $row->NamaTravel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="JudulKonten">Nama Lokasi</label>
                                <input type="text"
                                       class="form-control"
                                       id="JudulKonten"
                                       name="JudulKonten"
                                       placeholder="Masukkan nama lokasi"
                                       value="{{ $editMode ? $locationData->JudulKonten : '' }}"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="IDKatKonten">Kategori</label>
                                <select class="form-control" id="IDKatKonten" name="IDKatKonten" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($kategori_konten as $kat)
                                        <option value="{{ $kat->IDKatKonten }}"
                                            {{ $editMode && $locationData->IDKatKonten == $kat->IDKatKonten ? 'selected' : '' }}>
                                            {{ $kat->NamaKategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="IDKota">Kota</label>
                                @if (Auth::user()->LevelID == 1)
                                    <select class="form-control select2" id="IDKota" name="IDKota" required {{ (!$editMode || empty($locationData->IDTravel)) ? 'disabled' : '' }}>
                                        <option value="">{{ ($editMode && !empty($locationData->IDTravel)) ? 'Pilih Kota' : 'Pilih Travel Terlebih Dahulu' }}</option>
                                        @foreach ($kota as $k)
                                            <option value="{{ $k->IDKota }}"
                                                {{ $editMode && $locationData->IDKota == $k->IDKota ? 'selected' : '' }}>
                                                {{ $k->NamaKota }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Pilihan kota disesuaikan dengan travel yang dipilih.</small>
                                @else
                                    <select class="form-control select2" id="IDKota" name="IDKota" required>
                                        <option value="">Pilih Kota</option>
                                        @foreach ($kota as $k)
                                            <option value="{{ $k->IDKota }}"
                                                {{ $editMode && $locationData->IDKota == $k->IDKota ? 'selected' : '' }}>
                                                {{ $k->NamaKota }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="GambarKonten">Gambar Lokasi</label>
                                <input type="file"
                                       class="dropify"
                                       id="GambarKonten"
                                       name="GambarKonten"
                                       data-height="300"
                                       data-allowed-file-extensions="jpg jpeg png"
                                       data-default-file="{{ $editMode && !empty($locationData->GambarKonten)
                                            ? asset('assets/konten/' . $locationData->GambarKonten)
                                            : '' }}"
                                       {{ !$editMode ? 'required' : '' }}>
                            </div>

                            <div class="form-group">
                                <label for="IsiKonten">Deskripsi</label>
                                <textarea class="summernote"
                                          id="IsiKonten"
                                          name="IsiKonten">{{ $editMode ? $locationData->IsiKonten : '' }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Koordinat Lokasi</label>
                                <div id="bungkusII">
                                    <input class="form-control mb-3"
                                           id="input_place2"
                                           type="text"
                                           placeholder="Masukkan Lokasi Maps">
                                </div>

                                <div id="map" style="width:100%;height:380px;margin-bottom:20px;"></div>

                                <input type="hidden"
                                       class="form-control"
                                       id="Lat"
                                       name="Lat"
                                       value="{{ $editMode ? $locationData->Lat : '' }}"
                                       required>

                                <input type="hidden"
                                       class="form-control"
                                       id="Lng"
                                       name="Lng"
                                       value="{{ $editMode ? $locationData->Lng : '' }}"
                                       required>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                            <a href="{{ route('admin.location') }}" class="btn btn-light">Batal</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')
<script>
    let map;
    let marker;
    let searchBox;

    function initMap() {
        const defaultLat = {{ $editMode && !empty($locationData->Lat) ? $locationData->Lat : -7.556757269230274 }};
        const defaultLng = {{ $editMode && !empty($locationData->Lng) ? $locationData->Lng : 112.22636016751586 }};

        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 13
        });

        marker = new google.maps.Marker({
            position: { lat: defaultLat, lng: defaultLng },
            map: map,
            draggable: true
        });

        const input = document.getElementById('input_place2');
        searchBox = new google.maps.places.SearchBox(input);

        map.addListener('bounds_changed', function () {
            searchBox.setBounds(map.getBounds());
        });

        marker.addListener('dragend', function () {
            const position = marker.getPosition();
            $('#Lat').val(position.lat());
            $('#Lng').val(position.lng());
        });

        searchBox.addListener('places_changed', function () {
            const places = searchBox.getPlaces();
            if (!places.length) return;

            const place = places[0];
            if (!place.geometry || !place.geometry.location) return;

            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);

            $('#Lat').val(place.geometry.location.lat());
            $('#Lng').val(place.geometry.location.lng());
        });
    }

    $(document).ready(function () {
        $('.dropify').dropify({
            messages: {
                default: 'Drag dan drop gambar di sini atau klik',
                replace: 'Drag dan drop atau klik untuk mengganti gambar',
                remove: 'Hapus',
                error: 'Oops, terjadi kesalahan.'
            }
        });

        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        @if (Auth::user()->LevelID == 1)
        $('#IDTravel').on('change', function () {
            let idTravel = $(this).val();
            let $kotaSelect = $('#IDKota');
            $kotaSelect.empty();

            if (!idTravel) {
                $kotaSelect.attr('disabled', true);
                $kotaSelect.append('<option value="">Pilih Travel Terlebih Dahulu</option>');
                return;
            }

            $kotaSelect.removeAttr('disabled');
            $kotaSelect.append('<option value="">Memuat data kota...</option>');

            $.ajax({
                url: "{{ url('admin/location/getKotaByTravel') }}/" + idTravel,
                type: "GET",
                success: function (res) {
                    $kotaSelect.empty();
                    $kotaSelect.append('<option value="">Pilih Kota</option>');
                    if (res && res.length > 0) {
                        res.forEach(function (item) {
                            $kotaSelect.append(`<option value="${item.IDKota}">${item.NamaKota}</option>`);
                        });
                    } else {
                        $kotaSelect.append('<option value="">Tidak ada data kota untuk travel ini</option>');
                    }
                },
                error: function () {
                    $kotaSelect.empty();
                    $kotaSelect.append('<option value="">Gagal memuat data kota</option>');
                }
            });
        });
        @endif

        $('#form-tambah').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.location.simpan') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('admin.location') }}";
                        });
                    } else {
                        toast('error', response.message);
                    }
                },
                error: function () {
                    toast('error', 'Gagal menyimpan data');
                }
            });
        });
    });
</script>


@endpush
