@extends('layouts.master')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-xl-12 stretch-card grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <form id="form-default" method="post">
                                <div class="form-group row">
                                    <div class="col-md-2 align-self-center">
                                        <label for="">Tanggal</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" readonly value="{{ date_indo(date('Y-m-d')) }}"
                                            class="form-control" placeholder="Masukkan Tanggal">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2 align-self-center">
                                        <label for="">Nama Jamaah</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" readonly value="{{ @$jamaah->NamaJamaah }}"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2 align-self-center">
                                        <label for="">Nama Paket</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" readonly value="{{ @$paket->NamaPaket }}"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2 align-self-center">
                                        <label for="">Sisa Tagihan</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text" readonly
                                            value="{{ formatCurrency($jamaah->InfoTagihan - $jamaah->InfoTerbayar) }}"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2 align-self-center">
                                        <label for="">Keterangan</label>
                                    </div>
                                    <div class="col-md-10">
                                        <textarea name="UraianTransaksi" required placeholder="Masukkan Keterangan Transaksi" rows="5"
                                            class="form-control"></textarea>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2 align-self-center">
                                        <label for="">Nominal pembayaran</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary rounded-lg text-white">Rp</span>
                                            </div>
                                            <input type="text" required name="Nominal"
                                                placeholder="Masukkan nominal pembayaran" value=""
                                                class="form-control nominal">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <input type="hidden" name="IDPerson" value="{{ @$jamaah->IDPerson }}">
                                    <input type="hidden" name="IDPaket" value="{{ @$jamaah->IDPaket }}">
                                    <a href="#" id="btn-cetak" target="_blank" class="btn btn-warning mr-2 d-none">
                                        <i class="mdi mdi-printer"></i> Cetak Nota
                                    </a>
                                    <a type="button" href="{{ url('admin/tagihan/Tagihantravel/entrybayarjamaah') }}"
                                        id="btn-cancel" class="btn btn-outline-primary mr-2">Batal</a>
                                    <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan</button>
                                    <a type="button" href="" class="btn btn-primary" id="btn-reload"
                                        style="display: none;">Catat Pengeluaran Baru</a>
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
            $('.nominal').on('keyup', function() {
                let nominal = $(this).val();
                $(this).val(format_nominal(nominal));
            });

            let e = $.Event('keyup');
            e.which = 65;
            $('.nominal').trigger(e);

            $("#form-default").submit(function(e) {
                e.preventDefault();
                $('#btn-cetak').addClass('d-none');
                console.log(`${baseURL}admin/tagihan/Tagihantravel/simpanpembayaranjamaah`);
                const formData = new FormData($("#form-default")[0]);
                $.ajax({
                    url: `${baseURL}admin/tagihan/Tagihantravel/simpanpembayaranjamaah`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(res) {
                        console.log(res);
                        if (res.status) {
                            if (res.print_url) {
                                $('#btn-cetak').attr('href', res.print_url).removeClass('d-none');
                            }
                            toast('success', res.message);
                        } else {
                            toast('error', res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        toast('error', 'Sistem Error');
                    }
                });
            });
        });
    </script>
@endpush
