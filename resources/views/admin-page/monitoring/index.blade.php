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
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap">
                            {{-- Tabs --}}
                            <form action="" class="col-md-12 mb-3">
                                <input type="hidden" id="trv" name="trv">
                                @php
                                    $tabs = ['Persiapan', 'Perjalanan', 'History'];
                                @endphp
                                @foreach($tabs as $tab)
                                    <button type="submit" name="type" value="{{ $tab }}"
                                        class="btn btn-lg text-black-50 btn-tab {{ $type == $tab ? 'tab-active' : '' }}">
                                        {{ $tab }}
                                    </button>
                                @endforeach
                            </form>

                            {{-- Filter Travel --}}
                            <div class="col-md-5">
                                <select name="IDTravel" id="IDTravel" class="form-control select2">
                                    <option value="">Pilih Travel</option>
                                    @foreach($travel as $row)
                                        <option value="{{ $row->IDTravel }}" {{ $row->IDTravel == $IDTravel ? 'selected' : '' }}>
                                            {{ $row->NamaTravel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search Form --}}
                            <div class="col-md-7">
                                <form id="form-search" method="get" class="w-100">
                                    <div class="input-search d-flex align-items-center px-3">
                                        <i class="fa fa-search text-gray mr-2"></i>
                                        <input type="text" name="" class="input-text" id="search-keyword" placeholder="Cari Data">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </form>
                            </div>

                            {{-- DataTable --}}
                            <div class="table-responsive mt-4 w-100">
                                <table id="datatable" class="table w-100">
                                    <thead class="bg-lightgray">
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Paket</th>
                                            <th>Kategori Paket</th>
                                            <th>Keberangkatan</th>
                                            <th>Kepulangan</th>
                                            <th>Nama Travel</th>
                                            <th>Jumlah Jamaah</th>
                                            <th>Paket Tracking</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
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
        const type = "{{ $type }}";

        //datatables
        const table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: `${baseURL}admin/Monitoring/get?type=${type}`,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            },
            columns: [
                { data: 'No', orderable: false, searchable: false, className: "text-center" },
                { data: 'NamaPaket' },
                { data: 'KategoriPaket' },
                { data: null, render: function(data) { return format_date_ymd(data.TglBerangkat); } },
                { data: null, render: function(data) { return format_date_ymd(data.TglPulang); } },
                { data: 'NamaTravel' },
                { data: 'JumlahJamaah', orderable: false, searchable: false },
                { data: 'JumlahTracking', orderable: false, searchable: false },
                { data: null, orderable: false, searchable: false,
                    render: function(data) {
                        return `<a class="btn btn-inverse-info btn-edit p-2 mr-2" href="${baseURL}admin/Monitoring/show?id=${btoa(data.IDPaket)}">
                                    <i class="fa fa-eye text-behance"></i>
                                </a>`;
                    }
                }
            ],
            language: {
                url: `${baseURL}assets/admin-page/datatable/id.json`
            },
            fnDrawCallback: function() {
                $('.bs-toggle').bootstrapToggle();
            }
        });

        // Search
        $('#form-search').on('submit', function(e) {
            e.preventDefault();
            table.search($('#search-keyword').val()).draw();
        });

        // Filter travel
        $("#IDTravel").on('change', function() {
            let trv = $(this).val();
            let url = `${baseURL}admin/Monitoring/get?type=${type}&trv=${trv}`;
            table.ajax.url(url).load();
        });
    });
</script>
@endpush
