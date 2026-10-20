@extends("layouts.master")

@push("css")
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css" crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs4.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .form-submitting {
            pointer-events: none;
            opacity: 0.6;
        }

        .current-media-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            margin-bottom: 10px;
        }

        .current-media-img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
        }

        .card-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-container-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 18px;
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-container-body {
            padding: 18px;
        }

        .item-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #ffffff;
            padding: 16px;
            margin-bottom: 16px;
        }
    </style>
@endpush

@section("content")
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-11 mx-auto stretch-card grid-margin">
                    <div class="card w-100">
                        <div class="card-body">
                            {{-- Top Header Standar Haromain --}}
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">{{ $title }}</h4>
                                <a href="{{ route("admin.landing.index") }}" class="btn btn-light btn-sm">
                                    <i class="fa fa-arrow-left mr-1"></i> Kembali
                                </a>
                            </div>

                            {{-- Alert --}}
                            <div id="alert-container" style="display:none;" class="mb-3">
                                <div id="alert-message" class="alert"></div>
                            </div>

                            {{-- Form Agregat --}}
                            <form id="form-agregat" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="section" value="{{ $section }}">

                                {{-- ========================================================================= --}}
                                {{-- SECTION 1: PELAYANAN LEBIH BAIK --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-1")
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-file-text-o mr-2 text-warning"></i>Konten Seksi 1</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Headline Section 1 <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="JudulKonten" class="form-control"
                                                    value="{{ @$data->JudulKonten }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Isi Konten (Teks, Poin-Poin,
                                                    Penutup)</label>
                                                <textarea name="IsiKonten" class="summernote form-control" rows="8">{{ @$data->IsiKonten }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Gambar Mockup Section 1</label>
                                                @if (!empty(@$data->GambarKonten))
                                                    <div
                                                        class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                                        <small class="text-muted d-block mb-1 font-weight-bold">Gambar Saat
                                                            Ini:</small>
                                                        <img src="{{ asset("assets/konten/" . @$data->GambarKonten) }}"
                                                            alt="Preview" class="img-fluid rounded"
                                                            style="max-height: 120px; object-fit: contain;">
                                                        <div class="small text-muted mt-1">{{ @$data->GambarKonten }}</div>
                                                    </div>
                                                @endif
                                                <input type="file" name="GambarKonten" class="dropify" data-height="160"
                                                    data-allowed-file-extensions="jpg jpeg png webp svg"
                                                    data-max-file-size="2M"
                                                    @if (!empty(@$data->GambarKonten)) data-default-file="{{ asset("assets/konten/" . @$data->GambarKonten) }}" @endif />
                                                <small class="text-muted">Format: JPG, PNG, WEBP, SVG. Maks 2MB.</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- SECTION 2: PERAN HAROMAIN (HEADER + 3 KARTU + FOOTER) --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-2")
                                    {{-- 1. Header Card --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-header mr-2 text-primary"></i>1. Header Seksi</span>
                                        </div>
                                        <div class="card-container-body">
                                            <input type="hidden" name="header_id" value="{{ @$header->IDKonten }}">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Utama Seksi 2 <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="header_JudulKonten" class="form-control"
                                                    value="{{ @$header->JudulKonten }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Subjudul / Deskripsi Pengantar</label>
                                                <textarea name="header_IsiKonten" class="form-control summernote" rows="4">{{ @$header->IsiKonten }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. 3 Kartu Nilai Tambah --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-th-large mr-2 text-success"></i>2. Tiga Kartu Nilai
                                                Tambah</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="row">
                                                @foreach ($list as $key => $item)
                                                    <div class="col-md-4">
                                                        <div class="item-card h-100">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-primary">Kartu
                                                                    #{{ $key + 1 }}</strong>
                                                                <span
                                                                    class="badge badge-light text-muted">{{ $item->IDKonten }}</span>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Judul Kartu</label>
                                                                <input type="text"
                                                                    name="items[{{ $item->IDKonten }}][JudulKonten]"
                                                                    class="form-control" value="{{ $item->JudulKonten }}"
                                                                    required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Keterangan Singkat</label>
                                                                <textarea name="items[{{ $item->IDKonten }}][IsiKonten]" class="form-control summernote" rows="4">{{ $item->IsiKonten }}</textarea>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-bold">Icon SVG / Gambar</label>
                                                                @if (!empty($item->GambarKonten))
                                                                    <div
                                                                        class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                                                        <small
                                                                            class="text-muted d-block mb-1 font-weight-bold">Gambar
                                                                            Saat Ini:</small>
                                                                        <img src="{{ asset("assets/konten/" . $item->GambarKonten) }}"
                                                                            alt="Preview" class="img-fluid rounded"
                                                                            style="max-height: 120px; object-fit: contain;">
                                                                        <div class="small text-muted mt-1">
                                                                            {{ $item->GambarKonten }}</div>
                                                                    </div>
                                                                @endif
                                                                <input type="file"
                                                                    name="items[{{ $item->IDKonten }}][GambarKonten]"
                                                                    class="dropify" data-height="120"
                                                                    data-max-file-size="2M"
                                                                    data-allowed-file-extensions="jpg jpeg png webp svg"
                                                                    @if (!empty($item->GambarKonten)) data-default-file="{{ asset("assets/konten/" . $item->GambarKonten) }}" @endif />
                                                                <small class="text-muted">Format: SVG, PNG, JPG.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 3. Footer Card --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-quote-left mr-2 text-info"></i>3. Footer Penutup
                                                Seksi</span>
                                        </div>
                                        <div class="card-container-body">
                                            <input type="hidden" name="footer_id" value="{{ @$footer->IDKonten }}">
                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold">Teks Penutup</label>
                                                <textarea name="footer_IsiKonten" class="form-control summernote" rows="3">{{ @$footer->IsiKonten }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- SECTION 3: FITUR UNGGULAN (HEADER + 3 TAHAPAN A/B/C) --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-3")
                                    {{-- 1. Header Card --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-header mr-2 text-primary"></i>1. Header Fitur
                                                Unggulan</span>
                                        </div>
                                        <div class="card-container-body">
                                            <input type="hidden" name="header_id" value="{{ @$header->IDKonten }}">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Fitur Unggulan <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="header_JudulKonten" class="form-control"
                                                    value="{{ @$header->JudulKonten }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Subjudul Deskripsi</label>
                                                <textarea name="header_IsiKonten" class="form-control summernote" rows="3">{{ @$header->IsiKonten }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. Tiga Tahapan List --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-tasks mr-2 text-success"></i>2. Tiga Tahapan Perjalanan
                                                (A, B, C)</span>
                                        </div>
                                        <div class="card-container-body">
                                            @foreach ($list as $key => $item)
                                                <div class="item-card mb-4">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                                        <h5 class="mb-0 text-dark font-weight-bold">
                                                            <i class="fa fa-map-marker text-danger mr-1"></i> Tahap
                                                            {{ chr(65 + $key) }}: {{ $item->JudulKonten }}
                                                        </h5>
                                                        <span class="badge badge-light">{{ $item->IDKonten }}</span>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-7">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Judul Tahapan</label>
                                                                <input type="text"
                                                                    name="items[{{ $item->IDKonten }}][JudulKonten]"
                                                                    class="form-control" value="{{ $item->JudulKonten }}"
                                                                    required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Detail Isi (Subjudul H4 +
                                                                    Poin List + Catatan)</label>
                                                                <textarea name="items[{{ $item->IDKonten }}][IsiKonten]" class="summernote form-control" rows="6">{{ $item->IsiKonten }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Gambar Mockup
                                                                    Tahapan</label>
                                                                @if (!empty($item->GambarKonten))
                                                                    <div
                                                                        class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                                                        <small
                                                                            class="text-muted d-block mb-1 font-weight-bold">Gambar
                                                                            Saat Ini:</small>
                                                                        <img src="{{ asset("assets/konten/" . $item->GambarKonten) }}"
                                                                            alt="Preview" class="img-fluid rounded"
                                                                            style="max-height: 120px; object-fit: contain;">
                                                                        <div class="small text-muted mt-1">
                                                                            {{ $item->GambarKonten }}</div>
                                                                    </div>
                                                                @endif
                                                                <input type="file"
                                                                    name="items[{{ $item->IDKonten }}][GambarKonten]"
                                                                    class="dropify" data-height="160"
                                                                    data-max-file-size="2M"
                                                                    data-allowed-file-extensions="jpg jpeg png webp svg"
                                                                    @if (!empty($item->GambarKonten)) data-default-file="{{ asset("assets/konten/" . $item->GambarKonten) }}" @endif />
                                                                <small class="text-muted">Disarankan format landscape
                                                                    (rasio 4:3 atau 16:9).</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- SECTION 4: TARGET PENGGUNA (HEADER + 4 PILAR AUDIENS) --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-4")
                                    {{-- 1. Header Card --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-header mr-2 text-primary"></i>1. Header Target
                                                Pengguna</span>
                                        </div>
                                        <div class="card-container-body">
                                            <input type="hidden" name="header_id" value="{{ @$header->IDKonten }}">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Utama <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="header_JudulKonten" class="form-control"
                                                    value="{{ @$header->JudulKonten }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Subjudul</label>
                                                <textarea name="header_IsiKonten" class="form-control summernote" rows="2">{{ @$header->IsiKonten }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. 4 Pilar Audiens List --}}
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-users mr-2 text-success"></i>2. Empat Pilar
                                                Audiens</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="row">
                                                @foreach ($list as $key => $item)
                                                    <div class="col-md-6 col-lg-3">
                                                        <div class="item-card h-100">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-primary">Pilar
                                                                    #{{ $key + 1 }}</strong>
                                                                <span
                                                                    class="badge badge-light">{{ $item->IDKonten }}</span>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Nama Audiens</label>
                                                                <input type="text"
                                                                    name="items[{{ $item->IDKonten }}][JudulKonten]"
                                                                    class="form-control" value="{{ $item->JudulKonten }}"
                                                                    required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Subjudul & Manfaat</label>
                                                                <textarea name="items[{{ $item->IDKonten }}][IsiKonten]" class="summernote form-control" rows="4">{{ $item->IsiKonten }}</textarea>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-bold">Icon SVG / Gambar</label>
                                                                @if (!empty($item->GambarKonten))
                                                                    <div
                                                                        class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                                                        <small
                                                                            class="text-muted d-block mb-1 font-weight-bold">Gambar
                                                                            Saat Ini:</small>
                                                                        <img src="{{ asset("assets/konten/" . $item->GambarKonten) }}"
                                                                            alt="Preview" class="img-fluid rounded"
                                                                            style="max-height: 120px; object-fit: contain;">
                                                                        <div class="small text-muted mt-1">
                                                                            {{ $item->GambarKonten }}</div>
                                                                    </div>
                                                                @endif
                                                                <input type="file"
                                                                    name="items[{{ $item->IDKonten }}][GambarKonten]"
                                                                    class="dropify" data-height="120"
                                                                    data-max-file-size="2M"
                                                                    data-allowed-file-extensions="jpg jpeg png webp svg"
                                                                    @if (!empty($item->GambarKonten)) data-default-file="{{ asset("assets/konten/" . $item->GambarKonten) }}" @endif />
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- SECTION 5: VIDEO SIMULASI LAPANGAN --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-5")
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-youtube-play mr-2 text-danger"></i>Video Simulasi
                                                Lapangan</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Section 5 <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="JudulKonten" class="form-control"
                                                    value="{{ @$data->JudulKonten }}" required>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Teks Deskripsi & Poin-Poin Manfaat</label>
                                                <textarea name="IsiKonten" class="summernote form-control" rows="8">{{ @$data->IsiKonten }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Tautan YouTube</label>
                                                <input type="text" name="VideoUrl" class="form-control"
                                                    value="{{ @$data->GambarKonten }}"
                                                    placeholder="https://www.youtube.com/watch?v=...">
                                                <small class="text-muted">Masukkan link lengkap video YouTube (akan
                                                    otomatis diputar langsung via Modal Pop-up player).</small>
                                            </div>

                                            <div class="form-group">
                                                <label class="font-weight-bold">Atau Gambar Alternatif (Opsional, jika
                                                    bukan YouTube)</label>
                                                @if (!empty(@$data->GambarKonten) && strpos(@$data->GambarKonten, "http") === false)
                                                    <div
                                                        class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                                        <small class="text-muted d-block mb-1 font-weight-bold">Gambar Saat
                                                            Ini:</small>
                                                        <img src="{{ asset("assets/konten/" . @$data->GambarKonten) }}"
                                                            alt="Preview" class="img-fluid rounded"
                                                            style="max-height: 120px; object-fit: contain;">
                                                        <div class="small text-muted mt-1">{{ @$data->GambarKonten }}
                                                        </div>
                                                    </div>
                                                @endif
                                                <input type="file" name="GambarUpload" class="dropify"
                                                    data-height="160"
                                                    data-max-file-size="2M"
                                                    data-allowed-file-extensions="jpg jpeg png webp svg mp4"
                                                    @if (!empty(@$data->GambarKonten) && strpos(@$data->GambarKonten, "http") === false) data-default-file="{{ asset("assets/konten/" . @$data->GambarKonten) }}" @endif />
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- SECTION 6: MITRA TRAVEL HAROMAIN --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-6")
                                    <div class="card-container mb-4">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-pencil-square-o mr-2 text-primary"></i>Keterangan & Deskripsi Seksi Mitra Travel</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Seksi <span class="text-danger">*</span></label>
                                                <input type="text" name="header_JudulKonten" class="form-control"
                                                    value="{{ @$header->JudulKonten ?? 'Mitra Travel Haromain' }}" required>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold">Deskripsi / Keterangan Seksi</label>
                                                <textarea name="header_IsiKonten" class="form-control" rows="3">{{ @$header->IsiKonten ?? 'Bersama mitra travel, Haromain membantu menghadirkan pelayanan Haji dan Umrah yang lebih terintegrasi, profesional, dan terpercaya.' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-container">
                                        <div
                                            class="card-container-header d-flex justify-content-between align-items-center">
                                            <span><i class="fa fa-building-o mr-2 text-primary"></i>Mitra Travel
                                                Resmi</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold"
                                                id="btn-add-mitra">
                                                <i class="fa fa-plus mr-1"></i> Tambah Mitra Travel Baru
                                            </button>
                                        </div>
                                        <div class="card-container-body">
                                            <input type="hidden" name="deleted_ids" id="deleted_ids" value="">

                                            <div class="row" id="mitra-repeater-container">
                                                @foreach ($list as $key => $item)
                                                    <div class="col-md-6 mitra-item" data-id="{{ $item->IDKonten }}">
                                                        <div class="item-card">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-primary mitra-label">Mitra Travel
                                                                    #{{ $key + 1 }}</strong>
                                                                <div>
                                                                    <span
                                                                        class="badge border text-dark font-weight-bold mr-2 px-2 py-1"
                                                                        style="background-color: #f1f3f5; border-color: #ced4da; font-size: 11px;">
                                                                        {{ $item->IDKonten }}
                                                                    </span>
                                                                    <button type="button"
                                                                        class="btn btn-inverse-danger btn-sm p-2 btn-remove-mitra"
                                                                        data-id="{{ $item->IDKonten }}" title="Hapus">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Nama Travel</label>
                                                                <input type="text"
                                                                    name="items[{{ $item->IDKonten }}][JudulKonten]"
                                                                    class="form-control" value="{{ $item->JudulKonten }}"
                                                                    required>
                                                            </div>

                                                            <div class="form-group mb-0">
                                                                <label class="font-weight-bold">Logo Travel</label>
                                                                @if (!empty($item->GambarKonten))
                                                                    <div
                                                                        class="current-media-box p-2 bg-light rounded text-center border mb-2">
                                                                        <small
                                                                            class="text-muted d-block mb-1 font-weight-bold">Gambar
                                                                            Saat Ini:</small>
                                                                        <img src="{{ asset("assets/konten/" . $item->GambarKonten) }}"
                                                                            alt="Preview" class="img-fluid rounded"
                                                                            style="max-height: 120px; object-fit: contain;">
                                                                        <div class="small text-muted mt-1">
                                                                            {{ $item->GambarKonten }}</div>
                                                                    </div>
                                                                @endif
                                                                <input type="file"
                                                                    name="items[{{ $item->IDKonten }}][GambarKonten]"
                                                                    class="dropify" data-height="120"
                                                                    data-max-file-size="2M"
                                                                    data-allowed-file-extensions="jpg jpeg png webp svg"
                                                                    @if (!empty($item->GambarKonten)) data-default-file="{{ asset("assets/konten/" . $item->GambarKonten) }}" @endif />
                                                                <small class="text-muted d-block mt-1">
                                                                    <i class="mdi mdi-information-outline"></i> Tips: Gunakan format PNG transparan atau crop pas pada tepi logo (rasio landscape) agar tampilan di landing page optimal.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- SECTION 7: FINAL CTA BANNER --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "section-7")
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-bullhorn mr-2 text-primary"></i>Pengaturan Banner Ajakan (CTA Penutup)</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Judul Utama CTA <span class="text-danger">*</span></label>
                                                <input type="text" name="cta_judul" class="form-control" required
                                                    value="{{ $ctaHeader->JudulKonten ?? 'Tingkatkan Pelayanan, Tumbuhkan Kepercayaan Jamaah' }}">
                                            </div>
                                            <div class="form-group">
                                                <label class="font-weight-bold">Deskripsi / Sub-Judul CTA <span class="text-danger">*</span></label>
                                                <textarea name="cta_deskripsi" class="form-control summernote" rows="3" required>{{ $ctaHeader->IsiKonten ?? 'Hadirkan pelayanan digital yang terintegrasi untuk travel, Tour Leader/Mutowif, jamaah, dan keluarga bersama Haromain.' }}</textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Teks Tombol CTA <span class="text-danger">*</span></label>
                                                        @php
                                                            $currentBtnLabel = $actionBtn->JudulKonten ?? 'Diskusikan Kerja Sama Haromain';
                                                            if ($currentBtnLabel === 'Tombol Konsultasi CTA') {
                                                                $currentBtnLabel = 'Diskusikan Kerja Sama Haromain';
                                                            }
                                                        @endphp
                                                        <input type="text" name="btn_label" class="form-control" required
                                                            value="{{ $currentBtnLabel }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Link Tujuan Tombol (WhatsApp / URL) <span class="text-danger">*</span></label>
                                                        <input type="url" name="btn_url" class="form-control" required
                                                            value="{{ $actionBtn->IsiKonten ?? 'https://wa.me/6287777235337' }}">
                                                        <small class="text-muted">Contoh: https://wa.me/6287777235337</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- ========================================================================= --}}
                                {{-- GLOBAL SETTING: FOOTER & KONTAK --}}
                                {{-- ========================================================================= --}}
                                @if ($section === "global-setting")
                                    <div class="card-container">
                                        <div class="card-container-header">
                                            <span><i class="fa fa-globe mr-2 text-primary"></i>Pengaturan Kontak & Media
                                                Sosial Global</span>
                                        </div>
                                        <div class="card-container-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Nomor WhatsApp Official</label>
                                                        <input type="text" name="whatsapp" class="form-control"
                                                            value="{{ $setting["whatsapp"] ?? "6287777235337" }}">
                                                        <small class="text-muted">Format internasional tanpa simbol +
                                                            (contoh: 6287777235337).</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Email Support</label>
                                                        <input type="email" name="email" class="form-control"
                                                            value="{{ $setting["email"] ?? "haromain@afindo-inf.com" }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Deskripsi Ringkas Footer</label>
                                                        <textarea name="description" class="form-control summernote" rows="3">{{ $setting["description"] ?? "Haromain adalah platform pendampingan jamaah haji dan umroh yang dikembangkan dari pengalaman serta kondisi nyata di lapangan." }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Instagram URL</label>
                                                        <input type="url" name="instagram" class="form-control"
                                                            value="{{ $setting["instagram"] ?? "https://www.instagram.com/haromain.indonesia" }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Facebook URL</label>
                                                        <input type="url" name="facebook" class="form-control"
                                                            value="{{ $setting["facebook"] ?? "https://web.facebook.com/profile.php?id=61584806686309" }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">TikTok URL</label>
                                                        <input type="url" name="tiktok" class="form-control"
                                                            value="{{ $setting["tiktok"] ?? "https://www.tiktok.com/@haromain.indonesi?_r=1&_t=ZS-92LGAA16RhB/" }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">App Store URL</label>
                                                        <input type="url" name="appstore" class="form-control"
                                                            value="{{ $setting["appstore"] ?? "https://apps.apple.com/id/app/haromain/id6736605994" }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Google Play Store URL</label>
                                                        <input type="url" name="playstore" class="form-control"
                                                            value="{{ $setting["playstore"] ?? "https://play.google.com/store/apps/details?id=com.tiap.haromain.haromain" }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Action Submit Sesuai Standar Haromain (Gambar 1) --}}
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary mr-2" id="btn-submit">Simpan</button>
                                    <button class="btn btn-primary loading mr-2 d-none" type="button" disabled><span
                                            class="spinner-border spinner-border-sm mr-1"></span> Loading...</button>
                                    <a href="{{ route("admin.landing.index") }}" class="btn btn-light">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("js")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function() {
            // Dropify Init
            $('.dropify').dropify({
                messages: {
                    default: 'Drag dan drop gambar di sini atau klik',
                    replace: 'Drag dan drop atau klik untuk mengganti gambar',
                    remove: 'Hapus',
                    error: 'Terjadi kesalahan pada file.'
                },
                error: {
                    'fileSize': 'Ukuran file terlalu besar! Maksimal adalah 2MB.',
                    'fileExtension': 'Format file tidak diizinkan! Hanya (png, jpg, jpeg, webp, svg).'
                }
            });

            // Summernote Init
            function initSummernote(selector) {
                $(selector).summernote({
                    height: 200,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });
            }
            initSummernote('.summernote');

            // Section 6: Mitra Travel Repeater Logic
            let newMitraCounter = 1;
            let deletedMitraIds = [];

            function reindexMitra() {
                $('#mitra-repeater-container .mitra-item').each(function(idx) {
                    $(this).find('.mitra-label').text('Mitra Travel #' + (idx + 1));
                });
            }

            $('#btn-add-mitra').on('click', function() {
                const tempId = 'new_' + (newMitraCounter++);
                const newMitraHtml = `
                    <div class="col-md-6 mitra-item" data-id="${tempId}">
                        <div class="item-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-primary mitra-label">Mitra Travel Baru</strong>
                                <div>
                                    <span class="badge badge-success mr-2">Baru</span>
                                    <button type="button" class="btn btn-inverse-danger btn-sm p-2 btn-remove-mitra" data-id="${tempId}" title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Nama Travel <span class="text-danger">*</span></label>
                                <input type="text" name="items[${tempId}][JudulKonten]"
                                    class="form-control" required placeholder="Contoh: PT. Haromain Tour & Travel">
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Logo Travel</label>
                                <input type="file" name="items[${tempId}][GambarKonten]" class="dropify"
                                    data-height="120"
                                    data-max-file-size="2M"
                                    data-allowed-file-extensions="jpg jpeg png webp svg"
                                />
                                <small class="text-muted d-block mt-1">
                                    <i class="mdi mdi-information-outline"></i> Tips: Gunakan format PNG transparan atau crop pas pada tepi logo (rasio landscape) agar tampilan di landing page optimal.
                                </small>
                            </div>
                        </div>
                    </div>
                `;

                const $newMitra = $(newMitraHtml).appendTo('#mitra-repeater-container');
                $newMitra.find('.dropify').dropify({
                    messages: {
                        default: 'Drag dan drop gambar di sini atau klik',
                        replace: 'Drag dan drop atau klik untuk mengganti gambar',
                        remove: 'Hapus',
                        error: 'Terjadi kesalahan pada file.'
                    },
                    error: {
                        'fileSize': 'Ukuran file terlalu besar! Maksimal adalah 2MB.',
                        'fileExtension': 'Format file tidak diizinkan! Hanya (png, jpg, jpeg, webp, svg).'
                    }
                });
                reindexMitra();
            });

            $(document).on('click', '.btn-remove-mitra', function() {
                const $item = $(this).closest('.mitra-item');
                const id = $(this).data('id') || $item.data('id');

                Swal.fire({
                    title: 'Hapus Mitra Travel?',
                    text: 'Mitra travel ini akan dihapus saat form disimpan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (id && !String(id).startsWith('new_')) {
                            deletedMitraIds.push(id);
                            $('#deleted_ids').val(deletedMitraIds.join(','));
                        }
                        $item.remove();
                        reindexMitra();
                    }
                });
            });

            // Form submission dengan Anti-Rage Click / Submit Debounce & Safety Timeout
            let isSubmitting = false;

            function lockSubmit() {
                isSubmitting = true;
                $('#btn-submit').addClass('d-none');
                $('.loading').removeClass('d-none');
                $('#form-agregat').addClass('form-submitting');
            }

            function unlockSubmit() {
                isSubmitting = false;
                $('#btn-submit').removeClass('d-none');
                $('.loading').addClass('d-none');
                $('#form-agregat').removeClass('form-submitting');
            }

            $('#form-agregat').on('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) return;
                lockSubmit();

                // Safety timeout fallback (15 detik) untuk me-reset tombol jika koneksi jaringan terputus
                const safetyTimeout = setTimeout(function() {
                    if (isSubmitting) {
                        unlockSubmit();
                        Swal.fire({
                            icon: 'error',
                            title: 'Koneksi Terputus',
                            text: 'Koneksi timeout, silakan periksa jaringan dan coba lagi.'
                        });
                    }
                }, 15000);

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route("admin.landing.simpan") }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        clearTimeout(safetyTimeout);
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(function() {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    } else {
                                        window.location.href =
                                            "{{ route("admin.landing.index") }}";
                                    }
                                });
                            } else {
                                unlockSubmit();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        } catch (err) {
                            console.error("Parse error:", err);
                            unlockSubmit();
                        }
                    },
                    error: function(xhr, status, error) {
                        clearTimeout(safetyTimeout);
                        unlockSubmit();
                        var errMsg = 'Terjadi kesalahan sistem saat menyimpan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: errMsg
                        });
                    }
                });
            });
        });
    </script>
@endpush
