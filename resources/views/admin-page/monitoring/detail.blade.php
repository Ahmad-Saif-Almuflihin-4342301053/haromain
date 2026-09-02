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
                        <div class="d-flex justify-content-between flex-wrap w-100">
                            <form action="" class="col-md-12 mb-3">
                                <input type="hidden" id="id" name="id" value="{{ base64_encode($IDPaket) }}">
                                @php $tabs = ['Detail' => 'Detil Paket', 'Tracking' => 'Live Tracking', 'Jamaah' => 'Jamaah', 'Pembayaranjamaah' => 'Pembayaran', 'Pembayaranharomain' => 'Haromain']; @endphp
                                @foreach($tabs as $key => $label)
                                    <button type="submit" value="{{ $key }}" name="type"
                                        class="btn btn-lg text-black-50 btn-tab {{ $type == $key ? 'tab-active' : '' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </form>

                            <div class="d-flex justify-content-between flex-wrap w-100">
                                <div class="col-md-7" style="padding: 0 3rem 0 1rem;">
                                    <div id="carouselExampleControls" class="carousel slide mb-4" data-ride="carousel">
                                        <div class="carousel-inner">
                                            @php $arrGambar = ['Gambar1','Gambar2','Gambar3','Gambar4','Gambar5']; $firstImage = true; @endphp
                                            @foreach($arrGambar as $value)
                                                @if(!empty($paket->$value))
                                                    <div class="carousel-item {{ $firstImage ? 'active' : '' }}">
                                                        <div class="bg-cover" style="width:100%; height:0; padding-bottom:100%; background-image:url('{{ asset('assets/gambar-paket/' . $paket->$value) }}')"></div>
                                                    </div>
                                                    @php $firstImage = false; @endphp
                                                @endif
                                            @endforeach
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Nama Paket</label>
                                    <input type="text" class="form-control" value="{{ $paket->NamaPaket }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Kategori Paket</label>
                                    <input type="text" class="form-control" value="{{ $paket->KategoriPaket }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Keberangkatan</label>
                                    <input type="text" class="form-control" value="{{ date_indo(datetime_to_date($paket->TglBerangkat)) }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Kepulangan</label>
                                    <input type="text" class="form-control" value="{{ date_indo(datetime_to_date($paket->TglPulang)) }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Deskripsi Paket</label>
                                    <textarea class="form-control" rows="5" readonly>{{ $paket->Deskripsi }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Lama Hari</label>
                                    <input type="number" class="form-control" value="{{ $paket->LamaHari }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Kota Tujuan</label>
                                    @if($paket->KotaTujuan)
                                        <ol>
                                            @foreach(json_decode($paket->KotaTujuan) as $value)
                                                <li>{{ $value }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Kuota Paket</label>
                                    <input type="number" class="form-control" value="{{ $paket->KuotaPaket }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Maksimal Booking</label>
                                    <input type="text" class="form-control" value="{{ date_indo(datetime_to_date($paket->TglMaxBooking)) }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Maksimal Bayar</label>
                                    <input type="text" class="form-control" value="{{ date_indo(datetime_to_date($paket->TglMaxBayar)) }}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Include Fasilitas</label>
                                    @if($paket->IncludeFasilitas)
                                        <ol>
                                            @foreach(json_decode($paket->IncludeFasilitas) as $value)
                                                <li>{{ $value }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Exclude Fasilitas</label>
                                    @if($paket->ExcludeFasilitas)
                                        <ol>
                                            @foreach(json_decode($paket->ExcludeFasilitas) as $value)
                                                <li>{{ $value }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Maksimal Upload Dokumen</label>
                                    <input type="text" class="form-control" value="{{ date_indo(datetime_to_date($paket->TglMaxDokumen)) }}" readonly>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Biaya Quad</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-white">Rp</span>
                                        </div>
                                        <input type="text" class="form-control nominal" value="{{ $paket->BiayaQuad }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Biaya Triple</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-white">Rp</span>
                                        </div>
                                        <input type="text" class="form-control nominal" value="{{ $paket->BiayaTriple }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Biaya Double</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary text-white">Rp</span>
                                        </div>
                                        <input type="text" class="form-control nominal" value="{{ $paket->BiayaDouble }}" readonly>
                                    </div>
                                </div>

                                {{-- Hotel Paket --}}
                                <div class="form-group col-md-12">
                                    <label>Hotel</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th>Nama Hotel</th>
                                                <th>Bintang</th>
                                                <th>Tanggal Masuk</th>
                                                <th>Tanggal Keluar</th>
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th>Alamat</th>
                                            </thead>
                                            <tbody>
                                                @forelse($hotelpaket as $row)
                                                    <tr>
                                                        <td>{{ $row->NamaHotel }}</td>
                                                        <td>{{ $row->Bintang }}</td>
                                                        <td>{{ date_indo($row->TglMasuk) }}</td>
                                                        <td>{{ date_indo($row->TglKeluar) }}</td>
                                                        <td>{{ $row->KoorLat }}</td>
                                                        <td>{{ $row->KoorLong }}</td>
                                                        <td>{{ $row->AlamatHotel }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Dokumen Persyaratan --}}
                                <div class="form-group col-md-12">
                                    <label>Dokumen Persyaratan</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th>Nama Dokumen</th>
                                                <th>Keterangan Dokumen</th>
                                            </thead>
                                            <tbody>
                                                @forelse($syaratpaket as $row)
                                                    <tr>
                                                        <td>{{ $row->NamaSyarat }}</td>
                                                        <td>{{ $row->Keterangan }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center">Tidak ada data</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div> {{-- end inner form --}}
                        </div> {{-- end flex --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
