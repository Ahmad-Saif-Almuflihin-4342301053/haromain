@extends('layouts.master')

@section('content')

@php
    $edit = isset($banner->KodeBanner) ? true : false;
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
                        <form id="form-default" method="post">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                        <option value="">Pilih Travel</option>
                                        @foreach ($travel as $row)
                                            <option value="{{ $row->IDTravel }}"
                                                {{ $row->IDTravel == $IDTravel ? 'selected' : '' }}>
                                                {{ $row->NamaTravel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <select name="IDPaket" id="IDPaket" class="form-control select2" required>
                                        <option value="">Pilih Paket</option>
                                        @foreach ($paket as $row)
                                            <option value="{{ $row->IDPaket }}">
                                                {{ $row->NamaPaket }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <select name="IDCabang" id="IDCabang" class="form-control select2" required>
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($cabang as $row)
                                            <option value="{{ $row->IDCabang }}">
                                                {{ $row->NamaCabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="bg-light p-5 rounded-lg" id="emptyData">
                                Silahkan pilih travel, paket dan cabang
                            </div>

                            <div class="bg-light p-5 rounded-lg table-responsive" id="data-jamaah"
                                style="display: none;">
                                <div class="mb-3 h4">
                                    <span id="NamaTravel"></span>
                                    <span id="NamaPaket"></span>
                                </div>

                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Tarif</th>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                                    </div>
                                                    <input type="text" name="Tarif" id="Tarif"
                                                        placeholder="Tarif" readonly class="form-control nominal">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Jamaah</th>
                                            <td id="JumlahJamaah"></td>
                                        </tr>
                                        <tr>
                                            <th>Total</th>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                                    </div>
                                                    <input type="text" name="Total" id="Total"
                                                        placeholder="Total" readonly class="form-control nominal">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Daftar Jamaah</th>
                                            <td>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <td style="width:20px">
                                                                    <input type="checkbox" name=""
                                                                        class="form-control" id="checkAll">
                                                                </td>
                                                                <td>
                                                                    <label for="checkAll" class="m-0">Pilih
                                                                        Semua</label>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="ListJamaah">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="font-weight-bold text-behance text-right" style="display: none;"
                                    id="invoice-added">
                                    Invoice sudah dibuat
                                </div>

                                <div class="text-right mt-3" id="action-button">
                                    <button type="button" onclick="history.back()"
                                        class="btn btn-outline-primary mr-2">Batal</button>
                                    <button type="submit" class="btn btn-primary">Buat Invoice</button>
                                </div>
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
        let index = 0;
        let totaljamaah = 0;
        let tarif = 0;
        $("#IDTravel").on('change', function() {
            setPaket()
            setCabang()
            refreshData()
        })
        $("#IDPaket").on('change', function() {
            refreshData()
        })
        $("#IDCabang").on('change', function() {
            refreshData()
        })

        function refreshData() {
            paket = $("#IDPaket").val()
            travel = $("#IDTravel").val()
            cabang = $("#IDCabang").val()
            if (paket != '' && travel != '' && cabang != '') {
                $("#emptyData").hide()
                $("#invoice-added").hide()
                $("#action-button").show()
                $("#data-jamaah").show()
                $("#ListJamaah").empty()
                updateJumlah()
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/gettagihan?travel=${travel}&paket=${paket}&cabang=${cabang}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {

                        $("#NamaTravel").text(res.data.NamaTravel)
                        $("#NamaPaket").text(` - ${res.data.NamaPaket}`)
                        $("#Tarif").val(format_nominal(res.data.tarif))
                        tarif = res.data.tarif;
                        res.data.jamaah.forEach(el => {
                            let checked = el.NoInvoice != null && el.NoInvoice != '' ?
                                'checked' : ''
                            let td = `<tr>
                                        <td style="width:20px">
                                            <input type="hidden" name="IDPerson[${index}]" class="form-control" value="${el.IDPerson}">
                                            <input type="checkbox" ${checked} name="CheckJamaah[${index}]" class="form-control check-jamaah" value="${el.IDPerson}" id="${index}">
                                        </td>
                                        <td>
                                            <label for="${index}" class="m-0">
                                               ${el.NamaJamaah}
                                            </label>
                                        </td>
                                    </tr>`
                            $("#ListJamaah").append(td)
                            if (el.NoInvoice != null && el.NoInvoice != '') {
                                $("#invoice-added").show()
                                $("#action-button").hide()

                            }
                            index++;
                        });

                        updateJumlah()

                    },
                    error: function(xhr, status, error) {
                        toast('error', 'Sistem Error')
                    }
                });

            } else {
                $("#emptyData").show()
                $("#data-jamaah").hide()
            }
        }

        $("#ListJamaah").on('change', '.check-jamaah', function() {
            updateJumlah()
            $("#JumlahJamaah").text(totaljamaah)
        })
        $('#checkAll').on('change', function() {
            if (this.checked) {
                $(".check-jamaah").prop("checked", true);
            } else {
                $(".check-jamaah").prop("checked", false);
            }
            updateJumlah()
        })

        function updateJumlah() {
            totaljamaah = $('.check-jamaah:checked').length;
            $("#JumlahJamaah").text(totaljamaah)

            totalbiaya = totaljamaah * tarif;
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
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(res) {

                    res.data.forEach(el => {
                        let option =
                            `<option value="${el.IDPaket}" >${el.NamaPaket}</option>`;
                        $("#IDPaket").append(option)
                    });
                },
                error: function(xhr, status, error) {
                    toast('error', 'Sistem Error')
                }
            });
        }

        function setCabang() {
            $("#IDCabang").empty()
            $("#IDCabang").append(`<option value="">Pilih Cabang</option>`)
            travel = $("#IDTravel").val()
            $.ajax({
                url: `${baseURL}admin/tagihan/Tagihantravel/getcabang?id=${travel}`,
                type: 'GET',
                success: function(res) {

                    res.data.forEach(el => {
                        let option =
                            `<option value="${el.IDCabang}" >${el.NamaCabang}</option>`;
                        $("#IDCabang").append(option)
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
                url: `${baseURL}admin/tagihan/Tagihantravel/simpancetak`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function(res) {

                    if (res.status) {
                        toast('success', res.message)
                        window.location.href = res.redirect
                    } else {
                        console.log(formData);
                        toast('error', res.message)
                    }
                },
                error: function(xhr, status, error) {
                    toast('error', 'Sistem Error')
                }
            });
        });

    });
</script>
@endpush
