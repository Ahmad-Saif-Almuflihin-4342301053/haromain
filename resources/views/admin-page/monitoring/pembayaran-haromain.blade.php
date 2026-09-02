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

@php
$AdminPusat = Auth::user()->IDTravel == '' ? true : false;
@endphp

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
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <input type="text" id="filter-nama" class="form-control" placeholder="Filter Nama Jamaah">
                                    </div>
                                </div>

                                <form id="form-default" method="post">
                                    @if ($AdminPusat)
                                        <button type="submit" class="btn btn-primary float-right mb-3">Simpan Perubahan</button>
                                    @endif
                                    <input type="hidden" name="IDPaket" value="{{ $IDPaket }}">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="datatable">
                                            <thead>
                                                <tr class="table-active">
                                                    <th>No</th>
                                                    <th>Nama Jamaah</th>
                                                    <th>Nominal</th>
                                                    <th style="width: 100px;" class="text-center">
                                                        Terbayar <br>
                                                        <input type="checkbox" id="check-all" {{ $AdminPusat ? '' : 'disabled' }}>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($jamaah) && count($jamaah) > 0)
                                                    @foreach ($jamaah as $key => $row)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $row->NamaJamaah }}</td>
                                                            <td>{{ formatCurrency($row->NominalPaid) }}</td>
                                                            <td class="text-center">
                                                                <!-- We use a custom class 'check-item' for our logic -->
                                                                <input type="checkbox" class="check-item" data-id="{{ $row->IDPerson }}" {{ $row->IsPaid == 1 ? 'checked' : '' }} {{ $AdminPusat ? '' : 'disabled' }}>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- content-wrapper --}}
</div> {{-- main-panel --}}

@endsection
@push('js')
<script>
$(document).ready(function() {
    // Initial state map: IDPerson -> IsPaid (boolean)
    let jamaahState = {};
    
    // Initialize state from server data
    @if($jamaah)
        @foreach($jamaah as $row)
            jamaahState['{{ $row->IDPerson }}'] = {{ $row->IsPaid == 1 ? 'true' : 'false' }};
        @endforeach
    @endif

    // DataTable initialization
    const table = $('#datatable').DataTable({
        pageLength: 10,
        columnDefs: [
            { targets: 3, orderable: false } // 'Terbayar' column
        ]
    });

    // Sync state when rows are drawn.
    table.on('draw', function() {
        $('.check-item').each(function() {
            let id = $(this).data('id');
            $(this).prop('checked', jamaahState[id]);
        });
        updateCheckAllState();
    });

    // Handle individual checkbox change
    $('#datatable').on('change', '.check-item', function() {
        let id = $(this).data('id');
        let checked = $(this).is(':checked');
        jamaahState[id] = checked;
        updateCheckAllState();
    });

    // Handle Check All
    $('#check-all').on('click', function() {
        let checked = $(this).is(':checked');
        
        // Update state for ALL jamaah
        for (let id in jamaahState) {
            jamaahState[id] = checked;
        }

        // Update visible checkboxes
        $('.check-item').prop('checked', checked);
    });

    function updateCheckAllState() {
        let allChecked = true;
        let hasItems = false;
        for (let id in jamaahState) {
            hasItems = true;
            if (!jamaahState[id]) {
                allChecked = false;
                break;
            }
        }
        if (hasItems) {
            $('#check-all').prop('checked', allChecked);
        } else {
            $('#check-all').prop('checked', false);
        }
    }

    // Nama Jamaah Filter
    $('#filter-nama').on('keyup', function() {
        table.column(1).search(this.value).draw();
    });

    // Form Submission
    $("#form-default").submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('IDPaket', "{{ $IDPaket }}");

        // Append state for ALL jamaah
        for (let id in jamaahState) {
            formData.append(`IDPerson[${id}]`, id); // Needed for controller loop
            if (jamaahState[id]) {
                formData.append(`IsPaid[${id}]`, '1');
            }
        }

        $.ajax({
            url: `${baseURL}admin/Monitoring/setpaid`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            processData: false,
            contentType: false,
            data: formData,
            success: function(res) {
                if (res.status) {
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
