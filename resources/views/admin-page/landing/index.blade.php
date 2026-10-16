@extends('layouts.master')

@section('content')
<div class="main-panel">
    <div class="content-wrapper pb-0">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Manajemen Landing</h4>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped border w-100 align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center font-weight-bold">No</th>
                                        <th style="width: 30%;" class="font-weight-bold">Nama Section</th>
                                        <th style="width: 50%;" class="font-weight-bold">Preview Ringkas</th>
                                        <th style="width: 15%;" class="text-center font-weight-bold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sections as $sec)
                                        <tr>
                                            <td class="text-center font-weight-bold">{{ $sec['no'] }}</td>
                                            <td>
                                                <div class="font-weight-bold text-dark">{{ $sec['name'] }}</div>
                                                <small class="text-muted">{{ $sec['description'] }}</small>
                                            </td>
                                            <td>
                                                <div class="text-secondary font-weight-medium">
                                                    {{ $sec['preview'] }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ $sec['edit_url'] }}" class="btn btn-primary btn-sm px-3 shadow-sm font-weight-bold">
                                                    <i class="fa fa-edit mr-1"></i> Edit Section
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
