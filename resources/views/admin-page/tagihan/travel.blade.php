@extends('layouts.master')

@section('content')

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">

                            <form id="form-default" method="post">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                            <option value="">Pilih Travel</option>
                                            @foreach ($travel as $row)
                                                <option value="{{ $row->IDTravel }}" {{ ($row->IDTravel == ($IDTravel ?? '')) ? 'selected' : '' }}>
                                                    {{ $row->NamaTravel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <select name="IDPaket" id="IDPaket" class="form-control select2" required>
                                            <option value="">Pilih Paket</option>
                                            @foreach ($paket as $row)
                                                <option value="{{ $row->IDPaket }}">{{ $row->NamaPaket }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <select name="IDCabang" id="IDCabang" class="form-control select2" required>
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($cabang as $row)
                                                <option value="{{ $row->IDCabang }}">{{ $row->NamaCabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="bg-light p-5 rounded-lg" id="emptyData">
                                    Silahkan pilih travel, paket dan cabang
                                </div>

                                <div class="bg-light p-5 rounded-lg table-responsive" id="data-jamaah" style="display: none;">
                                    <div class="mb-3 h4">
                                        <span id="NamaTravel"></span>
                                        <span id="NamaPaket"></span>
                                    </div>
                                    <table class="table">
                                        <thead>
                                            <th>No</th>
                                            <th>Nama Jamaah</th>
                                            <th>No HP</th>
                                            <th>Tagihan</th>
                                            <th>Terbayar</th>
                                            <th>Kurang Bayar</th>
                                            <th>Aksi</th>
                                        </thead>
                                        <tbody id="ListJamaah"></tbody>
                                    </table>
                                </div>

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
        $(document).ready(function() {
            const baseURL = "{{ url('/') }}" + '/';
            let index = 0;

            $('#IDTravel').on('change', function() { setPaket(); setCabang(); refreshData(); });
            $('#IDPaket').on('change', function() { refreshData(); });
            $('#IDCabang').on('change', function() { refreshData(); });

            function refreshData() {
                const paket = $('#IDPaket').val();
                const travel = $('#IDTravel').val();
                const cabang = $('#IDCabang').val();
                if (paket && travel && cabang) {
                    $('#emptyData').hide();
                    $('#data-jamaah').show();
                    $('#ListJamaah').empty();
                    $.ajax({
                        url: `${baseURL}admin/tagihan/Tagihantravel/gettagihanjamaah?travel=${travel}&paket=${paket}&cabang=${cabang}`,
                        type: 'GET',
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        success: function(res) {
                            res = typeof res === 'string' ? JSON.parse(res) : res;
                            $('#NamaTravel').text(res.data.NamaTravel);
                            $('#NamaPaket').text(` - ${res.data.NamaPaket}`);
                            $('#Tarif').val(res.data.tarif ?? '');
                            res.data.jamaah.forEach((el, key) => {
                                const infoterbayar = el.InfoTerbayar ?? 0;
                                const kurangbayar = (el.InfoTagihan - infoterbayar).toString();
                                const td = `<tr>
                                    <td>${key+1}</td>
                                    <td><label class="m-0">${el.NamaJamaah}</label></td>
                                    <td>${el.NoHP}</td>
                                    <td>Rp ${format_nominal(el.InfoTagihan)}</td>
                                    <td>Rp ${format_nominal(infoterbayar)}</td>
                                    <td>Rp ${format_nominal(kurangbayar)}</td>
                                    <!-- <td><a href="${baseURL}admin/tagihan/Tagihantravel/bayarjamaah?id=${btoa(el.IDPerson)}&pkt=${btoa(el.IDPaket)}" class="btn btn-primary ${kurangbayar=='0' ? 'd-none' : ''}">Bayar</a></td> -->
                                    <td><a href="${baseURL}admin/tagihan/Tagihantravel/bayarjamaah?id=${btoa(el.IDPerson)}&pkt=${btoa(el.IDPaket)}" class="btn btn-primary ${el.IsPaid == '1' ? 'd-none' : ''}">Bayar</a></td>
                                </tr>`;
                                $('#ListJamaah').append(td);
                                index++;
                            });
                        },
                        error: function() { toast('error', 'Sistem Error') }
                    });
                } else {
                    $('#emptyData').show();
                    $('#data-jamaah').hide();
                }
            }

            function setPaket() {
                $('#IDPaket').empty().append(`<option value="">Pilih Paket</option>`);
                const travel = $('#IDTravel').val();
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/getpaket?id=${travel}`,
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        res.data.forEach(el => { $('#IDPaket').append(`<option value="${el.IDPaket}">${el.NamaPaket}</option>`); });
                    },
                    error: function() { toast('error', 'Sistem Error') }
                });
            }

            function setCabang() {
                $('#IDCabang').empty().append(`<option value="">Pilih Cabang</option>`);
                const travel = $('#IDTravel').val();
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/getcabang?id=${travel}`,
                    type: 'GET',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        res.data.forEach(el => { $('#IDCabang').append(`<option value="${el.IDCabang}">${el.NamaCabang}</option>`); });
                    },
                    error: function() { toast('error', 'Sistem Error') }
                });
            }

            $('#form-default').submit(function(e) {
                e.preventDefault();
                const formData = new FormData($('#form-default')[0]);
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/simpanpembayaranjamaah`,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res.status) { toast('success', res.message); window.location.href = res.redirect; }
                        else { toast('error', res.message); }
                    },
                    error: function() { toast('error', 'Sistem Error') }
                });
            });
        });
    </script>

@endpush
