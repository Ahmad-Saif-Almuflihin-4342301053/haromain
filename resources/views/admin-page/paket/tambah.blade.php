@extends('layouts.master')

@section('content')
    @php
        $edit = isset($paket->IDPaket) && !empty($paket->IDPaket);
    @endphp


    @push('css')
        <style>
            .pac-container {
                z-index: 999999;
                display: block;
            }

            .progress {
                display: none !important;
            }

            .image-crop-modal {
                display: none;
                position: fixed;
                z-index: 9999 !important;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8) !important;
                overflow: auto;
            }

            .image-crop-modal-content {
                background-color: #ffffff !important;
                margin: 3% auto;
                padding: 25px;
                border-radius: 8px;
                width: 85%;
                max-width: 800px;
                max-height: 85vh;
                overflow-y: auto;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                position: relative;
            }

            .image-crop-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 1px solid #ddd;
            }

            .image-crop-modal-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin: 0;
            }

            .image-crop-close {
                font-size: 28px;
                font-weight: bold;
                color: #aaa;
                cursor: pointer;
                border: none;
                background: none;
            }

            .image-crop-close:hover,
            .image-crop-close:focus {
                color: #000;
            }

            .image-crop-container-wrapper {
                max-height: 400px;
                margin-bottom: 20px;
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
            }

            #cropperImage {
                max-width: 100%;
                max-height: 400px;
                display: block;
                margin: 0 auto;
            }

            .image-crop-modal-footer {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
            }

            .image-preview {
                max-width: 150px;
                max-height: 150px;
                border: 2px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
                transition: transform 0.2s;
            }

            .image-preview:hover {
                transform: scale(1.05);
                border-color: #007bff;
            }

            .quality-control {
                margin-bottom: 15px;
                padding: 15px;
                background-color: #e9ecef;
                border-radius: 4px;
                border: 1px solid #dee2e6;
            }

            .quality-control label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #495057;
            }

            .quality-slider {
                width: 100%;
                margin-bottom: 10px;
                height: 6px;
            }

            .quality-value {
                font-weight: bold;
                color: #007bff;
                font-size: 1.1em;
            }

            /* Fix button styling */
            .image-crop-modal .btn {
                padding: 10px 20px;
                border-radius: 4px;
                border: none;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s;
                font-weight: 500;
            }

            .image-crop-modal .btn-secondary {
                background-color: #6c757d !important;
                color: white !important;
            }

            .image-crop-modal .btn-secondary:hover {
                background-color: #5a6268 !important;
            }

            .image-crop-modal .btn-primary {
                background-color: #007bff !important;
                color: white !important;
            }

            .image-crop-modal .btn-primary:hover {
                background-color: #0056b3 !important;
            }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    @endpush

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <form id="form-default" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">
                                <div class="d-flex justify-content-between flex-wrap">

                                    {{-- Nama Paket --}}
                                    <div class="form-group col-md-6">
                                        <label>Nama Paket</label>
                                        <input type="text" name="NamaPaket" class="form-control"
                                            placeholder="Masukkan Nama Paket" value="{{ $paket->NamaPaket ?? '' }}"
                                            required>
                                    </div>

                                    {{-- Kategori Paket --}}
                                    <div class="form-group col-md-6">
                                        <label>Kategori Paket</label>
                                        <select name="KategoriPaket" class="form-control select2" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($KategoriPaket as $kat)
                                                <option value="{{ $kat }}"
                                                    {{ ($paket->KategoriPaket ?? '') === $kat ? 'selected' : '' }}>
                                                    {{ $kat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Tanggal Berangkat --}}
                                    <div class="form-group col-md-6">
                                        <label>Keberangkatan</label>
                                        <input type="text" name="TglBerangkat" id="TglBerangkat"
                                            class="form-control datepicker"
                                            value="{{ isset($paket->TglBerangkat) ? datetime_to_date($paket->TglBerangkat) : '' }}" required>
                                    </div>

                                    {{-- Tanggal Pulang --}}
                                    <div class="form-group col-md-6">
                                        <label>Kepulangan</label>
                                        <input type="text" name="TglPulang" id="TglPulang"
                                            class="form-control datepicker"
                                            value="{{ isset($paket->TglPulang) ? datetime_to_date($paket->TglPulang) : '' }}" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Maskapai</label>
                                        <select name="Maskapai" class="form-control select2" required>
                                            <option value="">Pilih Maskapai</option>
                                            @foreach ($Maskapai as $mskp)
                                                <option value="{{ $mskp->Maskapai }}"
                                                    {{ ($paket->Maskapai ?? '') === $mskp->Maskapai ? 'selected' : '' }}>
                                                    {{ $mskp->Maskapai }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Jenis Penerbangan</label>
                                        <select name="JenisPenerbangan" class="form-control select2" required>
                                            <option value="">Pilih Jenis Penerbangan</option>
                                            @foreach ($JenisPenerbangan as $jp)
                                                <option value="{{ $jp }}"
                                                    {{ ($paket->JenisPenerbangan ?? '') === $jp ? 'selected' : '' }}>
                                                    {{ $jp }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Berangkat Dari</label>
                                        <input type="text" name="BerangkatDari" class="form-control"
                                            placeholder="Masukkan Berangkat Dari" value="{{ $paket->BerangkatDari ?? '' }}"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Landing Di</label>
                                        <input type="text" name="LandingDi" class="form-control"
                                            placeholder="Masukkan Landing Di" value="{{ $paket->LandingDi ?? '' }}"
                                            required>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div class="form-group col-md-6">
                                        <label>Deskripsi Paket</label>
                                        <textarea name="Deskripsi" class="form-control" rows="5">{{ $paket->Deskripsi ?? '' }}</textarea>
                                    </div>

                                    {{-- Lama Hari --}}
                                    <div class="form-group col-md-6">
                                        <label>Lama Hari</label>
                                        <input type="number" name="LamaHari" id="LamaHari" class="form-control" readonly
                                            value="{{ $paket->LamaHari ?? '' }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="">Kota Tujuan</label>
                                        <div class="input-group">
                                            <input type="text" id="InputKota" class="form-control"
                                                placeholder="Tambah kota tujuan">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary " type="button" id="add-kota">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="list-kota" class="mt-2">

                                        </div>
                                    </div>

                                    {{-- Kuota --}}
                                    <div class="form-group col-md-6">
                                        <label>Kuota Paket</label>
                                        <input type="number" name="KuotaPaket" class="form-control"
                                            value="{{ $paket->KuotaPaket ?? '' }}">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Maksimal Booking</label>
                                        <input type="text" name="TglMaxBooking"
                                            value="{{ old('TglMaxBooking', isset($paket) ? datetime_to_date($paket->TglMaxBooking) : '') }}"
                                            placeholder="Masukkan Tanggal Maksimal Booking" class="form-control datepicker">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Maksimal Bayar</label>
                                        <input type="text" name="TglMaxBayar"
                                            value="{{ old('TglMaxBayar', isset($paket) ? datetime_to_date($paket->TglMaxBayar) : '') }}"
                                            placeholder="Masukkan Tanggal Maksimal Bayar" class="form-control datepicker">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Include Fasilitas</label>
                                        <div class="input-group">
                                            <input type="text" id="InputInFasilitas" class="form-control"
                                                placeholder="Include Fasilitas">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary" type="button" id="add-infasilitas">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="list-infasilitas" class="mt-2"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Exclude Fasilitas</label>
                                        <div class="input-group">
                                            <input type="text" id="InputExFasilitas" class="form-control"
                                                placeholder="Exclude Fasilitas">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary" type="button"
                                                    id="add-exfasilitas">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="list-exfasilitas" class="mt-2"></div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Maksimal Upload Dokumen</label>
                                        <input type="text" name="TglMaxDokumen"
                                            value="{{ old('TglMaxDokumen', $paket->TglMaxDokumen ?? '') }}"
                                            placeholder="Masukkan Tanggal Maksimal Upload Dokumen"
                                            class="form-control datepicker">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Biaya Quad</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">Rp</span>
                                            <input type="text" name="BiayaQuad" class="form-control nominal"
                                                value="{{ $paket->BiayaQuad ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Biaya Triple</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">Rp</span>
                                            <input type="text" name="BiayaTriple" class="form-control nominal"
                                                value="{{ $paket->BiayaTriple ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Biaya Double</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">Rp</span>
                                            <input type="text" name="BiayaDouble" class="form-control nominal"
                                                value="{{ $paket->BiayaDouble ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label for="">Hotel</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm mt-3 table-bordered border" id="tabel-dokumen">
                                                <thead>
                                                    <th>Nama Hotel</th>
                                                    <th>Bintang</th>
                                                    <th>Tanggal Masuk</th>
                                                    <th>Tanggal Keluar</th>
                                                    <th>Latitude</th>
                                                    <th>Longitude</th>
                                                    <th>Alamat</th>
                                                    <th>Aksi</th>
                                                </thead>
                                                <tbody id="tbody-hotel">

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="8">
                                                            <button type="button" id="btn-add-hotel"
                                                                class="btn btn-block btn-github btn-sm">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="">Dokumen Persyaratan</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm mt-3 table-bordered border" id="tabel-dokumen">
                                            <thead>
                                                <th>Nama Dokumen</th>
                                                <th>Keterangan Dokumen</th>
                                                <th>Aksi</th>
                                            </thead>
                                            <tbody id="tbody-dokumen">

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <button type="button" id="btn-add-dokumen"
                                                            class="btn btn-block btn-github btn-sm">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="">Gambar Paket</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm mt-3 table-bordered border" id="tabel-gambar">
                                            <thead>
                                                <th>#</th>
                                                <th>Upload Gambar</th>
                                                <th>Aksi</th>
                                            </thead>
                                            <tbody id="tbody-gambar">
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="7">
                                                        <button type="button" id="btn-add-gambar"
                                                            class="btn btn-block btn-github btn-sm">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right p-2">
                                <input type="hidden" name="id" value="{{ $paket->IDPaket ?? '' }}">

                                <button type="button" onclick="history.back()" class="btn btn-outline-primary mr-2">
                                    Batal
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cropperModal" class="image-crop-modal">
        <div class="image-crop-modal-content">
            <div class="image-crop-modal-header">
                <h5 class="image-crop-modal-title">Crop Gambar</h5>
                <button class="image-crop-close" onclick="closeCropperModal()">&times;</button>
            </div>
            <div class="cropper-modal-body">
                <!-- Quality Control -->
                <div class="quality-control">
                    <label for="qualitySlider">
                        Kualitas Gambar: <span id="qualityValue" class="quality-value">95%</span>
                    </label>
                    <input type="range" id="qualitySlider" class="quality-slider" min="50" max="100"
                        value="95" step="5">
                    <small style="color: #666;">
                        Tip: 95% = kualitas tinggi dengan ukuran file wajar | 100% = kualitas maksimal
                    </small>
                </div>

                <!-- Cropper Container -->
                <div class="image-crop-container-wrapper">
                    <img id="cropperImage" src="" alt="Image for cropping">
                </div>
            </div>
            <div class="image-crop-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCropperModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="cropImage()">Crop & Simpan</button>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">Pilih titik koordinat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label class="control-label font-weight-bold font-utama">Lokasi Maps</label>
                    <div id="bungkusII" class="d-none">
                        <input class="form-control " id="input_place2" style="width: 50%; margin-top: 8px;"
                            type="text" placeholder="Masukkan Lokasi Maps" value="">
                    </div>
                    <div id="map" style="width:100%;height:380px;"></div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Latitude</label>
                                <input id="latpengambilan" name="KoorLat" type="text" value="-7.556757269230274"
                                    class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="">Longitude</label>
                                <input id="lngpengambilan" name="KoorLong" type="text" value="112.22636016751586"
                                    class="form-control" readonly>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-submit-map">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <script>
        let cropper = null;
        let currentInputId = null;
        let currentPreviewRow = null;

        $(document).ready(function() {
            $('#qualitySlider').on('input', function() {
                $('#qualityValue').text(this.value + '%');
            });
        });

        function initializeCropper(inputId) {
            const $input = $(inputId);
            if ($input.length === 0) return;

            const fileInputHtml =
                `<input type="file" accept="image/png, image/jpeg, image/jpg" class="form-control cropper-file-input">`;
            $input.after(fileInputHtml);

            $input.next('.cropper-file-input').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (!file.type.match('image.*')) {
                    alert('Harap pilih file gambar (PNG, JPG, atau JPEG)');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    currentInputId = inputId;
                    currentPreviewRow = $input.closest('tr');
                    openCropperModal(event.target.result);
                };
                reader.readAsDataURL(file);
            });
        }

        function openCropperModal(imageSrc) {
            $('#cropperModal').show();
            $('#cropperImage').attr('src', imageSrc);

            if (cropper) {
                cropper.destroy();
            }

            setTimeout(function() {
                cropper = new Cropper(document.getElementById('cropperImage'), {
                    aspectRatio: 1,
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    responsive: true,
                    checkOrientation: true,
                    minContainerWidth: 200,
                    minContainerHeight: 200,
                    minCropBoxWidth: 100,
                    minCropBoxHeight: 100
                });
            }, 100);
        }

        // Close Cropper Modal
        function closeCropperModal() {
            $('#cropperModal').hide();
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        // Crop Image and Save
        function cropImage() {
            if (!cropper) return;

            const quality = $('#qualitySlider').val() / 100;

            // Get cropped canvas with high quality
            const canvas = cropper.getCroppedCanvas({
                width: 1200,
                height: 1200,
                minWidth: 800,
                minHeight: 800,
                maxWidth: 2400,
                maxHeight: 2400,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            if (!canvas) {
                alert('Gagal melakukan crop gambar');
                return;
            }

            // Convert to baseURl64 with quality
            canvas.toBlob(function(blob) {
                const reader = new FileReader();
                reader.onloadend = function() {
                    const baseURl64data = reader.result;

                    // Set to hidden input
                    $(currentInputId).val(baseURl64data);

                    // Update preview
                    if (currentPreviewRow) {
                        const previewHtml = `<button type="button" class="btn p-0 show-image" data-foto="${baseURl64data}">
                        <img src="${baseURl64data}" class="image-preview"/>
                    </button>`;
                        currentPreviewRow.find('td:first').html(previewHtml);
                    }

                    closeCropperModal();
                    toast('success', 'Gambar berhasil di-crop');
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', quality);
        }

        // Close modal when clicking outside
        $(document).on('click', function(event) {
            if ($(event.target).attr('id') === 'cropperModal') {
                closeCropperModal();
            }
        });

        $(document).ready(function() {
            let index = 0;
            const isEdit = @json($edit);

            if (isEdit) {
                let KotaTujuan = @json($paket->KotaTujuan ?? '');
                let IncludeFasilitas = @json($paket->IncludeFasilitas ?? '');
                let ExcludeFasilitas = @json($paket->ExcludeFasilitas ?? '');
                let SyaratPaket = @json($syaratpaket ?? '');
                let HotelPaket = @json($hotelpaket ?? '');

                let Gambar = [];
                Gambar[1] = @json($paket->Gambar1 ?? '');
                Gambar[2] = @json($paket->Gambar2 ?? '');
                Gambar[3] = @json($paket->Gambar3 ?? '');
                Gambar[4] = @json($paket->Gambar4 ?? '');
                Gambar[5] = @json($paket->Gambar5 ?? '');

                KotaTujuan = KotaTujuan != 'null' && KotaTujuan != '' ? JSON.parse(KotaTujuan) : [];
                IncludeFasilitas = IncludeFasilitas != 'null' && IncludeFasilitas != '' ? JSON.parse(
                    IncludeFasilitas) : [];
                ExcludeFasilitas = ExcludeFasilitas != 'null' && ExcludeFasilitas != '' ? JSON.parse(
                    ExcludeFasilitas) : [];
                SyaratPaket = SyaratPaket != 'null' && SyaratPaket != '' ? JSON.parse(SyaratPaket) : [];
                HotelPaket = HotelPaket != 'null' && HotelPaket != '' ? JSON.parse(HotelPaket) : [];

                KotaTujuan.forEach(el => {
                    insertkota(el)
                });
                IncludeFasilitas.forEach(el => {
                    insertinfasilitas(el)
                });
                ExcludeFasilitas.forEach(el => {
                    insertexfasilitas(el)
                });

                for (let i = 1; i <= 5; i++) {
                    let filename = Gambar[i];
                    if (filename != null && filename != '') {
                        let path = `${baseURL}assets/gambar-paket/${filename}`
                        let tr = `<tr id="gambar-${index}" class="gambar">
                        <td> <button type="button" class="btn p-0 show-image" data-foto="${path}"><img src="${path}" class="image-preview"/> </button></td>
                        <td>
                            <input type="hidden" class="form-control" name="temp_Gambar[${index}]" value="${filename}" id="">
                            <input type="hidden" class="form-control" name="Gambar[${index}]"  id="crop${index}">
                        </td>
                        <td>
                            <button type="button" data-id="${index}" class="btn btn-inverse-danger btn-del-gambar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`
                        $("#tbody-gambar").append(tr)
                        if ($('.gambar').length == 5) {
                            $("#btn-add-gambar").hide()
                        }
                        initializeCropper(`#crop${index}`);
                        index++;
                    }
                }

                SyaratPaket.forEach(el => {
                    let tr = `<tr id='${index}' class="dokumen">
                        <td>
                            <input type="text" name="NamaSyarat[]" value="${el.NamaSyarat}" class="form-control" placeholder="Masukkan Nama Syarat" required>
                        </td>
                        <td>
                            <input type="text" name="Keterangan[]" class="form-control" value="${el.Keterangan}" placeholder="Masukkan Keterangan Syarat" id="">
                        </td>
                        <td>
                        <input type="hidden" name="NoUrutSyarat[]" value="${el.NoUrutSyarat}">
                            <button type="button" data-no="${el.NoUrutSyarat}" data-id="${index}" class="btn btn-inverse-danger btn-del-dok">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                      </tr>`
                    $("#tbody-dokumen").append(tr)
                    index++;
                })
                HotelPaket.forEach(el => {
                    inserthotel(el)
                })
            }


            $("#form-default").submit(function(e) {
                e.preventDefault()
                const formData = new FormData($("#form-default")[0]);
                $.ajax({
                    url: `${baseURL}admin/Paket/simpan`,
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(res) {

                        if (res.status) {
                            window.location.href = res.redirect
                            toast('success', res.message)
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        toast('error', 'Sistem Error')
                    }
                });
            });



            $('.nominal').on('keyup', function() {
                let nominal = $(this).val()
                $(this).val(format_nominal(nominal))
            })
            let e = $.Event('keyup');
            e.which = 65;
            $('.nominal').trigger(e);

            $("#add-kota").on('click', function() {
                let val = $("#InputKota").val();
                insertkota(val)
            })

            function insertkota(val) {
                let badge = `<button type="button" id="kota-${index}" class="badge badge-primary m-1 badge-kota">${val}<i class="fa fa-times ml-2"></i>
            <input type="hidden" name="KotaTujuan[]" value="${val}">
            </button>
            `
                $("#list-kota").append(badge)
                $("#InputKota").val("");
                index++
            }
            $("#list-kota").on('click', '.badge-kota', function() {
                $(this).remove()
            })

            $("#add-infasilitas").on('click', function() {
                let val = $("#InputInFasilitas").val();
                insertinfasilitas(val)
            })

            function insertinfasilitas(val) {
                let badge = `<button type="button" id="in-fas-${index}" class="badge badge-primary m-1 badge-in-fas">${val}<i class="fa fa-times ml-2"></i>
            <input type="hidden" name="IncludeFasilitas[]" value="${val}">
            </button>
            `
                $("#list-infasilitas").append(badge)
                $("#InputInFasilitas").val("");
                index++
            }
            $("#list-infasilitas").on('click', '.badge-in-fas', function() {
                $(this).remove()
            })

            $("#add-exfasilitas").on('click', function() {
                let val = $("#InputExFasilitas").val();
                insertexfasilitas(val)
            })

            function insertexfasilitas(val) {
                let badge = `<button type="button" id="ex-fas-${index}" class="badge badge-primary m-1 badge-ex-fas">${val}<i class="fa fa-times ml-2"></i>
            <input type="hidden" name="ExcludeFasilitas[]" value="${val}">
            </button>
            `
                $("#list-exfasilitas").append(badge)
                $("#InputExFasilitas").val("");
                index++
            }
            $("#list-exfasilitas").on('click', '.badge-ex-fas', function() {
                $(this).remove()
            })

            // gambar - UPDATED FOR CROPPER.JS
            $("#btn-add-gambar").on('click', function() {
                let tr = `<tr id="gambar-${index}" class="gambar">
                        <td></td>
                        <td>
                            <input type="hidden" class="form-control" name="Gambar[${index}]" id="crop${index}">
                            <input type="hidden" class="form-control" name="temp_Gambar[${index}]" id="">
                        </td>
                        <td>
                            <button type="button" data-id="${index}" class="btn btn-inverse-danger btn-del-gambar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`
                $("#tbody-gambar").append(tr)
                if ($('.gambar').length == 5) {
                    $("#btn-add-gambar").hide()
                }
                initializeCropper(`#crop${index}`);
                index++;
            })

            $("#btn-add-dokumen").on('click', function() {
                let tr = `<tr id='${index}' class="dokumen">
                        <td>
                            <input type="text" name="NamaSyarat[]" class="form-control" placeholder="Masukkan Nama Syarat" required>
                        </td>
                        <td>
                            <input type="text" name="Keterangan[]" class="form-control" placeholder="Masukkan Keterangan Syarat" id="">
                        </td>
                        <td>
                            <button type="button" data-no="" data-id="${index}" class="btn btn-inverse-danger btn-del-dok">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                      </tr>`
                $("#tbody-dokumen").append(tr)
                index++;
            })
            $("#btn-add-hotel").on('click', function() {
                inserthotel()
            })

            function inserthotel(el = []) {
                let tr = `<tr id="${index}">
                            <td>
                                <input type="text" name="NamaHotel[${index}]" required class="form-control" placeholder="Nama Hotel" id="" value="${el.NamaHotel ? el.NamaHotel : ''}">
                            </td>
                            <td>
                                <input type="text" name="Bintang[${index}]" value="${el.Bintang ? el.Bintang : ''}" class="form-control" placeholder="Bintang Hotel" id="">
                            </td>
                            <td>
                                <input type="date" name="TglMasuk[${index}]" value="${el.TglMasuk ? el.TglMasuk : ''}" class="form-control" placeholder="Tanggal Masuk Hotel" id="">
                            </td>
                            <td>
                                <input type="date" name="TglKeluar[${index}]" value="${el.TglKeluar ? el.TglKeluar : ''}" class="form-control" placeholder="Tanggal Keluar Hotel" id="">
                            </td>
                            <td>
                                <input type="text" name="KoorLat[${index}]" value="${el.KoorLat ? el.KoorLat : ''}" class="form-control" placeholder="Latitude Hotel" id="KoorLat${index}" readonly>
                            </td>
                            <td>
                                <input type="text" name="KoorLong[${index}]" value="${el.KoorLong ? el.KoorLong : ''}" class="form-control" placeholder="Longitude Hotel" id="KoorLong${index}" readonly>
                            </td>
                            <td>
                                <input type="text" name="AlamatHotel[${index}]" value="${el.AlamatHotel ? el.AlamatHotel : ''}" class="form-control" placeholder="Alamat Hotel" id="">
                            </td>
                            <td>
                                <div class="d-flex">
                                        <button type="button" title="Cari Lokasi" data-id="${index}" data-lat="${el.KoorLat ? el.KoorLat : ''}" data-long="${el.KoorLong ? el.KoorLong : ''}" data-toggle="modal" data-target="#mapModal" class="btn btn-inverse-info btn-map-hotel btn-sm pt-0 mr-2">
                                            <i class="fa fa-map-marker"></i>
                                        </button>
                                        <button type="button" data-id="${index}" class="btn btn-inverse-danger btn-del-hotel btn-sm pt-0">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                </div>
                            </td>

                        </tr>`
                $("#tbody-hotel").append(tr)
                index++;
            }

            $("#tbody-gambar").on('click', '.btn-del-gambar', function() {
                id = $(this).data('id')
                $("#gambar-" + id).remove();
                $("#btn-add-gambar").show()
            })
            $("#tbody-hotel").on('click', '.btn-del-hotel', function() {
                id = $(this).data('id')
                $("#" + id).remove();
            })
            $("#tbody-dokumen").on('click', '.btn-del-dok', function() {
                id = $(this).data('id')
                let no = $(this).data('no')
                if (no == '' || no == null) {
                    $("#" + id).remove();
                } else {
                    $.ajax({
                        url: `${baseURL}admin/Paket/hapussyarat?no=${no}`,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(res) {

                            if (res.status) {
                                toast('success', res.message)
                                $("#" + id).remove();
                            } else {
                                toast('error', res.message)
                            }
                        },
                        error: function(xhr, status, error) {
                            toast('error', 'Syarat paket tidak dapat dihapus')
                        }
                    });
                }
            })

            $("#tbody-gambar").on('click', '.show-image', function(e) {
                let foto = $(this).data('foto')
                Swal.fire({
                    imageUrl: foto,
                    width: 700,
                })
            });
            // show lama hari
            $('#TglBerangkat, #TglPulang').on('change', setLamaHari);

            function setLamaHari() {
                let berangkat = $('#TglBerangkat').val();
                let pulang = $('#TglPulang').val();

                if (berangkat && pulang) {
                    let daysDiff = Math.floor((new Date(pulang) - new Date(berangkat)) / (1000 * 60 * 60 * 24)) + 1;
                    $('#LamaHari').val(daysDiff);
                }
            }
        });
    </script>

    <script type="text/javascript">
        let active_map = 0;
        $("#tbody-hotel").on('click', '.btn-map-hotel', function() {

            $('#mapModal').find('#bungkusII').append(
                `<input class="form-control " id="input_place2" style="width: 50%; margin-top: 8px;" type="text" placeholder="Masukkan Lokasi Maps" value="">`
            )


            active_map = $(this).data('id')
            lat_active = $(`#KoorLat${active_map}`).val();
            long_active = $(`#KoorLong${active_map}`).val()
            if (lat_active != '' && long_active != '') {
                initMap(lat_active, long_active)
            } else {
                initMap()
            }
        })
        $(".btn-submit-map").on('click', function() {
            $('#mapModal').modal('hide')
            $(`#KoorLat${active_map}`).val($('#latpengambilan').val())
            $(`#KoorLong${active_map}`).val($('#lngpengambilan').val())
        })

        function initMap(latitude = '-7.556757269230274', longitude = '112.22636016751586') {
            var lat_ = parseFloat(latitude);
            var lng_ = parseFloat(longitude);
            const options = {
                enableHighAccuracy: false,
                timeout: 5000,
                maximumAge: 0,
            };

            function success(pos) {
                const crd = pos.coords;
                lat_ = crd.latitude;
                lng_ = crd.longitude;

            }

            function error(err) {
                console.warn(`ERROR(${err.code}): ${err.message}`);
            }

            navigator.geolocation.getCurrentPosition(success, error, options);


            //setting lokasi awal marker utk default nya
            var initialLatlng = {
                lat: lat_,
                lng: lng_
            };
            //var formatted_address = document.getElementById('Alamat').value;
            //initial map
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: initialLatlng
            });

            //setting place api search input text
            var input_ambil = document.getElementById('input_place2');
            console.log(input_ambil);
            var searchBox_ambil = new google.maps.places.SearchBox(input_ambil);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input_ambil);
            map.addListener('bounds_changed', function() {
                searchBox_ambil.setBounds(map.getBounds());
            });
            var markers = [];

            var marker = new google.maps.Marker({
                position: initialLatlng,
                map: map,
                icon: `${baseURL}assets/marker_small.png`,
                size: new google.maps.Size(10, 10),
                draggable: true
            });
            markers.push(marker);

            searchBox_ambil.addListener('places_changed', function() {
                var places_ambil = searchBox_ambil.getPlaces();
                if (places_ambil.length == 0) {
                    return;
                }

                var bounds_ambil = new google.maps.LatLngBounds();
                for (var j = 0, place_ambil; place_ambil = places_ambil[j]; j++) {
                    var marker_ambil = new google.maps.Marker({
                        draggable: true,
                        map: map,
                        icon: `${baseURL}assets/marker_small.png`,
                        size: new google.maps.Size(10, 10),
                        title: place_ambil.name,
                        position: place_ambil.geometry.location
                    });
                    map.setZoom(14);
                    map.setCenter(place_ambil.geometry.location);

                    document.getElementById('latpengambilan').value = marker_ambil.getPosition().lat().toFixed(6);
                    document.getElementById('lngpengambilan').value = marker_ambil.getPosition().lng().toFixed(6);
                    google.maps.event.addListener(marker_ambil, 'dragend', function(e) {
                        displayPosition_ambil(this.getPosition());
                    });
                    google.maps.event.addListener(marker_ambil, 'click', function(e) {
                        displayPosition_ambil(this.getPosition());
                    });
                    for (let k = 0; k < markers.length; k++) {
                        markers[k].setMap(null);
                    }
                    markers = [];
                    markers.push(marker_ambil);
                    bounds_ambil.extend(place_ambil.geometry.location);
                }

                map.fitBounds(bounds_ambil);
            });

            google.maps.event.addListener(map, 'bounds_changed', function() {
                var bounds_ambil = map.getBounds();
                searchBox_ambil.setBounds(bounds_ambil);
            });

            google.maps.event.addListener(marker, 'dragend', function(e) {
                displayPosition_ambil(this.getPosition());
            });

            function displayPosition_ambil(post) {
                document.getElementById('latpengambilan').value = post.lat();
                document.getElementById('lngpengambilan').value = post.lng();
            }
        }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.AUTH_MAPS') }}&libraries=places&callback=initMap">
    </script>
@endpush
