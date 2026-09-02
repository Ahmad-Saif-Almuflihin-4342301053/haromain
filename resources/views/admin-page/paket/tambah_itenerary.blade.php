@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">

                        <div class="card-header">
                            <label>Nama Paket:</label>
                            <h5>{{ $paket->NamaPaket }}</h5>

                            <div class="mt-2 d-flex" style="gap: .5rem;">
                                <a href="{{ route('admin.paket.downloadIteneraryTemplate', [ 'encodedIDPaket' => strtr(base64_encode($paket->IDPaket), '+/=', '._-'), 'encodedIDTravel' => strtr(base64_encode($paket->IDTravel), '+/=', '._-') ]) }}"
                                    class="btn btn-sm btn-info" target="_blank">
                                    <i class="mdi mdi-download"></i> Download Format Excel
                                </a>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                    data-target="#modal-import-itenerary">
                                    <i class="mdi mdi-upload"></i> Import Excel
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteSemuaItenerary()">
                                    <i class="mdi mdi-trash"></i> Hapus Semua Itenerary
                                </button>
                            </div>
                        </div>

                        <form id="form-default" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="card-body">
                                <div class="row">

                                    <div class="col-6">
                                        <div class="form-input">
                                            <label>Hari Ke</label>
                                            <select id="HariKe" name="HariKe" class="form-control">
                                                <option data-tanggal="" value="">
                                                    Pilih Hari
                                                </option>
                                                @foreach ($datahari as $val)
                                                    <option value="{{ $val['harike'] }}"
                                                        data-tanggal="{{ $val['tanggal'] }}">
                                                        Hari Ke {{ $val['harike'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-input">
                                            <label>Tanggal</label>
                                            <input id="Tanggal" class="form-control" value=""
                                                readonly />
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <table class="table w-100" id="table-itenerary">
                                            <thead class="bg-lightgray">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Jam Mulai</th>
                                                    <th>Jam Selesai</th>
                                                    <th>Agenda</th>
                                                    <th>Lokasi</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="no">1</span>
                                                    </td>

                                                    <td>
                                                        <input type="time" name="TglMulai[]" class="form-control"
                                                            required />
                                                    </td>

                                                    <td>
                                                        <input type="time" name="TglSelesai[]" class="form-control"
                                                            required />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="Agenda[]" class="form-control"
                                                            placeholder="Masukkan agenda kegiatan" required />
                                                    </td>

                                                    <td>
                                                        <input type="text" name="Lokasi[]" class="form-control"
                                                            placeholder="Masukkan lokasi kegiatan" required />
                                                    </td>

                                                    <td style="width: 10%;">
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="removeRow(this)">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <button type="button" class="btn btn-primary w-100 mt-2 btn-sm" onclick="addRow()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>

                                    <div class="col-12 mt-3 text-right">
                                        <a href="javascript:history.back()" class="btn btn-secondary">
                                            Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            Simpan
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-import-itenerary" tabindex="-1" role="dialog"
        aria-labelledby="modal-import-itenerary-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-import-itenerary-label">Import Itenerary</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-import-itenerary" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0">File Excel</label>
                                <a href="{{ route('admin.paket.downloadIteneraryTemplate', [ 'encodedIDPaket' => strtr(base64_encode($paket->IDPaket), '+/=', '._-'), 'encodedIDTravel' => strtr(base64_encode($paket->IDTravel), '+/=', '._-') ]) }}"
                                    class="btn btn-sm btn-info" target="_blank">
                                    <i class="mdi mdi-download"></i> Download Format
                                </a>
                            </div>
                            <input type="file" name="file" class="form-control" required accept=".xlsx,.xls">
                            <small class="text-muted">Kolom: HariKe, JamMulai, JamSelesai, Agenda, Lokasi</small>
                        </div>

                        <div id="import-itenerary-result" class="d-none">
                            <hr>
                            <h6>Hasil Import</h6>
                            <div class="alert alert-success">Berhasil: <span id="import-itenerary-success-count">0</span>
                            </div>
                            <div id="import-itenerary-failed-container" class="d-none">
                                <div class="alert alert-danger">Gagal:</div>
                                <ul id="import-itenerary-failed-list" class="list-group"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
