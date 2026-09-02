@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-md-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">

                            <form id="form-default" method="post">
                                @csrf

                                <div class="d-flex justify-content-between flex-wrap">

                                    <div class="form-group col-md-6">
                                        <label>Cabang</label>
                                        <select name="IDCabang" class="form-control select2" required>
                                            <option value="">Pilih</option>
                                            @foreach ($cabang as $row)
                                                <option value="{{ $row->IDCabang }}">{{ $row->NamaCabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Uraian Transaksi</label>
                                        <textarea name="UraianTransaksi" class="form-control" rows="3" required>{{ old('UraianTransaksi') }}</textarea>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Kredit (Rp)</label>
                                        <input type="text" name="Kredit" class="form-control nominal"
                                            value="{{ old('Kredit') }}" required>
                                    </div>

                                </div>

                                <div class="text-right mt-3">
                                    <a href="#" id="btn-cetak" target="_blank" class="btn btn-warning mr-2 d-none">
                                        <i class="mdi mdi-printer"></i> Cetak Nota
                                    </a>
                                    <button type="button" onclick="history.back()"
                                        class="btn btn-outline-primary mr-2">Batal</button>
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
            $('#form-default').submit(function(e) {
                e.preventDefault();
                $('#btn-cetak').addClass('d-none');
                const formData = $(this).serialize();
                $.ajax({
                    url: `${baseURL}admin/Pengeluarankas/simpan`,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        res = typeof res === 'string' ? JSON.parse(res) : res;
                        if (res.status) {
                            if (res.print_url) {
                                $('#btn-cetak').attr('href', res.print_url).removeClass('d-none');
                            }
                            toast('success', res.message)
                        } else {
                            toast('error', res.message)
                        }
                    },
                    error: function() {
                        toast('error', 'Sistem Error')
                    }
                });
            });
        });
    </script>
@endpush
