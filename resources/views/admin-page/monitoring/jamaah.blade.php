@extends('layouts.master')

@section('content')

@push('css')
<!-- partial -->
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
                            {{-- Tabs --}}
                            <form action="" class="col-md-12 mb-3">
                                <input type="hidden" id="id" name="id" value="{{ base64_encode($IDPaket) }}">
                                @php
                                    $tabs = [
                                        'Detail' => 'Detil Paket',
                                        'Tracking' => 'Live Tracking',
                                        'Jamaah' => 'Jamaah',
                                        'Pembayaranjamaah' => 'Pembayaran',
                                        'Pembayaranharomain' => 'Haromain'
                                    ];
                                @endphp
                                @foreach($tabs as $key => $label)
                                    <button type="submit" value="{{ $key }}" name="type"
                                        class="btn btn-lg text-black-50 btn-tab {{ $type == $key ? 'tab-active' : '' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </form>

                            {{-- DataTable Jamaah --}}
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="datatable">
                                        <thead>
                                            <tr class="table-active">
                                                <th>No</th>
                                                <th>Nama Jamaah</th>
                                                <th>Dokumen Jamaah</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div> {{-- d-flex --}}
                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div> {{-- col-md-12 --}}
        </div> {{-- row --}}
    </div> {{-- content-wrapper --}}
</div> {{-- main-panel --}}

@endsection
@push('js')
<script>
    $(document).ready(function() {
        // DataTables
        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: "{{ url('admin/Monitoring/getJamaah?id=' . $IDPaket) }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            },
            columns: [
                { data: 'No', className: "text-center" },
                { data: 'NamaJamaah' },
                { data: null, render: function(data) {
                    let button = '';
                    if (data.Dokumen && data.Dokumen.length) {
                        data.Dokumen.forEach(el => {
                            button += `<a href="${baseURL}assets/dokumen_syarat/${el.FileDokumen}"
                                        title="Lihat Dokumen"
                                        class="btn btn-inverse-primary btn-sm mr-2">
                                        <i class="fa fa-file mr-2"></i> ${el.NamaSyarat}
                                      </a>`;
                        });
                    }
                    return button;
                }},
            ],
            language: {
                url: `${baseURL}assets/admin-page/datatable/id.json`,
            },
            columnDefs: [
                { orderable: false, targets: [0, 1, 2] }
            ],
        });
    });
</script>
@endpush
