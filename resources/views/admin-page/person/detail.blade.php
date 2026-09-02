@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h4 class="card-title">Data Jamaah</h4>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px">Nama Lengkap</th>
                                            <td>: {{ $jamaah->NamaJamaah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>: {{ $jamaah->NIK ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No Passport</th>
                                            <td>: {{ $jamaah->NoPasspor ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kelamin</th>
                                            <td>: {{ ($jamaah->JenisKelamin ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>: {{ $jamaah->NoHP ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px">Tempat, Tgl Lahir</th>
                                            <td>: {{ $jamaah->TempatLahir ?? '-' }}, {{ isset($jamaah) && $jamaah->TanggalLahir ? datetime_to_date($jamaah->TanggalLahir) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>: {{ $jamaah->Alamat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi</th>
                                            <td>: {{ $jamaah->NamaDesa ?? '-' }}, {{ $jamaah->NamaKec ?? '-' }}, {{ $jamaah->NamaKab ?? '-' }}, {{ $jamaah->NamaProv ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>: {{ $jamaah->Email ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="text-right mt-3">
                                <a href="{{ url('admin/Person') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h4 class="card-title">Daftar Paket</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 5%">No</th>
                                            <th>Nama Travel</th>
                                            <th>Nama Paket</th>
                                            <th>Tanggal Keberangkatan</th>
                                            <th>Tanggal Pulang</th>
                                            <th>Status Pembayaran</th>
                                            <th style="width: 10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($listPaket as $key => $paket)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>{{ $paket->NamaTravel }}</td>
                                                <td>{{ $paket->NamaPaket }}</td>
                                                <td>{{ $paket->TglBerangkat ? date('d-m-Y', strtotime($paket->TglBerangkat)) : '-' }}</td>
                                                <td>{{ $paket->TglPulang ? date('d-m-Y', strtotime($paket->TglPulang)) : '-' }}</td>
                                                <td>
                                                    @if($paket->IsPaid)
                                                        <span class="badge badge-success">Lunas</span>
                                                    @else
                                                        <span class="badge badge-warning">Belum Lunas</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-paket"
                                                        data-id="{{ $jamaah->IDPerson }}"
                                                        data-paket="{{ $paket->IDPaket }}"
                                                        data-travel="{{ $paket->IDTravel }}"
                                                        data-nama="{{ $paket->NamaPaket }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada paket yang diikuti</td>
                                            </tr>
                                        @endforelse
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

@push('js')
<script>
    $(document).ready(function() {
        $('.btn-hapus-paket').on('click', function() {
            let id = $(this).data('id');
            let paket = $(this).data('paket');
            let travel = $(this).data('travel');
            let nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus paket "${nama}" dari jamaah ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.person.hapus') }}",
                        type: 'GET',
                        data: { id: id, paket: paket, travel: travel },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
