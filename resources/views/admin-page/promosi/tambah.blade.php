@extends('layouts.master')

@section('content')


@php
    $edit = isset($transaksipromo->IDTransaksi) ? true : false;
@endphp

<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form id="form-default" method="post" class="row">
                            @csrf

                            {{-- Travel --}}
                            <div class="form-group col-md-6">
                                <label for="IDTravel">Travel</label>
                                <select name="IDTravel" id="IDTravel" class="form-control select2" required>
                                    @if (auth()->user()->LevelID == 1)
                                        <option value="">Pilih Travel</option>
                                    @endif
                                    @foreach ($travel as $row)
                                        <option value="{{ $row->IDTravel }}"
                                            {{ (isset($IDTravel) && $IDTravel == $row->IDTravel) ? 'selected' : '' }}>
                                            {{ $row->NamaTravel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Paket --}}
                            <div class="form-group col-md-6">
                                <label for="IDPaket">Paket</label>
                                <select id="IDPaket" name="IDPaket" class="form-control select2" required>
                                    <option value="">Pilih Paket</option>
                                    @foreach ($paket as $row)
                                        <option value="{{ $row->IDPaket }}"
                                            {{ (isset($IDPaket) && $IDPaket == $row->IDPaket) ? 'selected' : '' }}>
                                            {{ $row->NamaPaket }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tanggal Mulai --}}
                            <div class="form-group col-md-6">
                                <label for="TglMulai">Tanggal Mulai</label>
                                <input type="text" name="TglMulai" required class="form-control datepicker" id="TglMulai"
                                    value="{{ $transaksipromo->TglMulai ?? old('TglMulai') }}">
                            </div>

                            {{-- Tanggal Selesai --}}
                            <div class="form-group col-md-6">
                                <label for="TglBerakhir">Tanggal Selesai</label>
                                <input type="text" name="TglBerakhir" readonly class="form-control" id="TglSelesai"
                                    value="{{ $transaksipromo->TglBerakhir ?? old('TglBerakhir') }}">
                            </div>

                            {{-- Harga --}}
                            <div class="form-group col-md-6">
                                <label for="Tarif">Harga</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                    </div>
                                    <input type="text" name="Tarif" id="Tarif" placeholder="Tarif"
                                        value="{{ isset($HargaPromo) ? currency($HargaPromo) : '' }}"
                                        readonly class="form-control nominal">
                                </div>
                            </div>

                            {{-- Tombol --}}
                            <div class="text-right col-md-12">
                                <input type="hidden" name="id" value="{{ $transaksipromo->IDTransaksi ?? '' }}">
                                <button type="button" onclick="history.back()" class="btn btn-outline-primary mr-2">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
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
        const durasi = " {{ $Durasi }} "
        $("#IDTravel").on('change', function() {
            setPaket()
        })

        $("#TglMulai").on('change', function() {
            let startDate = $(this).val()
            date = new Date(startDate);
            next_date = new Date(date.setDate(date.getDate() + parseInt(durasi)));
            formatted = next_date.getUTCFullYear() + '-' + padNumber(next_date.getUTCMonth() + 1) + '-' + padNumber(next_date.getUTCDate())
            $("#TglSelesai").val(formatted)
        })

        function padNumber(number) {
            var string = '' + number;
            string = string.length < 2 ? '0' + string : string;
            return string;
        }

        function setPaket() {
            $("#IDPaket").empty()
            $("#IDPaket").append(`<option value="">Pilih Paket</option>`)
            travel = $("#IDTravel").val()
            $.ajax({
                url: `${baseURL}admin/Promosi/getpaket?id=${travel}`,
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
                url: `${baseURL}admin/Promosi/simpan`,
                type: 'POST',
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
                    toast('error', 'Sistem Error')
                }
            });
        });

    });
</script>

@endpush
