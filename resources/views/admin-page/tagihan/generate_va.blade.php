@extends('layouts.master')

@section('content')

@push('css')
 <style>
     p {
         margin: 0;
     }
 </style>
@endpush


<div class="main-panel">
    <div class="content-wrapper pb-0">
        <!-- first row starts here -->
        @if(isset($transaksitravel))
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="h3">Tagihan Pembayaran</h1>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div>
                                        <p class="font-weight-bolder">Nama Travel</p>
                                        <p>{{ $transaksitravel->NamaTravel }}</p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-weight-bolder">Nomor Invoice</p>
                                        <p>{{ $transaksitravel->NoInvoice }}</p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-weight-bolder">Total Tagihan</p>
                                        <p>Rp {{ currency($transaksitravel->Kredit) }}</p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-weight-bolder">Metode Pembayaran</p>
                                        <p>Virtual Account {{ $NamaBank }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <p class="font-weight-bolder">Batas Akhir Pembayaran</p>
                                        <p>{{ date_indo(datetime_to_date($transaksitravel->ExpiredVA)) }} ({{ substr($transaksitravel->ExpiredVA, 11, 8) }} WIB)</p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-weight-bolder">Nomor Virtual Account</p>
                                        <div class="input-group">
                                            <input type="text" class="form-control font-weight-bold" style="font-size: 16px;" id="va" value="{{ $transaksitravel->NoVa }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-primary" id="copyButton" type="button">
                                                    <i class="fa fa-copy small"></i> Salin
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="h3">Tagihan sudah dibayar</h1>
                            <a href="{{ url('admin/tagihan/Biayaaplikasi') }}" class="btn btn-primary mt-3">
                                <i class="fa fa-arrow-left mr-2"></i> Kembali ke halaman tagihan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- content-wrapper ends -->
</div>

@endsection
@push('js')
<script>
    $(document).ready(function() {
        $("#copyButton").click(function() {
            let va = $("#va").val();
            navigator.clipboard.writeText(va).then(function() {
                alert("Nomor Virtual Account Berhasil Disalin: " + va);
            }).catch(function(err) {
                console.error('Failed to copy text: ', err);
            });
        });
    });
</script>

@endpush
