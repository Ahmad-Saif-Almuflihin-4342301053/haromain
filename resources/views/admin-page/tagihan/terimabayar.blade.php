@extends('layouts.master')

@section('content')

    @php
        $edit = isset($banner?->KodeBanner);
    @endphp

    @push('css')
    <style>
        .table-sm th,
        .table-sm td {
            padding: 0px 2px;
        }
    </style>
    @endpush

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">

                            <div class="bg-light p-5 rounded-lg table-responsive" id="data-jamaah">
                                <div class="mb-3 h4">
                                    <span id="NamaTravel">{{ $NamaTravel ?? '' }}</span>
                                    <span id="NamaPaket">{{ $NamaPaket ?? '' }}</span>
                                    <span id="info" class="text-reddit">{{ empty($NoInvoice) ? 'Invoice untuk paket travel ini tidak tersedia' : '' }}</span>
                                </div>

                                <form action="" id="form-get" method="get">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 40%;">Invoice</th>
                                                <td style="width: 60%;">
                                                    <select name="inv" id="NoInvoice" class="form-control select2">
                                                        <option value="">Pilih Invoice</option>
                                                        @foreach ($invoice ?? [] as $row)
                                                            <option value="{{ $row->NoInvoice }}" @selected((($NoInvoice ?? '') == $row->NoInvoice))>
                                                                {{ $row->NoInvoice }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 40%;">Travel</th>
                                                <td style="width: 60%;">
                                                    <select name="travel" id="IDTravel" class="form-control select2">
                                                        <option value="">Pilih Travel</option>
                                                        @foreach ($travel ?? [] as $row)
                                                            <option value="{{ $row->IDTravel }}" @selected((($IDTravel ?? '') == $row->IDTravel))>
                                                                {{ $row->NamaTravel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Paket</th>
                                                <td>
                                                    <select name="paket" id="IDPaket" class="form-control select2">
                                                        <option value="">Pilih Paket</option>
                                                        @foreach ($paket ?? [] as $row)
                                                            <option value="{{ $row->IDPaket }}" @selected((($IDPaket ?? '') == $row->IDPaket))>
                                                                {{ $row->NamaPaket }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Cabang</th>
                                                <td>
                                                    <select name="cabang" id="IDCabang" class="form-control select2">
                                                        <option value="">Pilih Cabang</option>
                                                        @foreach ($cabang ?? [] as $row)
                                                            <option value="{{ $row->IDCabang }}" @selected((($IDCabang ?? '') == $row->IDCabang))>
                                                                {{ $row->NamaCabang }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>

                                <form id="form-default" method="post">
                                    @csrf
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <th style="width: 40%;">Jumlah Jamaah</th>
                                                <td style="width: 60%;" id="JumlahJamaah"></td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                                        </div>
                                                        <input type="text" name="Total" id="Total" placeholder="Total" readonly class="form-control nominal">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Daftar Jamaah</th>
                                                <td>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead></thead>
                                                            <tbody id="ListJamaah"></tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    @if(!empty($NoInvoice) && (($IsPaid ?? 0) == 0))
                                        <div class="text-right mt-3" id="btn-submit">
                                            <input type="hidden" name="NoInvoice" value="{{ $NoInvoice }}">
                                            <button type="button" onclick="history.back()" class="btn btn-outline-primary mr-2">Batal</button>
                                            @if((($LevelID ?? '') === '1'))
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            @elseif((($LevelID ?? '') === '2'))
                                                <button type="button" class="btn btn-primary" id="btn-bayar" data-toggle="modal" data-target="#modalVA">Bayar</button>
                                            @endif
                                        </div>
                                    @endif

                                    @if((($IsPaid ?? 0) == 1))
                                        <div class="font-weight-bold text-behance text-right" id="invoice-added">Tagihan sudah dibayar</div>
                                    @endif

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVA" tabindex="-1" aria-labelledby="modalVALabel" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVALabel">Rincian Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-va">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="Travel">Nomor Invoice</label>
                            <input type="text" name="invoice" id="show-NoInvoice" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="" class="d-block">Pilih Bank</label>
                            <select name="bank" id="bank" class="form-control select2" required>
                                <option value="">Pilih Bank</option>
                                @foreach ($bank ?? [] as $row)
                                <option value="{{ $row['code'] }}">{{ $row['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class=" form-group">
                            <label for="">Total Pembayaran</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                </div>
                                <input type="text" id="show-Total" placeholder="Tarif" value="" readonly class="form-control nominal">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="btn-va">Generate Virtual Account</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        $(document).ready(function() {
            let tarif = 0;
            let index = 0;
            init()
            $("#IDTravel").on('change', function() {
                $("#NoInvoice").val('')
                $("#IDCabang").val('')
                setPaket()
            })
            $("#IDPaket").on('change', function() {
                $("#NoInvoice").val('')
                refreshData()
            })
            $("#IDCabang").on('change', function() {
                $("#NoInvoice").val('')
                refreshData()
            })
            $("#NoInvoice").on('change', function() {
                $("#IDTravel").val('')
                $("#IDPaket").val('')
                $("#IDCabang").val('')
                refreshData()
            })
            $("#btn-bayar").on('click', function() {
                $("#show-NoInvoice").val($("#NoInvoice").val())
                $("#show-Total").val($("#Total").val())
                $("#show-NoVA").val('')
            })

            function refreshData() {
                $("#form-get").submit()
            }

            function init() {
                inv = $("#NoInvoice").val()
                if (inv != '') {
                    $("#emptyData").hide()
                    $("#data-jamaah").show()
                    $("#ListJamaah").empty()
                    updateJumlah()
                    $.ajax({
                        url: `${baseURL}admin/tagihan/Terimabayar/gettagihan?inv=${inv}`,
                        type: 'GET',
                        success: function(res) {

                            res.data.jamaah.forEach(el => {
                                let checked = el.NoInvoice != null && el.NoInvoice != '' ? 'checked' : ''
                                let td = `<tr>
                                        <td style="width:20px">
                                            <input type="hidden" name="IDPerson[${index}]" class="form-control" value="${el.IDPerson}">
                                            <input type="checkbox" disabled  data-tagihan="${el.NominalPaid}"  ${checked} name="CheckJamaah[${index}]" class="form-control check-jamaah" value="${el.IDPerson}" id="${index}">
                                        </td>
                                        <td>
                                            <label for="${index}" class="m-0">
                                               ${el.NamaJamaah}
                                            </label>
                                        </td>
                                    </tr>`
                                $("#ListJamaah").append(td)
                                index++;
                            });
                            updateJumlah()

                        },
                        error: function(xhr, status, error) {
                            toast('error', 'Sistem Error')
                        }
                    });

                }
            }

            function updateJumlah() {
                totaljamaah = $('.check-jamaah:checked').length;
                $("#JumlahJamaah").text(totaljamaah)

                totalbiaya = 0;
                $(".check-jamaah:checked").each(function() {
                    infoTagihan = $(this).data("tagihan") || 0;
                    totalbiaya += parseInt(infoTagihan, 10);
                });
                formatted_totabiaya = format_nominal(totalbiaya.toString())
                $("#Total").val(formatted_totabiaya)

            }

            function setPaket() {
                $("#IDPaket").empty()
                $("#IDPaket").append(`<option value="">Pilih Paket</option>`)
                travel = $("#IDTravel").val()
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/getpaket?id=${travel}`,
                    type: 'GET',
                    success: function(res) {

                        res.data.forEach(el => {
                            let option = `<option value="${el.IDPaket}" >${el.NamaPaket}</option>`;
                            $("#IDPaket").append(option)
                        });
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            }
            $("#form-default").submit(function(e) {
                e.preventDefault()
                const formData = new FormData($("#form-default")[0]);
                $.ajax({
                    url: `${baseURL}admin/tagihan/Terimabayar/simpan`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {

                        if (res.status) {
                            toast('success', res.message)
                            window.location.href = res.redirect
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });
            });

            $("#form-va").on('submit', function(e) {
                e.preventDefault()
                $("#btn-va").prop("disabled", true)
                let inv = $("#show-NoInvoice").val()
                let bank = $("#bank").val()
                $.ajax({
                    url: `${baseURL}admin/tagihan/Pembayaran/generate_virtual_account?inv=${inv}&bank=${bank}`,
                    type: 'GET',
                    success: function(res) {

                        if (res.status == 'success') {
                            window.location.href = res.redirect
                        } else {
                            toast('error', res.message)
                            $("#btn-va").prop("disabled", false)
                        }
                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Gagal mendapatkan data')
                        $("#btn-va").prop("disabled", false)
                    }
                });
            })


        });
    </script>

@endpush
