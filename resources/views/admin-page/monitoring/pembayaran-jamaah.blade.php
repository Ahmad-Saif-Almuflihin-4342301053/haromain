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
                        <div class="d-flex justify-content-between flex-wrap">
                            <form action="" class="col-md-12 mb-3">
                                <input type="hidden" id="id" name="id" value="{{ base64_encode($IDPaket) }}">
                                <button type="submit" value="Detail" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Detail' ? 'tab-active' : '' }}">Detil Paket</button>
                                <button type="submit" value="Tracking" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Tracking' ? 'tab-active' : '' }}">Live Tracking</button>
                                <button type="submit" value="Jamaah" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Jamaah' ? 'tab-active' : '' }}">Jamaah</button>
                                <button type="submit" value="Pembayaranjamaah" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Pembayaranjamaah' ? 'tab-active' : '' }}">Pembayaran</button>
                                <button type="submit" value="Pembayaranharomain" name="type" class="btn btn-lg text-black-50 btn-tab {{ $type == 'Pembayaranharomain' ? 'tab-active' : '' }}">Haromain</button>
                            </form>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="datatable">
                                        <thead>
                                            <tr class="table-active">
                                                <th>No</th>
                                                <th>Nama Jamaah</th>
                                                <th>Info Tagihan</th>
                                                <th>Tagihan Terbayar</th>
                                                <th>Sisa Tagihan</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- content-wrapper ends -->
</div>

@endsection
@push('js')
<script>
$(document).ready(function() {
    //datatables
    const table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: "{{ url('admin/Monitoring/getJamaah?id=' . $IDPaket) }}",
            type: "POST",
            headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
        },
        columns: [
            { data: 'No', className: "text-center" },
            { data: 'NamaJamaah' },
            {
                data: null,
                render: function(data, type, row) {
                    return `Rp ${format_nominal(data.InfoTagihan)}`
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let info = format_nominal(data.InfoTerbayar)
                    return info != '' ? `Rp ${info}` : 'Rp 0'
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `Rp ${format_nominal(data.InfoSisaTagihan)}`
                }
            },
        ],
        language: {
            url: `${baseURL}assets/admin-page/datatable/id.json`,
        },
        columnDefs: [
            { orderable: false, targets: [0, 1, 2, 3, 4] }
        ],
    });
});
</script>
@endpush
